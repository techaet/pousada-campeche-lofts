<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

// Cotação para lead: o texto do lead vai ao robô (workflow CNST-16 do n8n, que roda o CNST-02B com um lead de mentira e apaga o que
// ele gravou) e o robô devolve a resposta pronta, no mesmo texto e nas mesmas regras que usa com os leads do WhatsApp.
// Aqui não há preço, idioma nem modelo de mensagem: só chamar o robô e achar o WhatsApp do lead para o link.

const COTACAO_URL_PADRAO = 'https://tech-aetsolidez.app.n8n.cloud/webhook/painel-cotacao-texto';

/** Manda o texto do lead ao robô. Devolve ['text' => resposta, 'decision' => ..., 'reason' => ...]. Lança RuntimeException se não der. */
function cotacao_pedir_ao_robo(string $texto): array {
    $segredo = (string) cfg('n8n_cotacao_segredo', '');
    if ($segredo === '') throw new RuntimeException('Ligação com o robô não configurada (Configurações → Cotação pelo robô).');
    $ch = curl_init((string) (cfg('n8n_texto_url') ?: COTACAO_URL_PADRAO));
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 100,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'X-Painel-Secret: ' . $segredo],
        CURLOPT_POSTFIELDS => json_encode(['text' => mb_substr($texto, 0, 4000)])]);
    $corpo = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http === 401 || $http === 403) throw new RuntimeException('O robô recusou o segredo. Confira o segredo em Configurações.');
    $r = json_decode((string) $corpo, true);
    if ($http !== 200 || !is_array($r)) throw new RuntimeException("O robô não respondeu (HTTP $http). Tente de novo em instantes.");
    if (empty($r['ok'])) throw new RuntimeException('O robô não conseguiu responder: ' . (($r['error'] ?? '') ?: 'sem motivo') . '.');
    [$texto2, $completou] = cotacao_completar((string) $r['text']);
    return ['text' => $texto2, 'decision' => (string) ($r['decision'] ?? ''), 'reason' => (string) ($r['reason'] ?? ''), 'completou' => $completou];
}

const BLOCO_COMERCIAL = [
    'es' => ['promo' => 'Estos precios son para las primeras reservas de la temporada. ¡No pierdas esta oportunidad!',
        'superhost' => '¡Somos Superhost en Airbnb — vas a guardar excelentes recuerdos de estas vacaciones!',
        'pousada' => "🏆 Campeche Lofts es Airbnb SUPERHOST 5⭐\n16 años hospedando con calidad!\n\nNuestra pousada está muy cerca del mar, en la Praia do Campeche, en Florianópolis/SC. Nuestros lofts tienen aire acondicionado, excelente señal de internet, cocina completa y el coche queda estacionado en el patio de la pousada.\n📸 Fotos: https://www.campechelofts.floripa.br",
        'cta' => "Si estás de acuerdo con el presupuesto y quieres finalizar la reserva, comunícate directamente con el propietario de Pousada Campeche Lofts. 🏡\nLeonardo te atenderá personalmente por WhatsApp (+5548991223600). 💬\n👉 Haz clic en este enlace: "],
    'pt' => ['promo' => 'Esses preços são para as primeiras reservas da temporada. Não perca essa oportunidade!',
        'superhost' => 'Somos Superhost no Airbnb — você vai guardar ótimas lembranças dessas férias!',
        'pousada' => "🏆 Campeche Lofts é Airbnb SUPERHOST 5⭐\n16 anos hospedando com qualidade!\n\nNossa pousada fica bem pertinho do mar, na Praia do Campeche, em Florianópolis/SC. Nossos lofts têm ar-condicionado, ótimo sinal de internet, cozinha completa e o carro fica estacionado no pátio da pousada.\n📸 Fotos: https://www.campechelofts.floripa.br",
        'cta' => "Caso você esteja de acordo com o orçamento e queira finalizar a reserva, entre em contato diretamente com o proprietário da Pousada Campeche Lofts. 🏡\nO Leonardo irá atender você pessoalmente pelo WhatsApp (+5548991223600). 💬\n👉 Clique neste link: "],
    'en' => ['promo' => "These prices are for the first bookings of the season. Don't miss this opportunity!",
        'superhost' => 'We are Airbnb Superhosts — you will have wonderful memories of this vacation!',
        'pousada' => "🏆 Campeche Lofts is an Airbnb SUPERHOST 5⭐\n16 years hosting with quality!\n\nOur pousada is very close to the sea, at Campeche Beach in Florianópolis, Brazil. Our lofts have air conditioning, excellent internet signal, a fully equipped kitchen, and the car is parked in the pousada courtyard.\n📸 Photos: https://www.campechelofts.floripa.br",
        'cta' => "If you agree with the quote and would like to finalize your booking, please contact the owner of Pousada Campeche Lofts directly. 🏡\nLeonardo will assist you personally on WhatsApp (+5548991223600). 💬\n👉 Click this link: "],
];

/** Link para o Leonardo (WhatsApp) com a mensagem "quero finalizar a reserva", encurtado como o robô faz; se o encurtador falhar, vale o longo. */
function cta_link(): string {
    $longo = 'https://wa.me/5548991223600?text=' . rawurlencode('Olá Leonardo! Recebi um orçamento da Pousada Campeche Lofts e quero finalizar a reserva.');
    $c = json_ler('cta_link');
    if (!empty($c['curto']) && ($c['longo'] ?? '') === $longo) return $c['curto'];
    $ch = curl_init('https://tinyurl.com/api-create.php?url=' . rawurlencode($longo));
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8]);
    $curto = trim((string) curl_exec($ch));
    if (!preg_match('#^https://tinyurl\.com/\w+$#', $curto)) return $longo;
    json_atualizar('cta_link', fn() => ['longo' => $longo, 'curto' => $curto]);
    return $curto;
}

/** O robô não põe promoção, Superhost, descrição da pousada e link do Leonardo nas respostas de ficha só com o mês (decisão dele: sem valor fechado, sem CTA).
 *  Para o uso do Leonardo, que envia à mão, o painel acrescenta esse bloco (no idioma da resposta, antes da assinatura) quando ele não veio. */
function cotacao_completar(string $texto): array {
    if (stripos($texto, 'SUPERHOST') !== false) return [$texto, false];
    $idioma = preg_match('/^\s*(¡?Hola|Gracias|Perfecto)\b/iu', $texto) || preg_match('/\b(presupuesto|noche|fechas|contame|decime)\b/iu', $texto) ? 'es'
        : (preg_match('/^\s*(Ol[aá]|Obrigad)/iu', $texto) || preg_match('/\b(orçamento|noites?|datas)\b/iu', $texto) ? 'pt' : 'en');
    $b = BLOCO_COMERCIAL[$idioma];
    $assinatura = '';
    if (preg_match('/\n+\s*Constância\s*$/u', $texto, $m)) { $assinatura = $m[0]; $texto = substr($texto, 0, -strlen($m[0])); }
    $partes = [rtrim($texto)];
    if (str_contains($texto, 'R$')) $partes[] = $b['promo'] . "\n" . $b['superhost'];  // "estes preços" só faz sentido se há preço na resposta
    $partes[] = $b['pousada'];
    $partes[] = $b['cta'] . cta_link();
    return [implode("\n\n", $partes) . ($assinatura !== '' ? "\n\nConstância" : ''), true];
}

/** Só dígitos com DDI. Sem "+" e com 10–11 dígitos assume Brasil (55). Argentina: 54 + 10 dígitos ganha o 9 do celular. */
function telefone_normalizar(string $raw): ?string {
    $d = preg_replace('/\D/', '', $raw);
    if (str_starts_with($d, '00')) $d = substr($d, 2);
    if ($d === '') return null;
    if (!str_contains($raw, '+') && in_array(strlen($d), [10, 11], true)) $d = '55' . $d;
    if (str_starts_with($d, '54') && strlen($d) === 12) $d = '549' . substr($d, 2);
    return strlen($d) >= 11 && strlen($d) <= 15 ? $d : null;
}

/** Acha o WhatsApp do lead no texto colado (ficha do anúncio ou conversa). Prefere o que vem depois de "Phone number/Teléfono/Telefone". */
function telefone_do_texto(string $texto): ?string {
    $padroes = [
        '/(?:phone number|tel[eé]fono|telefone|whats\s?app|celular)\s*:?\s*(\+?\d[\d\s().\-]{8,18}\d)/iu',
        '/(\+\d[\d\s().\-]{8,18}\d)/u',
        '/(?<!\d)\(?(\d{2})\)?\s*(9?\d{4})[\s\-]?(\d{4})(?!\d)/u',  // brasileiro sem DDI: (48) 99122-3600
    ];
    foreach ($padroes as $p) {
        if (preg_match($p, $texto, $m) && ($f = telefone_normalizar(implode('', array_slice($m, 1))))) return $f;
    }
    return null;
}

const IDIOMAS_TRADUCAO = [
    'es-AR' => ['Español (Argentina, com "vos")', 'español rioplatense de Argentina, con voseo (vos, querés, contame)'],
    'es' => ['Español (neutro)', 'español neutro (tú)'],
    'pt-BR' => ['Português (Brasil)', 'português do Brasil'],
    'en' => ['English', 'English'],
    'fr' => ['Français', 'français'],
    'it' => ['Italiano', 'italiano'],
    'de' => ['Deutsch', 'Deutsch'],
];

/** Traduz um texto (qualquer idioma de origem) para o idioma escolhido, com o tom cordial da pousada. */
function traduzir_texto(string $texto, string $idioma): string {
    if (!isset(IDIOMAS_TRADUCAO[$idioma])) throw new RuntimeException('Idioma não suportado.');
    $destino = IDIOMAS_TRADUCAO[$idioma][1];
    $j = groq_json("Você é tradutor profissional da Pousada Campeche Lofts (Florianópolis, Brasil). Traduza o texto do usuário para $destino. "
        . "Detecte o idioma de origem sozinho. Mantenha o sentido, o tom cordial e natural, as quebras de linha, os emojis, os números, valores em R\$, datas, links e nomes próprios exatamente como estão. "
        . "Não explique, não comente e não acrescente nada. Se o texto já estiver no idioma pedido, devolva-o corrigido, sem mudar o sentido. "
        . 'Responda SOMENTE um objeto JSON com uma chave: {"traducao": "texto traduzido"}.', mb_substr($texto, 0, 4000));
    $t = trim((string) ($j['traducao'] ?? ''));
    if ($t === '') throw new RuntimeException('A IA não devolveu a tradução. Tente de novo.');
    return $t;
}
