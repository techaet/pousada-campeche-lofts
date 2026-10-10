<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

// Manutenção: tarefas em _dados/tarefas.json, mídias em _dados/midia/. Usado pelo bot (manutencao/bot.php),
// pela página do executor (manutencao/index.php) e pelo painel (admin/manutencao.php).

const PRIORIDADES = [1 => '🔴 Alta', 2 => '🟡 Média', 3 => '🟢 Baixa'];

// ---------- Telegram ----------
function tg(string $metodo, array $p = []) {
    $ch = curl_init(cfg('telegram_api', 'https://api.telegram.org') . '/bot' . cfg('telegram_token') . '/' . $metodo);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30,
        CURLOPT_POSTFIELDS => json_encode($p, JSON_UNESCAPED_UNICODE), CURLOPT_HTTPHEADER => ['Content-Type: application/json']]);
    $r = json_decode((string) curl_exec($ch), true);
    curl_close($ch);
    return $r['result'] ?? null;
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
    curl_close($ch);
    fclose($fp);
    if (!$ok) { @unlink(DADOS . '/midia/' . $nome); return null; }
    return $nome;
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
    return $t;
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
