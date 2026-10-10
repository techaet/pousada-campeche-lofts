<?php
declare(strict_types=1);
// Página do executor: acesso pelo link com chave (?k=...). Mesma lista do bot do Telegram.
require __DIR__ . '/../admin/manutencao_lib.php';
$k = (string) ($_REQUEST['k'] ?? '');
if ($k === '' || !cfg('executor_token') || !hash_equals((string) cfg('executor_token'), $k)) { http_response_code(404); exit('Página não encontrada.'); }

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $v = valor_parse((string) ($_POST['valor'] ?? ''));
    if ($v !== null && $v > 0) tarefa_valor_salvar($id, $v);
    if (isset($_POST['executar'])) {
        $msg = tarefa_executar($id) ? "Tarefa #$id marcada como executada. O gerente foi avisado." : 'Informe o valor antes de marcar como executada.';
    } else {
        $msg = $v ? 'Valor salvo.' : 'Valor inválido.';
    }
    header('Location: ?k=' . rawurlencode($k) . '&m=' . rawurlencode($msg), true, 303);
    exit;
}
$msg = (string) ($_GET['m'] ?? '');
header('X-Robots-Tag: noindex, nofollow');
?>
<!DOCTYPE html><html lang="pt-BR" translate="no"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow"><meta name="google" content="notranslate"><title>Tarefas · Campeche Lofts</title>
<style>
:root{--teal:#1d4038;--cream:#f1ece1;--line:#d9d3c6;--ink:#173234;--muted:#5d6b6b}
*{box-sizing:border-box}body{margin:0;font:16px/1.5 system-ui,-apple-system,sans-serif;color:var(--ink);background:var(--cream)}
header{background:var(--teal);color:#fff;padding:14px 20px}main{max-width:640px;margin:0 auto;padding:20px 16px 60px}
.card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:16px}
.prio{display:inline-block;font-weight:700;font-size:14px;margin-bottom:6px}
img,video{max-width:100%;border-radius:10px;margin:8px 0;display:block}audio{width:100%;margin:8px 0}
form{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}input{flex:1;min-width:120px;padding:12px;border:1.5px solid var(--line);border-radius:10px;font:inherit;background:var(--cream)}
button{padding:12px 16px;border:0;border-radius:10px;background:var(--teal);color:#fff;font:600 16px inherit;font-family:inherit}button.sec{background:#fff;color:var(--teal);border:1.5px solid var(--teal)}
.ok{background:#e3f1e6;border:1px solid #9bc9a5;padding:12px 16px;border-radius:10px;margin-bottom:16px}.dica{color:var(--muted);font-size:14px}h2{font-size:18px;margin:26px 0 10px}
</style></head><body><header><strong>Campeche Lofts · Tarefas de manutenção</strong></header><main>
<?php
if ($msg) echo '<div class="ok">' . h($msg) . '</div>';
$abertas = tarefas_por_status('aberta');
if (!$abertas) echo '<div class="card">Nenhuma tarefa em aberto. 🎉</div>';
foreach ($abertas as $t) {
    echo '<div class="card"><span class="prio">' . h(PRIORIDADES[$t['prioridade']]) . ' · #' . $t['id'] . '</span>';
    if (trim((string) $t['texto']) !== '') echo '<p>' . nl2br(h($t['texto'])) . '</p>';
    echo midia_html($t, $k);
    echo '<form method="post"><input type="hidden" name="k" value="' . h($k) . '"><input type="hidden" name="id" value="' . $t['id'] . '">'
        . '<input name="valor" inputmode="decimal" placeholder="Valor (R$)" value="' . ($t['valor'] !== null ? h(number_format((float) $t['valor'], 2, ',', '')) : '') . '">'
        . '<button class="sec">Salvar valor</button><button name="executar" value="1">✅ Executada</button></form></div>';
}
$feitas = tarefas_por_status('executada');
if ($feitas) {
    echo '<h2>Executadas — aguardando pagamento</h2>';
    foreach ($feitas as $t) echo '<div class="card"><strong>#' . $t['id'] . '</strong> · ' . h(brl((float) $t['valor'])) . '<br><span class="dica">' . h(resumo($t)) . '</span></div>';
}
?>
<h2>Comandos do Telegram</h2>
<div class="card"><?php if ($bot = cfg('telegram_bot_username')) echo '<p><a href="https://t.me/' . h($bot) . '" target="_blank" rel="noopener">Abrir o bot no Telegram (@' . h($bot) . ')</a></p>'; ?><?= ajuda_html('executor') ?></div>
</main></body></html>
