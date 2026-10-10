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
    curl_close($ch);
    if ($http === 401 || $http === 403) throw new RuntimeException('O robô recusou o segredo. Confira o segredo em Configurações.');
    $r = json_decode((string) $corpo, true);
    if ($http !== 200 || !is_array($r)) throw new RuntimeException("O robô não respondeu (HTTP $http). Tente de novo em instantes.");
    if (empty($r['ok'])) throw new RuntimeException('O robô não conseguiu responder: ' . (($r['error'] ?? '') ?: 'sem motivo') . '.');
    return ['text' => (string) $r['text'], 'decision' => (string) ($r['decision'] ?? ''), 'reason' => (string) ($r['reason'] ?? '')];
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
