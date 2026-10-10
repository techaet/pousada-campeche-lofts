<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

// Manutenção: tarefas em _dados/tarefas.json, mídias em _dados/midia/. Usado pelo bot (manutencao/bot.php),
// pela página do executor (manutencao/index.php) e pelo painel (admin/manutencao.php).

const PRIORIDADES = [1 => '🔴 Alta', 2 => '🟡 Média', 3 => '🟢 Baixa'];

// ---------- Telegram ----------
function tg(string $metodo, array $p = []) {
    $ch = curl_init(cfg('telegram_api', 'https://api.telegram.org') . '/bot' . cfg('telegram_token') . '/' . $metodo);
    $upload = (bool) array_filter($p, fn($v) => $v instanceof CURLFile);
    if ($upload) {  // multipart: arrays (reply_markup) vão como JSON dentro do campo
        foreach ($p as $k => $v) if (is_array($v)) $p[$k] = json_encode($v, JSON_UNESCAPED_UNICODE);
    }
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $upload ? 180 : 30,
        CURLOPT_POSTFIELDS => $upload ? $p : json_encode($p, JSON_UNESCAPED_UNICODE)]
        + ($upload ? [] : [CURLOPT_HTTPHEADER => ['Content-Type: application/json']]));
    $r = json_decode((string) curl_exec($ch), true);
    return $r['result'] ?? null;
}

/** Manda uma mídia da tarefa a um chat: pelo file_id (veio do Telegram) ou subindo o arquivo local (veio do site). */
function tg_enviar_midia(int $chat, array $m): void {
    $metodo = ['photo' => 'sendPhoto', 'video' => 'sendVideo', 'animation' => 'sendAnimation', 'video_note' => 'sendVideoNote',
        'voice' => 'sendVoice', 'audio' => 'sendAudio'][$m['tipo']] ?? 'sendDocument';
    $campo = ['sendPhoto' => 'photo', 'sendVideo' => 'video', 'sendAnimation' => 'animation', 'sendVideoNote' => 'video_note',
        'sendVoice' => 'voice', 'sendAudio' => 'audio'][$metodo] ?? 'document';
    if (!empty($m['file_id'])) { tg($metodo, ['chat_id' => $chat, $campo => $m['file_id']]); return; }
    $f = DADOS . '/midia/' . ($m['arquivo'] ?? '');
    if (is_file($f)) tg($metodo, ['chat_id' => $chat, $campo => new CURLFile($f, '', $m['nome'] ?? basename($f))]);
}

/** Baixa um arquivo do Telegram para _dados/midia/ (bots só baixam até 20 MB). Devolve o nome local ou null. */
function tg_baixar(string $file_id, string $prefixo): ?string {
    $info = tg('getFile', ['file_id' => $file_id]);
    if (empty($info['file_path'])) return null;
    $ext = strtolower(pathinfo($info['file_path'], PATHINFO_EXTENSION)) ?: 'bin';
    if (!preg_match('/^[a-z0-9]{1,5}$/', $ext)) $ext = 'bin';
    if (!is_dir(DADOS . '/midia')) mkdir(DADOS . '/midia', 0700, true);
    $nome = $prefixo . '.' . $ext;
    $fp = fopen(DADOS . '/midia/' . $nome, 'w');
    $ch = curl_init(cfg('telegram_api', 'https://api.telegram.org') . '/file/bot' . cfg('telegram_token') . '/' . $info['file_path']);
    curl_setopt_array($ch, [CURLOPT_FILE => $fp, CURLOPT_TIMEOUT => 120]);
    $ok = curl_exec($ch) !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
    fclose($fp);
    if (!$ok) { @unlink(DADOS . '/midia/' . $nome); return null; }
    return $nome;
}

/** Guarda o @username do bot (via getMe) para o link do Telegram no painel. */
function bot_username_garantir(): void {
    if (cfg('telegram_token') && !cfg('telegram_bot_username') && ($me = tg('getMe')) && !empty($me['username'])) cfg_salvar(['telegram_bot_username' => $me['username']]);
}

/** Fonte única dos comandos do bot: alimenta o /ajuda do bot, a página do executor, o card do admin e o menu "/" do Telegram.
 *  [comando ou ação, descrição]. Os que começam com "/" são comandos de verdade (entram no menu do Telegram). */
const COMANDOS_BOT = [
    'gerente' => [
        ['texto, foto, vídeo ou áudio', 'registra uma demanda (pode juntar tudo). Depois toque em 🔴 Alta, 🟡 Média ou 🟢 Baixa, ou em 🗑 Excluir'],
        ['/tarefas', 'ver as demandas em aberto e o total a pagar'],
        ['/pagar', 'pagar o que o prestador executou: envie em seguida o comprovante (foto ou PDF)'],
        ['/cancelar', 'cancelar a ação em andamento'],
        ['/ajuda', 'mostrar esta ajuda'],
    ],
    'executor' => [
        ['/tarefas', 'ver as tarefas em aberto, da mais urgente para a menos urgente, com fotos e vídeos'],
        ['💲 Informar valor', 'botão em cada tarefa: digite o valor (ex.: 150 ou 150,50)'],
        ['✅ Executada', 'botão em cada tarefa: marca como feita e avisa os gerentes'],
        ['/cancelar', 'cancelar a ação em andamento'],
        ['/ajuda', 'mostrar esta ajuda'],
    ],
    'novo' => [
        ['/start', 'começar'],
        ['/meuid', 'ver o meu ID do Telegram (o número que o Leonardo cadastra para liberar o acesso)'],
    ],
];

/** Texto do /ajuda do bot para um papel ('gerente' ou 'executor'). */
function ajuda_texto(string $papel): string {
    $abre = $papel === 'gerente' ? "Olá! Estes são os comandos:\n\n" : "Olá! Estes são os comandos e botões:\n\n";
    return $abre . implode("\n", array_map(fn($c) => $c[0] . ' — ' . $c[1], COMANDOS_BOT[$papel]));
}

/** Mesma lista em HTML (página do executor e card do admin). */
function ajuda_html(string $papel): string {
    return '<ul style="margin:0;padding-left:20px">' . implode('', array_map(fn($c) => '<li><b>' . h($c[0]) . '</b>: ' . h($c[1]) . '</li>', COMANDOS_BOT[$papel])) . '</ul>';
}

/** Registra no Telegram o menu "/" de cada pessoa: gerentes, executor e (para os demais) só /start e /meuid. Devolve quantos menus foram aceitos. */
function bot_registrar_comandos(): int {
    $menu = fn(string $papel) => array_values(array_map(fn($c) => ['command' => ltrim($c[0], '/'), 'description' => mb_substr($c[1], 0, 100)],
        array_filter(COMANDOS_BOT[$papel], fn($c) => str_starts_with($c[0], '/'))));
    $ok = (int) (tg('setMyCommands', ['commands' => $menu('novo'), 'scope' => ['type' => 'default']]) === true);
    foreach (gerentes() as $g) $ok += (int) (tg('setMyCommands', ['commands' => $menu('gerente'), 'scope' => ['type' => 'chat', 'chat_id' => $g]]) === true);
    if (executor()) $ok += (int) (tg('setMyCommands', ['commands' => $menu('executor'), 'scope' => ['type' => 'chat', 'chat_id' => executor()]]) === true);
    return $ok;
}

function gerentes(): array { return array_map('intval', array_filter((array) cfg('gerentes', []))); }
function executor(): int { return (int) cfg('executor_id', 0); }

function avisar_gerentes(string $texto, array $extra = [], int $menos = 0): void {
    foreach (gerentes() as $g) if ($g !== $menos) tg('sendMessage', ['chat_id' => $g, 'text' => $texto] + $extra);
}

// ---------- tarefas ----------
function tarefas(): array { return json_ler('tarefas')['itens'] ?? []; }

function tarefa_nova(int $por, string $texto, array $midia, ?string $grupo): array {
    $ret = null;
    json_atualizar('tarefas', function ($d) use (&$ret, $por, $texto, $midia, $grupo) {
        if ($grupo !== null && isset($d['grupos'][$grupo], $d['itens'][$d['grupos'][$grupo]])) {
            $id = $d['grupos'][$grupo];
            if ($midia) $d['itens'][$id]['midias'][] = $midia;
            if ($texto !== '' && ($d['itens'][$id]['texto'] ?? '') === '') $d['itens'][$id]['texto'] = $texto;
            $ret = [$d['itens'][$id], false];
            return $d;
        }
        $id = (string) (($d['seq'] ?? 0) + 1);
        $d['seq'] = (int) $id;
        $d['itens'][$id] = ['id' => (int) $id, 'criada' => date('c'), 'por' => $por, 'texto' => $texto,
            'midias' => $midia ? [$midia] : [], 'prioridade' => 2, 'status' => 'aberta', 'valor' => null];
        if ($grupo !== null) $d['grupos'][$grupo] = $id;
        $ret = [$d['itens'][$id], true];
        return $d;
    });
    return $ret;
}

/** Cria a tarefa de uma vez (usado pelo painel, com várias mídias). */
function tarefa_criar(int $por, string $texto, array $midias, int $prioridade): array {
    $ret = null;
    json_atualizar('tarefas', function ($d) use (&$ret, $por, $texto, $midias, $prioridade) {
        $id = (string) (($d['seq'] ?? 0) + 1);
        $d['seq'] = (int) $id;
        $d['itens'][$id] = $ret = ['id' => (int) $id, 'criada' => date('c'), 'por' => $por, 'texto' => $texto, 'midias' => $midias,
            'prioridade' => isset(PRIORIDADES[$prioridade]) ? $prioridade : 2, 'status' => 'aberta', 'valor' => null];
        return $d;
    });
    return $ret;
}

const LIMITE_UPLOAD = 50 * 1024 * 1024;  // limite dos bots do Telegram para enviar arquivos

/** Valida e guarda um arquivo enviado pelo painel. Devolve a mídia da tarefa; lança RuntimeException com a razão. */
function midia_salvar_upload(string $nome, string $tmp, int $erro, int $tam): array {
    if ($erro === UPLOAD_ERR_INI_SIZE || $erro === UPLOAD_ERR_FORM_SIZE) throw new RuntimeException("\"$nome\" é maior que o limite do servidor (" . ini_get('upload_max_filesize') . ').');
    if ($erro !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) throw new RuntimeException("Não consegui receber \"$nome\".");
    if ($tam > LIMITE_UPLOAD) throw new RuntimeException("\"$nome\" passa de 50 MB.");
    $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/heic' => 'heic', 'image/heif' => 'heic',
        'video/mp4' => 'mp4', 'video/quicktime' => 'mov', 'video/webm' => 'webm',
        'audio/mpeg' => 'mp3', 'audio/mp4' => 'm4a', 'audio/x-m4a' => 'm4a', 'audio/aac' => 'aac', 'audio/ogg' => 'ogg', 'audio/wav' => 'wav', 'audio/x-wav' => 'wav', 'audio/webm' => 'webm'][$mime] ?? null;
    if (!$ext) throw new RuntimeException("\"$nome\": tipo de arquivo não aceito ($mime). Use foto, vídeo ou áudio.");
    if (!is_dir(DADOS . '/midia')) mkdir(DADOS . '/midia', 0700, true);
    $arq = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($tmp, DADOS . '/midia/' . $arq)) throw new RuntimeException("Não consegui guardar \"$nome\".");
    $tipo = $ext === 'heic' ? 'document' : (str_starts_with($mime, 'image/') ? 'photo' : (str_starts_with($mime, 'video/') ? 'video' : 'audio'));
    return ['tipo' => $tipo, 'file_id' => null, 'nome' => mb_substr(basename($nome), 0, 80), 'arquivo' => $arq];
}

/** Aplica $fn(tarefa) -> tarefa sob trava. Devolve a tarefa nova (ou null se não existe). */
function tarefa_mudar(int $id, callable $fn): ?array {
    $ret = null;
    json_atualizar('tarefas', function ($d) use ($id, $fn, &$ret) {
        if (isset($d['itens'][(string) $id])) { $d['itens'][(string) $id] = $ret = $fn($d['itens'][(string) $id]); }
        return $d;
    });
    return $ret;
}

function tarefas_por_status(string $status): array {
    $l = array_values(array_filter(tarefas(), fn($t) => $t['status'] === $status));
    usort($l, fn($a, $b) => [$a['prioridade'], $a['id']] <=> [$b['prioridade'], $b['id']]);
    return $l;
}

function total_em_aberto(): array {  // [qtd, soma] das executadas ainda não pagas
    $l = tarefas_por_status('executada');
    return [count($l), array_sum(array_map(fn($t) => (float) $t['valor'], $l))];
}

function tarefa_valor_salvar(int $id, float $v): ?array {
    return tarefa_mudar($id, function ($t) use ($v) { if ($t['status'] === 'aberta') $t['valor'] = $v; return $t; });
}

/** Marca como executada (precisa ter valor) e avisa os gerentes. Devolve a tarefa ou null se não deu. */
function tarefa_executar(int $id): ?array {
    $ok = false;
    $t = tarefa_mudar($id, function ($t) use (&$ok) {
        if ($t['status'] === 'aberta' && $t['valor'] !== null) { $t['status'] = 'executada'; $t['executada_em'] = date('c'); $ok = true; }
        return $t;
    });
    if (!$ok) return null;
    [$qtd, $soma] = total_em_aberto();
    avisar_gerentes("✅ Tarefa #{$t['id']} executada — " . brl((float) $t['valor']) . "\n" . resumo($t)
        . "\n\nEm aberto para pagamento: " . brl($soma) . " ($qtd item" . ($qtd === 1 ? '' : 's') . "). Use /pagar.");
    planilha_enviar([$t]);
    return $t;
}

/** Grava/atualiza as tarefas na planilha de custos (Drive). Falha não derruba o fluxo: o botão do painel reenvia tudo. */
function planilha_enviar(array $tarefas): bool {
    if (!$tarefas) return true;
    $quando = fn($iso) => $iso ? date('d/m/Y H:i', strtotime($iso)) : '';
    $r = apps_script(['acao' => 'manutencao', 'itens' => array_map(fn($t) => [
        'id' => $t['id'], 'executada_em' => $quando($t['executada_em'] ?? ''), 'prioridade' => trim(preg_replace('/^\S+\s/u', '', PRIORIDADES[$t['prioridade']])),
        'descricao' => trim((string) $t['texto']) !== '' ? trim((string) $t['texto']) : '(sem texto)', 'valor' => (float) $t['valor'],
        'status' => $t['status'] === 'paga' ? 'Paga' : 'A pagar', 'paga_em' => $quando($t['paga_em'] ?? ''), 'anexos' => count($t['midias'])], array_values($tarefas))]);
    if (!empty($r['ok']) && !empty($r['planilha'])) { cfg_salvar(['planilha_url' => $r['planilha']]); return true; }
    return false;
}

function resumo(array $t): string {
    $x = trim((string) $t['texto']);
    $n = count($t['midias']);
    return ($x !== '' ? mb_strimwidth($x, 0, 200, '…') : '(sem texto)') . ($n ? " · $n anexo" . ($n > 1 ? 's' : '') : '');
}

// ---------- estado da conversa (esperando valor / comprovante) ----------
function estado_ler(int $chat): array { return json_ler('tarefas')['estados'][(string) $chat] ?? []; }
function estado_gravar(int $chat, ?array $e): void {
    json_atualizar('tarefas', function ($d) use ($chat, $e) {
        if ($e === null) unset($d['estados'][(string) $chat]); else $d['estados'][(string) $chat] = $e;
        return $d;
    });
}

// ---------- HTML de uma tarefa (executor e painel) ----------
function midia_html(array $t, string $chave): string {
    $o = '';
    foreach ($t['midias'] as $i => $m) {
        $u = '/manutencao/midia.php?k=' . rawurlencode($chave) . '&f=' . rawurlencode((string) ($m['arquivo'] ?? ''));
        if (empty($m['arquivo'])) { $o .= '<p class="dica">📎 ' . h($m['tipo']) . ' grande demais para o site — veja no Telegram.</p>'; continue; }
        $o .= match ($m['tipo']) {
            'photo' => '<a href="' . h($u) . '" target="_blank"><img src="' . h($u) . '" alt="foto" loading="lazy"></a>',
            'video', 'animation', 'video_note' => '<video src="' . h($u) . '" controls playsinline preload="metadata"></video>',
            'voice', 'audio' => '<audio src="' . h($u) . '" controls preload="none"></audio>',
            default => '<p><a href="' . h($u) . '" target="_blank">📎 ' . h($m['nome'] ?? 'arquivo') . '</a></p>',
        };
    }
    return $o;
}
