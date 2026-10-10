<?php
declare(strict_types=1);
// Webhook do bot do Telegram (manutenção). Só aceita chamadas com o segredo do webhook e só obedece gerentes/executor cadastrados.
require __DIR__ . '/../admin/manutencao_lib.php';

http_response_code(200);
if (!cfg('telegram_secret') || !hash_equals((string) cfg('telegram_secret'), (string) ($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? ''))) exit;
$u = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($u)) exit;

// o Telegram reenvia o mesmo update se a resposta demorar: ignora repetidos
$repetido = false;
json_atualizar('tarefas', function ($d) use ($u, &$repetido) {
    if (($u['update_id'] ?? 0) <= ($d['ultimo_update'] ?? 0)) $repetido = true; else $d['ultimo_update'] = $u['update_id'];
    return $d;
});
if ($repetido) exit;

function teclado_prioridade(array $t): array {
    $b = [];
    foreach (PRIORIDADES as $n => $nome) $b[] = ['text' => ($t['prioridade'] === $n ? '✔ ' : '') . $nome, 'callback_data' => "p:{$t['id']}:$n"];
    return ['inline_keyboard' => [$b, [['text' => '🗑 Excluir', 'callback_data' => "x:{$t['id']}"]]]];
}

function texto_tarefa(array $t): string {
    return "#{$t['id']} · " . PRIORIDADES[$t['prioridade']] . "\n" . resumo($t);
}

function enviar_tarefa_ao_executor(int $chat, array $t): void {
    foreach ($t['midias'] as $m) tg_enviar_midia($chat, $m);
    $v = $t['valor'] !== null ? ' · ' . brl((float) $t['valor']) : '';
    tg('sendMessage', ['chat_id' => $chat, 'text' => texto_tarefa($t) . $v, 'reply_markup' => ['inline_keyboard' => [[
        ['text' => '💲 Informar valor', 'callback_data' => "v:{$t['id']}"],
        ['text' => '✅ Executada', 'callback_data' => "e:{$t['id']}"]]]]]);
}

function listar_ao_executor(int $chat): void {
    $l = tarefas_por_status('aberta');
    if (!$l) { tg('sendMessage', ['chat_id' => $chat, 'text' => 'Nenhuma tarefa em aberto. 🎉']); return; }
    tg('sendMessage', ['chat_id' => $chat, 'text' => count($l) . ' tarefa(s) em aberto, da mais urgente para a menos:']);
    foreach ($l as $t) enviar_tarefa_ao_executor($chat, $t);
}

function listar_ao_gerente(int $chat): void {
    $l = tarefas_por_status('aberta');
    $txt = $l ? "Em aberto:\n" . implode("\n", array_map('texto_tarefa', $l)) : 'Nenhuma tarefa em aberto.';
    [$q, $s] = total_em_aberto();
    tg('sendMessage', ['chat_id' => $chat, 'text' => $txt . "\n\nExecutadas aguardando pagamento: " . brl($s) . " ($q). Use /pagar."]);
}

/** Extrai a primeira mídia da mensagem: [tipo, file_id, nome, tamanho] ou null. */
function midia_da_mensagem(array $m): ?array {
    if (!empty($m['photo'])) { $p = end($m['photo']); return ['photo', $p['file_id'], 'foto', (int) ($p['file_size'] ?? 0)]; }
    foreach (['video', 'animation', 'video_note', 'voice', 'audio', 'document'] as $tipo) {
        if (!empty($m[$tipo])) return [$tipo, $m[$tipo]['file_id'], $m[$tipo]['file_name'] ?? $tipo, (int) ($m[$tipo]['file_size'] ?? 0)];
    }
    return null;
}

// ---------- botões ----------
if (isset($u['callback_query'])) {
    $cb = $u['callback_query'];
    $de = (int) $cb['from']['id'];
    $chat = (int) $cb['message']['chat']['id'];
    $mid = (int) $cb['message']['message_id'];
    $eh_g = in_array($de, gerentes(), true);
    $eh_e = $de === executor();
    [$acao, $id, $arg] = array_pad(explode(':', (string) $cb['data']), 3, '');
    $id = (int) $id;
    $resp = '';
    if ($eh_g && $acao === 'p') {
        $t = tarefa_mudar($id, function ($t) use ($arg) { if ($t['status'] === 'aberta' && isset(PRIORIDADES[(int) $arg])) $t['prioridade'] = (int) $arg; return $t; });
        if ($t) tg('editMessageText', ['chat_id' => $chat, 'message_id' => $mid, 'text' => '✅ Demanda registrada.' . "\n" . texto_tarefa($t), 'reply_markup' => teclado_prioridade($t)]);
    } elseif ($eh_g && $acao === 'x') {
        json_atualizar('tarefas', function ($d) use ($id) { if (($d['itens'][(string) $id]['status'] ?? '') === 'aberta') unset($d['itens'][(string) $id]); return $d; });
        tg('editMessageText', ['chat_id' => $chat, 'message_id' => $mid, 'text' => "🗑 Demanda #$id excluída."]);
    } elseif ($eh_e && ($acao === 'v' || $acao === 'e')) {
        $t = tarefas()[(string) $id] ?? null;
        if (!$t || $t['status'] !== 'aberta') { $resp = 'Essa tarefa já foi encerrada.'; }
        elseif ($acao === 'e' && $t['valor'] !== null) {
            tarefa_executar($id);
            tg('sendMessage', ['chat_id' => $chat, 'text' => "✅ Tarefa #$id marcada como executada. O gerente foi avisado."]);
        } else {
            estado_gravar($chat, ['tipo' => 'valor', 'tarefa' => $id, 'concluir' => $acao === 'e']);
            tg('sendMessage', ['chat_id' => $chat, 'text' => "Digite o valor da tarefa #$id (ex.: 150 ou 150,50)." . ($acao === 'e' ? "\nDepois do valor eu marco como executada." : '')]);
        }
    }
    tg('answerCallbackQuery', ['callback_query_id' => $cb['id'], 'text' => $resp]);
    exit;
}

// ---------- mensagens ----------
$m = $u['message'] ?? null;
if (!$m || ($m['chat']['type'] ?? '') !== 'private') exit;
$de = (int) $m['from']['id'];
$chat = (int) $m['chat']['id'];
$eh_g = in_array($de, gerentes(), true);
$eh_e = $de === executor();
$texto = trim((string) ($m['text'] ?? $m['caption'] ?? ''));
$cmd = str_starts_with($texto, '/') ? strtolower(explode('@', explode(' ', $texto)[0])[0]) : '';

if (!$eh_g && !$eh_e) {
    if ($cmd === '/start' || $cmd === '/meuid') tg('sendMessage', ['chat_id' => $chat, 'text' => "Seu ID do Telegram é $de. Passe esse número ao Leonardo para liberar o seu acesso."]);
    exit;
}

if ($cmd === '/cancelar') { estado_gravar($chat, null); tg('sendMessage', ['chat_id' => $chat, 'text' => 'Ok, cancelado.']); exit; }

if ($eh_e) {  // ---- executor ----
    $est = estado_ler($chat);
    if ($cmd === '/start' || $cmd === '/ajuda') {
        tg('sendMessage', ['chat_id' => $chat, 'text' => "Olá! Comandos:\n/tarefas — ver as tarefas em aberto (da mais urgente para a menos)\n\nEm cada tarefa use os botões para informar o valor e marcar como executada."]);
    } elseif ($cmd === '/tarefas') {
        listar_ao_executor($chat);
    } elseif (($est['tipo'] ?? '') === 'valor' && $cmd === '') {
        $v = valor_parse($texto);
        if ($v === null || $v <= 0) { tg('sendMessage', ['chat_id' => $chat, 'text' => 'Não entendi o valor. Digite só o número, ex.: 150 ou 150,50 (ou /cancelar).']); exit; }
        $t = tarefa_valor_salvar((int) $est['tarefa'], $v);
        estado_gravar($chat, null);
        if ($t && !empty($est['concluir'])) {
            tarefa_executar($t['id']);
            tg('sendMessage', ['chat_id' => $chat, 'text' => "✅ Tarefa #{$t['id']} executada por " . brl($v) . ". O gerente foi avisado."]);
        } elseif ($t) {
            tg('sendMessage', ['chat_id' => $chat, 'text' => "Valor da tarefa #{$t['id']}: " . brl($v) . '.', 'reply_markup' => ['inline_keyboard' => [[['text' => '✅ Marcar como executada', 'callback_data' => "e:{$t['id']}"]]]]]);
        }
    } else {
        tg('sendMessage', ['chat_id' => $chat, 'text' => 'Use /tarefas para ver a lista.']);
    }
    exit;
}

// ---- gerente ----
$est = estado_ler($chat);
if ($cmd === '/start' || $cmd === '/ajuda') {
    tg('sendMessage', ['chat_id' => $chat, 'text' => "Olá! Para registrar uma demanda de manutenção é só me mandar texto, foto, vídeo ou áudio (pode juntar tudo).\n\n/tarefas — demandas em aberto\n/pagar — pagar o que o prestador já executou (depois envie o comprovante)\n/cancelar — desistir de uma ação"]);
} elseif ($cmd === '/tarefas') {
    listar_ao_gerente($chat);
} elseif ($cmd === '/pagar') {
    $l = tarefas_por_status('executada');
    if (!$l) { tg('sendMessage', ['chat_id' => $chat, 'text' => 'Nada em aberto para pagar.']); exit; }
    $soma = array_sum(array_map(fn($t) => (float) $t['valor'], $l));
    estado_gravar($chat, ['tipo' => 'comprovante', 'ids' => array_column($l, 'id')]);
    tg('sendMessage', ['chat_id' => $chat, 'text' => "A pagar: " . brl($soma) . "\n" . implode("\n", array_map(fn($t) => "#{$t['id']} · " . brl((float) $t['valor']) . ' · ' . mb_strimwidth(resumo($t), 0, 60, '…'), $l))
        . "\n\nFaça o pagamento e me envie agora o comprovante (foto ou PDF). Eu marco tudo como pago e mando o comprovante ao prestador. /cancelar para desistir."]);
} elseif (($est['tipo'] ?? '') === 'comprovante' && $cmd === '' && midia_da_mensagem($m)) {
    $ids = array_map('intval', $est['ids']);
    $pagas = [];
    json_atualizar('tarefas', function ($d) use ($ids, &$pagas) {
        foreach ($ids as $i) {
            if (($d['itens'][(string) $i]['status'] ?? '') === 'executada') {
                $d['itens'][(string) $i]['status'] = 'paga';
                $d['itens'][(string) $i]['paga_em'] = date('c');
                $pagas[] = $d['itens'][(string) $i];
            }
        }
        return $d;
    });
    estado_gravar($chat, null);
    planilha_enviar($pagas);
    $soma = array_sum(array_map(fn($t) => (float) $t['valor'], $pagas));
    $lista = implode(', ', array_map(fn($t) => '#' . $t['id'], $pagas));
    $aviso = "💰 Pagamento de " . brl($soma) . " registrado (tarefas $lista).";
    if (executor()) {
        tg('sendMessage', ['chat_id' => executor(), 'text' => "💰 Você recebeu um pagamento de " . brl($soma) . " referente às tarefas $lista. Comprovante abaixo:"]);
        tg('copyMessage', ['chat_id' => executor(), 'from_chat_id' => $chat, 'message_id' => (int) $m['message_id']]);
        $aviso .= "\nComprovante enviado ao prestador.";
    } else {
        $aviso .= "\n⚠ Prestador ainda não cadastrado: o comprovante NÃO foi encaminhado.";
    }
    tg('sendMessage', ['chat_id' => $chat, 'text' => $aviso]);
    avisar_gerentes($aviso, [], $chat);
} elseif (($est['tipo'] ?? '') === 'comprovante' && $cmd === '') {
    tg('sendMessage', ['chat_id' => $chat, 'text' => 'Estou esperando o comprovante do pagamento (foto ou PDF). /cancelar para desistir.']);
} elseif ($cmd === '') {
    $mid = midia_da_mensagem($m);
    if ($texto === '' && !$mid) { tg('sendMessage', ['chat_id' => $chat, 'text' => 'Mande texto, foto, vídeo ou áudio.']); exit; }
    $midia = [];
    if ($mid) {
        [$tipo, $fid, $nome, $tam] = $mid;
        $midia = ['tipo' => $tipo, 'file_id' => $fid, 'nome' => $nome, 'arquivo' => null];
        // o nome local vem do fim do file_id (único por arquivo); o id da tarefa ainda não existe aqui
        if ($tam <= 20 * 1024 * 1024) $midia['arquivo'] = tg_baixar($fid, substr(md5($fid), 0, 16));
    }
    [$t, $nova] = tarefa_nova($de, $texto, $midia, $m['media_group_id'] ?? null);
    if ($nova) tg('sendMessage', ['chat_id' => $chat, 'text' => '✅ Demanda registrada.' . "\n" . texto_tarefa($t) . "\n\nEscolha a prioridade:", 'reply_markup' => teclado_prioridade($t)]);
} else {
    tg('sendMessage', ['chat_id' => $chat, 'text' => 'Comando desconhecido. Use /ajuda.']);
}
