<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

// Cotação para lead: lê o tarifário da planilha Campeche Automation (via Apps Script), calcula noite a noite
// e monta a mensagem. Regras (aba "configuracao"): valores em centavos; período = início inclusivo, fim exclusivo;
// taxa de limpeza única, embutida no total; criança 0–12 usa a tarifa de 2 adultos; 13–15 fica como criança;
// 16+ vira adulto; soma das idades (13+) acima do limite aplica acréscimo interno, nunca mostrado ao lead.

const COLUNAS_COMPOSICAO = ['1-0' => '1 adulto', '2-0' => '2 adultos', '3-0' => '3 adultos', '2-1' => '2 adultos + 1 criança', '2-2' => '2 adultos + 2 crianças'];

// ---------- tarifário (cache de 10 min; se o Google falhar, usa a última cópia) ----------
function tarifario_carregar(bool $forcar = false): array {
    $c = json_ler('tarifario');
    if (!$forcar && !empty($c['tarifario']) && time() - (int) ($c['buscado_em'] ?? 0) < 600) return $c;
    $r = apps_script(['acao' => 'tarifario']);
    if (!empty($r['ok']) && !empty($r['tarifario'])) {
        $novo = ['buscado_em' => time(), 'tarifario' => $r['tarifario'], 'configuracao' => $r['configuracao'] ?? []];
        json_atualizar('tarifario', fn() => $novo);
        return $novo;
    }
    if (!empty($c['tarifario'])) return $c + ['aviso' => 'Não consegui ler a planilha agora; usei a cópia de ' . date('d/m H:i', (int) $c['buscado_em']) . '.'];
    throw new RuntimeException('Não consegui ler o tarifário (' . ($r['erro'] ?? 'Apps Script não configurado') . '). Confira se a planilha Campeche Automation está compartilhada com lsf.loft@gmail.com e se o Apps Script foi atualizado.');
}

function data_flex(string $s): ?DateTimeImmutable {
    foreach (['!Y-m-d', '!d/m/Y'] as $f) { $d = DateTimeImmutable::createFromFormat($f, trim($s)); if ($d) return $d; }
    return null;
}

/** Transforma as abas em ['periodos' => [...], 'cfg' => [chave => valor]]. */
function tarifario_interpretar(array $dados): array {
    $linhas = $dados['tarifario'];
    $cab = array_map('trim', array_shift($linhas));
    $col = array_flip($cab);
    $periodos = [];
    foreach ($linhas as $l) {
        $ini = data_flex((string) ($l[$col['date_start']] ?? '')); $fim = data_flex((string) ($l[$col['date_end']] ?? ''));
        if (!$ini || !$fim) continue;
        $precos = [];
        foreach (COLUNAS_COMPOSICAO as $nome) if (isset($col[$nome])) $precos[$nome] = (int) preg_replace('/\D/', '', (string) ($l[$col[$nome]] ?? '0'));
        $periodos[] = ['nome' => (string) $l[0], 'ini' => $ini, 'fim' => $fim, 'precos' => $precos];
    }
    $cfg = [];
    foreach ($dados['configuracao'] ?? [] as $l) if (isset($l[0], $l[1])) $cfg[trim((string) $l[0])] = trim((string) $l[1]);
    return ['periodos' => $periodos, 'cfg' => $cfg];
}

// ---------- cálculo ----------
/** @param int[] $idades idades das crianças. Lança RuntimeException (mensagem para o Leonardo) se não der para cotar. */
function cotacao_calcular(array $t, DateTimeImmutable $ci, DateTimeImmutable $co, int $adultos, array $idades): array {
    $noites = (int) $ci->diff($co)->format('%r%a');
    if ($noites <= 0) throw new RuntimeException('O check-out precisa ser depois do check-in.');
    if ($adultos < 1) throw new RuntimeException('Informe pelo menos 1 adulto.');

    $adultosEf = $adultos + count(array_filter($idades, fn($i) => $i >= 16));
    $teens = array_values(array_filter($idades, fn($i) => $i >= 13 && $i <= 15));
    $coluna = COLUNAS_COMPOSICAO["$adultosEf-" . count($teens)] ?? null;
    if (!$coluna) throw new RuntimeException("Composição sem tarifa na tabela ($adultosEf adulto(s) + " . count($teens) . ' criança(s) de 13 a 15): cote à mão ou divida em mais de um loft.');

    $linhas = [];
    $soma = 0;
    for ($d = $ci; $d < $co; $d = $d->modify('+1 day')) {
        $p = null;
        foreach ($t['periodos'] as $x) if ($d >= $x['ini'] && $d < $x['fim']) { $p = $x; break; }
        if (!$p) throw new RuntimeException('A noite de ' . $d->format('d/m/Y') . ' está fora do tarifário. Peça ao Leonardo para atualizar a planilha.');
        $v = $p['precos'][$coluna] ?? 0;
        if ($v <= 0) throw new RuntimeException("Sem preço para \"$coluna\" em {$p['nome']}.");
        $k = $p['nome'] . '|' . $v;
        $linhas[$k] = ['periodo' => $p['nome'], 'noites' => ($linhas[$k]['noites'] ?? 0) + 1, 'diaria' => $v];
        $soma += $v;
    }

    $somaIdades = array_sum(array_filter($idades, fn($i) => $i >= 13));  // 0–12: "sem adicional" (regra da planilha)
    $limite = (int) ($t['cfg']['child_age_sum_surcharge_threshold'] ?? 18);
    $pct = (int) ($t['cfg']['child_age_sum_surcharge_percent'] ?? 20);
    $acrescimo = $somaIdades > $limite ? (int) round($soma * $pct / 100) : 0;
    $limpeza = (int) ($t['cfg']['cleaning_fee_cents'] ?? 0);

    return ['noites' => $noites, 'coluna' => $coluna, 'linhas' => array_values($linhas), 'diarias' => $soma, 'acrescimo' => $acrescimo,
        'acrescimo_pct' => $pct, 'soma_idades' => $somaIdades, 'limpeza' => $limpeza, 'total' => $soma + $acrescimo + $limpeza];
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
function composicao_texto(string $idioma, int $adultos, array $idades): string {
    $n = count($idades);
    $t = [
        'pt' => ['a' => ['adulto', 'adultos'], 'c' => ['criança', 'crianças'], 'e' => ' e '],
        'es' => ['a' => ['adulto', 'adultos'], 'c' => ['niño', 'niños'], 'e' => ' y '],
        'en' => ['a' => ['adult', 'adults'], 'c' => ['child', 'children'], 'e' => ' and '],
    ][$idioma];
    $s = "$adultos " . $t['a'][$adultos === 1 ? 0 : 1];
    return $n ? $s . $t['e'] . "$n " . $t['c'][$n === 1 ? 0 : 1] : $s;
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
