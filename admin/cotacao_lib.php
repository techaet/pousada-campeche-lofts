<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

// Cotação para lead: o PREÇO vem do robô (workflow CNST-04 do n8n, por um webhook), nunca calculado aqui — assim painel e robô
// sempre dão o mesmo valor, com as mesmas regras (bebês, vários lofts, descontos, taxa de limpeza). Este arquivo só chama o robô,
// descobre telefone/idioma e monta a mensagem no modelo do Leonardo.

function data_flex(string $s): ?DateTimeImmutable {
    foreach (['!Y-m-d', '!d/m/Y'] as $f) { $d = DateTimeImmutable::createFromFormat($f, trim($s)); if ($d) return $d; }
    return null;
}

/** Pede a cotação ao robô. Lança RuntimeException (mensagem para o Leonardo) se não der. */
function cotacao_robo(DateTimeImmutable $ci, DateTimeImmutable $co, int $adultos, array $idades): array {
    $url = (string) cfg('n8n_cotacao_url', '');
    if ($url === '') throw new RuntimeException('Ligação com o robô não configurada (Configurações → Cotação pelo robô).');
    if ($co <= $ci) throw new RuntimeException('O check-out precisa ser depois do check-in.');
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 45,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-Painel-Secret: ' . cfg('n8n_cotacao_segredo', '')],
        CURLOPT_POSTFIELDS => json_encode(['quote_input' => ['check_in' => $ci->format('Y-m-d'), 'check_out' => $co->format('Y-m-d'),
            'adults' => $adultos, 'children' => count($idades), 'child_ages' => $idades, 'loft_count' => null]])]);
    $corpo = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $q = json_decode((string) $corpo, true);
    if ($http === 401 || $http === 403) throw new RuntimeException('O robô recusou o segredo. Confira o segredo em Configurações.');
    if ($http !== 200 || !is_array($q)) throw new RuntimeException("O robô não respondeu à cotação (HTTP $http). Tente de novo ou cote pelo robô.");
    if (($q['status'] ?? '') !== 'computed') {
        $motivo = (string) ($q['internal_reason'] ?? $q['status'] ?? 'sem motivo');
        throw new RuntimeException(match (true) {
            str_contains($motivo, 'CHILD_AGES') => 'Faltam as idades das crianças.',
            default => "O robô não conseguiu cotar: $motivo.",
        });
    }
    $linhas = [];
    foreach ((array) json_decode((string) ($q['nightly_breakdown_json'] ?? '[]'), true) as $n) {
        $k = ($n['period'] ?? '') . '|' . ($n['daily_rate_cents'] ?? 0);
        $linhas[$k] = ['periodo' => $n['period'] ?? '', 'noites' => ($linhas[$k]['noites'] ?? 0) + 1, 'diaria' => (int) ($n['daily_rate_cents'] ?? 0)];
    }
    return ['noites' => (int) $q['nights'], 'total' => (int) $q['total_cents'], 'lofts' => max(1, (int) ($q['loft_count'] ?? 1)),
        'tarifa' => (string) ($q['tariff_key'] ?? ''), 'linhas' => array_values($linhas), 'limpeza' => (int) ($q['cleaning_fee_cents'] ?? 0),
        'acrescimo' => (int) ($q['child_surcharge_cents'] ?? 0)];
}

function reais(int $centavos): string { return 'R$ ' . number_format($centavos / 100, 2, ',', '.'); }

// ---------- telefone e idioma ----------
/** Só dígitos com DDI. Sem "+" e com 10–11 dígitos assume Brasil (55). Argentina: 54 + 10 dígitos ganha o 9 do celular. */
function telefone_normalizar(string $raw): ?string {
    $d = preg_replace('/\D/', '', $raw);
    if (str_starts_with($d, '00')) $d = substr($d, 2);
    if ($d === '') return null;
    if (!str_contains($raw, '+') && in_array(strlen($d), [10, 11], true)) $d = '55' . $d;
    if (str_starts_with($d, '54') && strlen($d) === 12) $d = '549' . substr($d, 2);
    return strlen($d) >= 11 && strlen($d) <= 15 ? $d : null;
}

function idioma_do_ddi(?string $digitos): ?string {
    if (!$digitos) return null;
    if (str_starts_with($digitos, '55')) return 'pt';
    foreach (['54', '56', '57', '51', '52', '53', '58', '34', '591', '593', '595', '598', '502', '503', '504', '505', '506', '507'] as $p) {
        if (str_starts_with($digitos, $p)) return 'es';
    }
    return 'en';
}

// ---------- mensagem ----------
function composicao_texto(string $idioma, int $adultos, array $idades, int $lofts = 1): string {
    $n = count($idades);
    $t = [
        'pt' => ['a' => ['adulto', 'adultos'], 'c' => ['criança', 'crianças'], 'e' => ' e '],
        'es' => ['a' => ['adulto', 'adultos'], 'c' => ['niño', 'niños'], 'e' => ' y '],
        'en' => ['a' => ['adult', 'adults'], 'c' => ['child', 'children'], 'e' => ' and '],
    ][$idioma];
    $s = "$adultos " . $t['a'][$adultos === 1 ? 0 : 1];
    $s = $n ? $s . $t['e'] . "$n " . $t['c'][$n === 1 ? 0 : 1] : $s;
    return $lofts > 1 ? "$s ($lofts lofts)" : $s;
}

/** Modelo aprovado pelo Leonardo, sem a apresentação da Constância. */
function cotacao_mensagem(string $idioma, string $nome, int $noites, DateTimeImmutable $ci, DateTimeImmutable $co, string $composicao, int $total): string {
    $v = ['{nome}' => trim($nome), '{noites}' => (string) $noites, '{ci}' => $ci->format('d/m/Y'), '{co}' => $co->format('d/m/Y'),
        '{comp}' => $composicao, '{total}' => reais($total)];
    $modelos = [
        'es' => "¡Hola{, nome}! Gracias por contactarnos.\n\nPerfecto. Para {noites} noche(s), del {ci} al {co}, para {comp}:\n\nTotal: {total}\nComposición: {comp}.\nReservas con 50% vía PIX y el saldo hasta el día del check-in.\n\nEstos precios son para las primeras reservas de la temporada. ¡No pierdas esta oportunidad!\n¡Somos Superhost en Airbnb — vas a guardar excelentes recuerdos de estas vacaciones!\n\n🏆 Campeche Lofts es Airbnb SUPERHOST 5⭐\n16 años hospedando con calidad!\n\nNuestra pousada está muy cerca del mar, en la Praia do Campeche, en Florianópolis/SC. Nuestros lofts tienen aire acondicionado, excelente señal de internet, cocina completa y el coche queda estacionado en el patio de la pousada.\n📸 Fotos: https://www.campechelofts.floripa.br\n\nSi estás de acuerdo con el presupuesto y quieres finalizar la reserva, comunícate directamente con el propietario de Pousada Campeche Lofts. 🏡\nLeonardo te atenderá personalmente por WhatsApp (+5548991223600). 💬\n👉 Haz clic en este enlace: https://tinyurl.com/223o8sbv",
        'pt' => "Olá{, nome}! Obrigado por entrar em contato.\n\nPerfeito. Para {noites} noite(s), de {ci} a {co}, para {comp}:\n\nTotal: {total}\nComposição: {comp}.\nReservas com 50% via PIX e o saldo até o dia do check-in.\n\nEstes preços são para as primeiras reservas da temporada. Não perca esta oportunidade!\nSomos Superhost no Airbnb — você vai guardar ótimas lembranças destas férias!\n\n🏆 Campeche Lofts é Airbnb SUPERHOST 5⭐\n16 anos hospedando com qualidade!\n\nNossa pousada fica pertinho do mar, na Praia do Campeche, em Florianópolis/SC. Nossos lofts têm ar-condicionado, excelente sinal de internet, cozinha completa e o carro fica estacionado no pátio da pousada.\n📸 Fotos: https://www.campechelofts.floripa.br\n\nSe você concorda com o orçamento e quer finalizar a reserva, fale diretamente com o proprietário da Pousada Campeche Lofts. 🏡\nO Leonardo vai te atender pessoalmente pelo WhatsApp (+5548991223600). 💬\n👉 Clique neste link: https://tinyurl.com/223o8sbv",
        'en' => "Hello{, nome}! Thank you for contacting us.\n\nPerfect. For {noites} night(s), from {ci} to {co}, for {comp}:\n\nTotal: {total}\nGuests: {comp}.\nBookings with 50% via PIX and the balance by check-in day.\n\nThese prices are for the first bookings of the season. Don't miss this opportunity!\nWe are Airbnb Superhosts — you will take home great memories from this holiday!\n\n🏆 Campeche Lofts is an Airbnb SUPERHOST 5⭐\n16 years of hosting with quality!\n\nOur pousada is very close to the sea, at Praia do Campeche, in Florianópolis/SC. Our lofts have air conditioning, excellent internet, a full kitchen, and your car stays parked in the pousada's yard.\n📸 Photos: https://www.campechelofts.floripa.br\n\nIf you agree with the quote and want to complete your booking, please contact the owner of Pousada Campeche Lofts directly. 🏡\nLeonardo will assist you personally on WhatsApp (+5548991223600). 💬\n👉 Click this link: https://tinyurl.com/223o8sbv",
    ];
    $m = $modelos[$idioma];
    $m = str_replace('{, nome}', $v['{nome}'] !== '' ? ', ' . $v['{nome}'] : '', $m);
    return strtr($m, $v);
}
