<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

// Comprovante de reserva: lê a conversa (Groq), valida, gera o PDF (dompdf), guarda e envia (Apps Script: Gmail + Drive).
// Mesma lógica do scripts/comprovante.py, que continua servindo de referência.

const MESES = ['pt' => 'janeiro fevereiro março abril maio junho julho agosto setembro outubro novembro dezembro',
    'es' => 'enero febrero marzo abril mayo junio julio agosto septiembre octubre noviembre diciembre'];
const TEXTOS = [
    'pt' => ['lang' => 'pt-BR', 'eyebrow' => 'Documento de hospedagem', 'titulo' => 'Comprovante de reserva',
        'confirmada' => 'Reserva confirmada', 'sec_hospede' => 'Hóspede responsável', 'l_nome' => 'Nome completo',
        'l_doc' => 'CPF', 'l_acomodacao' => 'Acomodação', 'l_hospedes' => 'Hóspedes', 'sec_estadia' => 'Detalhes da estadia',
        'l_duracao' => 'Duração', 'l_endereco' => 'Endereço', 'sec_recibo' => 'Recibo de pagamento', 'l_total' => 'Valor total',
        'l_sinal' => 'Sinal recebido', 'l_saldo' => 'Saldo no check-in', 'sec_recebido' => 'Recebido por',
        'socio' => 'Sócio-proprietário', 'rodape' => 'Campeche Lofts · Hospedagem tranquila em Florianópolis',
        'intro' => 'Este documento confirma a reserva do Loft {loft} e registra o recebimento do sinal informado para a estadia no Campeche Lofts.',
        'recibo' => 'Recebemos o valor de {sinal} referente ao sinal{pct} da reserva. O saldo remanescente deverá ser pago no check-in.',
        'quitado' => 'Recebemos o valor de {sinal} referente ao pagamento integral da reserva. Não há saldo a pagar no check-in.',
        'emitido' => 'Documento emitido em <strong>{data}</strong>.', 'noite' => 'noite', 'noites' => 'noites',
        'assunto' => 'Comprovante de reserva {numero} · Campeche Lofts',
        'email' => "Olá, {nome}!\n\nSegue em anexo o comprovante da sua reserva no Campeche Lofts (Loft {loft}, de {checkin} a {checkout}).\n\nSe algo estiver diferente do combinado, é só responder este e-mail.\n\nUm abraço,\nCampeche Lofts\nRua das Corticeiras, 270 · Campeche · Florianópolis/SC"],
    'es' => ['lang' => 'es-AR', 'eyebrow' => 'Documento de alojamiento', 'titulo' => 'Comprobante de reserva',
        'confirmada' => 'Reserva confirmada', 'sec_hospede' => 'Huésped titular', 'l_nome' => 'Nombre completo',
        'l_doc' => 'DNI / Pasaporte', 'l_acomodacao' => 'Alojamiento', 'l_hospedes' => 'Huéspedes', 'sec_estadia' => 'Detalles de la estadía',
        'l_duracao' => 'Duración', 'l_endereco' => 'Dirección', 'sec_recibo' => 'Recibo de pago', 'l_total' => 'Valor total',
        'l_sinal' => 'Seña recibida', 'l_saldo' => 'Saldo en el check-in', 'sec_recebido' => 'Recibido por',
        'socio' => 'Socio propietario', 'rodape' => 'Campeche Lofts · Alojamiento tranquilo en Florianópolis',
        'intro' => 'Este documento confirma la reserva del Loft {loft} y registra el pago de la seña para la estadía en Campeche Lofts.',
        'recibo' => 'Recibimos el valor de {sinal} correspondiente a la seña{pct} de la reserva. El saldo restante se abona en el check-in.',
        'quitado' => 'Recibimos el valor de {sinal} correspondiente al pago total de la reserva. No hay saldo a pagar en el check-in.',
        'emitido' => 'Documento emitido el <strong>{data}</strong>.', 'noite' => 'noche', 'noites' => 'noches',
        'assunto' => 'Comprobante de reserva {numero} · Campeche Lofts',
        'email' => "¡Hola, {nome}!\n\nTe enviamos adjunto el comprobante de tu reserva en Campeche Lofts (Loft {loft}, del {checkin} al {checkout}).\n\nSi algo no coincide con lo acordado, respondé este e-mail.\n\nUn abrazo,\nCampeche Lofts\nRua das Corticeiras, 270 · Campeche · Florianópolis/SC"],
];

function data_extenso(DateTimeImmutable $d, string $idioma): string {
    return $d->format('j') . ' de ' . explode(' ', MESES[$idioma])[(int) $d->format('n') - 1] . ' de ' . $d->format('Y');
}

function slug(string $s): string {
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    return trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($s)), '_') ?: 'hospede';
}

// ---------- leitura da conversa (Groq) ----------
function groq_extrair(string $conversa): array {
    $chave = (string) cfg('groq_key', '');
    if ($chave === '') throw new RuntimeException('Chave do Groq não configurada (Configurações).');
    $hoje = date('Y-m-d');
    $sistema = "Você extrai dados de reserva de uma conversa de WhatsApp de uma pousada em Florianópolis. Hoje é $hoje.\n"
        . "Responda SOMENTE um objeto JSON com estas chaves: nome (nome completo do hóspede responsável), documento (CPF, DNI ou passaporte, como escrito), "
        . "email, idioma ('pt' se o hóspede é brasileiro/escreve em português, 'es' se é argentino/escreve em espanhol), loft (inteiro de 3 a 9), "
        . "hospedes (texto curto, ex.: '2 adultos e 1 criança' / '2 adultos y 1 niño', no idioma do hóspede), checkin e checkout (AAAA-MM-DD), "
        . "total (número em reais: valor total da estadia), sinal (número em reais ou null; só se a conversa disser o valor do sinal pago), "
        . "ano_assumido (true se a conversa não dizia o ano e você escolheu o próximo ano futuro possível).\n"
        . "Use null para qualquer dado que NÃO esteja claramente na conversa. Nunca invente nome, documento, e-mail, valores ou loft. "
        . "Se um valor estiver em outra moeda que não reais, use null no total.";
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 40,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $chave],
        CURLOPT_POSTFIELDS => json_encode(['model' => cfg('groq_model') ?: 'llama-3.3-70b-versatile', 'temperature' => 0,
            'response_format' => ['type' => 'json_object'],
            'messages' => [['role' => 'system', 'content' => $sistema], ['role' => 'user', 'content' => mb_substr($conversa, 0, 12000)]]])]);
    $r = json_decode((string) curl_exec($ch), true);
    curl_close($ch);
    $j = json_decode((string) ($r['choices'][0]['message']['content'] ?? ''), true);
    if (!is_array($j)) throw new RuntimeException('O Groq não respondeu: ' . ($r['error']['message'] ?? 'sem resposta') . '. Preencha à mão.');
    return $j;
}

// ---------- validação ----------
/** Devolve [dados|null, erros, avisos]. $in = campos do formulário. */
function comprovante_validar(array $in): array {
    $erros = $avisos = [];
    $t = fn($k) => trim((string) ($in[$k] ?? ''));
    $nome = $t('nome'); $doc = $t('documento'); $hosp = $t('hospedes'); $email = $t('email');
    $idioma = $t('idioma') === 'es' ? 'es' : 'pt';
    $loft = (int) $t('loft');
    $total = valor_parse($t('total'));
    $sinal = $t('sinal') === '' ? null : valor_parse($t('sinal'));
    foreach (['nome' => $nome, 'documento' => $doc, 'hóspedes' => $hosp] as $k => $v) if ($v === '') $erros[] = "Preencha: $k.";
    if ($loft < 3 || $loft > 9) $erros[] = 'Loft deve ser de 3 a 9.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    try { $ci = new DateTimeImmutable($t('checkin')); $co = new DateTimeImmutable($t('checkout')); }
    catch (Throwable) { $erros[] = 'Datas inválidas.'; $ci = $co = null; }
    if ($total === null || $total <= 0) $erros[] = 'Informe o valor total.';
    if ($erros) return [null, $erros, []];
    $sinal ??= round($total / 2, 2);
    $noites = (int) $ci->diff($co)->format('%r%a');
    if ($noites <= 0) $erros[] = 'O check-out precisa ser depois do check-in.';
    if ($sinal <= 0 || $sinal > $total) $erros[] = 'Sinal inválido (precisa ser maior que 0 e no máximo o total).';
    if ($erros) return [null, $erros, []];
    $hoje = new DateTimeImmutable('today');
    if ($ci < $hoje) $avisos[] = 'O check-in já passou.';
    if ($hoje->diff($ci)->days > 300 && $ci > $hoje) $avisos[] = 'O check-in está a mais de 300 dias (o ano está certo?).';
    if ($noites > 60) $avisos[] = "$noites noites (confere?).";
    return [compact('nome', 'doc', 'hosp', 'email', 'idioma', 'loft', 'total', 'sinal', 'noites') + ['ci' => $ci, 'co' => $co], [], $avisos];
}

// ---------- PDF ----------
function comprovante_html(array $d, string $numero): string {
    $t = TEXTOS[$d['idioma']];
    $saldo = round($d['total'] - $d['sinal'], 2);
    $pct = ' de ' . round($d['sinal'] / $d['total'] * 100) . '%';
    $v = array_merge($t, [
        'numero' => $numero, 'nome' => h($d['nome']), 'documento' => h($d['doc']), 'loft' => sprintf('%02d', $d['loft']), 'hospedes' => h($d['hosp']),
        'checkin' => data_extenso($d['ci'], $d['idioma']), 'checkout' => data_extenso($d['co'], $d['idioma']),
        'noites' => $d['noites'] . ' ' . ($d['noites'] === 1 ? $t['noite'] : $t['noites']),
        'total' => brl($d['total']), 'sinal' => brl($d['sinal']), 'saldo' => brl($saldo),
    ]);
    $v['intro'] = str_replace('{loft}', $v['loft'], $t['intro']);
    $v['texto_recibo'] = str_replace(['{sinal}', '{pct}'], [brl($d['sinal']), $pct], $saldo == 0 ? $t['quitado'] : $t['recibo']);
    $v['emitido'] = str_replace('{data}', data_extenso(new DateTimeImmutable('today'), $d['idioma']), $t['emitido']);
    $modelo = require __DIR__ . '/modelo_comprovante.php';
    return strtr($modelo, array_combine(array_map(fn($k) => '{{' . $k . '}}', array_keys($v)), array_map('strval', $v)));
}

function comprovante_pdf(string $html): string {
    require_once __DIR__ . '/vendor/autoload.php';
    $o = new Dompdf\Options();
    $o->set('chroot', __DIR__);
    $o->set('isRemoteEnabled', false);
    $dp = new Dompdf\Dompdf($o);
    $dp->setBasePath(__DIR__);
    $dp->loadHtml($html, 'UTF-8');
    $dp->setPaper('A4');
    $dp->render();
    return $dp->output();
}

// ---------- gerar, guardar, enviar ----------
/** Gera tudo. Devolve ['numero','arquivo','enviado'=>bool|null,'drive'=>url|null,'avisos'=>[...]]. */
function comprovante_emitir(array $d): array {
    $numero = '';
    $ano = date('Y');
    json_atualizar('comprovantes', function ($c) use (&$numero, $ano) {
        $n = count(array_filter($c['itens'] ?? [], fn($i) => str_starts_with($i['numero'], "$ano-"))) + 1;
        $numero = sprintf('%s-%03d', $ano, $n);
        $c['itens'][] = ['numero' => $numero, 'reservado_em' => date('c')];
        return $c;
    });
    $arquivo = sprintf('%s_loft%02d_%s.pdf', $numero, $d['loft'], slug($d['nome']));
    try {
        $pdf = comprovante_pdf(comprovante_html($d, $numero));
    } catch (Throwable $e) {
        json_atualizar('comprovantes', function ($c) use ($numero) { $c['itens'] = array_values(array_filter($c['itens'], fn($i) => $i['numero'] !== $numero)); return $c; });
        throw $e;
    }
    if (!is_dir(DADOS . '/comprovantes')) mkdir(DADOS . '/comprovantes', 0700, true);
    file_put_contents(DADOS . '/comprovantes/' . $arquivo, $pdf);
    $r = ['numero' => $numero, 'arquivo' => $arquivo, 'enviado' => null, 'drive' => null, 'avisos' => []];

    $url = (string) cfg('apps_script_url', '');
    if ($url !== '') {
        $t = TEXTOS[$d['idioma']];
        $corpo = strtr($t['email'], ['{nome}' => explode(' ', $d['nome'])[0], '{loft}' => sprintf('%02d', $d['loft']),
            '{checkin}' => $d['ci']->format('d/m/Y'), '{checkout}' => $d['co']->format('d/m/Y')]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],  // text/plain evita o preflight do Apps Script
            CURLOPT_POSTFIELDS => json_encode(['segredo' => cfg('apps_script_segredo', ''), 'arquivo' => $arquivo, 'pdf' => base64_encode($pdf),
                'email' => $d['email'], 'assunto' => str_replace('{numero}', $numero, $t['assunto']), 'corpo' => $corpo])]);
        $resp = json_decode((string) curl_exec($ch), true);
        curl_close($ch);
        if (!empty($resp['ok'])) { $r['enviado'] = $d['email'] !== '' ? !empty($resp['enviado']) : null; $r['drive'] = $resp['drive'] ?? null; }
        else $r['avisos'][] = 'O envio por e-mail/Drive falhou: ' . ($resp['erro'] ?? 'sem resposta do Apps Script') . '. O PDF foi gerado e pode ser baixado.';
    } else {
        $r['avisos'][] = 'Apps Script não configurado: o PDF foi gerado, mas não foi enviado por e-mail nem salvo no Drive.';
    }

    json_atualizar('comprovantes', function ($c) use ($numero, $d, $r, $arquivo) {
        foreach ($c['itens'] as &$i) if ($i['numero'] === $numero) {
            $i = ['numero' => $numero, 'emitido_em' => date('Y-m-d'), 'idioma' => $d['idioma'], 'nome' => $d['nome'], 'documento' => $d['doc'],
                'email' => $d['email'], 'loft' => $d['loft'], 'hospedes' => $d['hosp'], 'checkin' => $d['ci']->format('Y-m-d'),
                'checkout' => $d['co']->format('Y-m-d'), 'noites' => $d['noites'], 'total' => $d['total'], 'sinal' => $d['sinal'],
                'saldo' => round($d['total'] - $d['sinal'], 2), 'arquivo' => $arquivo, 'enviado' => $r['enviado'], 'drive' => $r['drive']];
        }
        return $c;
    });
    return $r;
}
