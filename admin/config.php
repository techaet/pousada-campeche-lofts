<?php
declare(strict_types=1);
require __DIR__ . '/manutencao_lib.php';
exigir_admin();

$msg = $erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $novo = [];
    // segredos: campo vazio = manter o que já está salvo
    foreach (['telegram_token', 'groq_key', 'apps_script_segredo', 'n8n_cotacao_segredo'] as $k) {
        $v = trim((string) ($_POST[$k] ?? ''));
        if ($v !== '') $novo[$k] = $v;
    }
    foreach (['groq_model', 'apps_script_url', 'n8n_cotacao_url'] as $k) $novo[$k] = trim((string) ($_POST[$k] ?? ''));
    if ($novo['apps_script_url'] !== '' && !preg_match('#^https://script\.google\.com/#', $novo['apps_script_url'])) {
        $erro = 'O endereço do Apps Script deve começar com https://script.google.com/';
    } else {
        $novo['gerentes'] = array_values(array_filter(array_map('intval', preg_split('/[\s,;]+/', (string) ($_POST['gerentes'] ?? '')) ?: [])));
        $novo['executor_id'] = (int) trim((string) ($_POST['executor_id'] ?? ''));
        cfg_salvar($novo);
        if (isset($novo['telegram_token'])) cfg_salvar(['telegram_bot_username' => '']);
        bot_username_garantir();
        $msg = 'Configurações salvas.';
        if (isset($_POST['ativar_bot'])) {
            $r = tg('setWebhook', ['url' => 'https://www.campechelofts.floripa.br/manutencao/bot.php', 'secret_token' => cfg('telegram_secret'),
                'allowed_updates' => ['message', 'callback_query'], 'drop_pending_updates' => true]);
            $msg .= $r ? ' Bot ativado.' : '';
            if (!$r) $erro = 'Não consegui ativar o bot: confira o token do Telegram.';
        }
    }
}
$salvo = fn($k) => cfg($k) ? '•••••• (salvo — deixe vazio para manter)' : 'cole aqui';
$info = cfg('telegram_token') ? tg('getWebhookInfo') : null;
pagina_topo('Configurações');
if ($msg) echo '<div class="ok">' . h($msg) . '</div>';
if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
?>
<form method="post" autocomplete="off">
  <?= csrf_campo() ?>
  <div class="card"><strong>Bot do Telegram (manutenção)</strong>
    <label>Token do bot (do @BotFather)</label><input type="password" name="telegram_token" placeholder="<?= h($salvo('telegram_token')) ?>">
    <label>IDs dos gerentes (separe por vírgula)</label><input name="gerentes" value="<?= h(implode(', ', gerentes())) ?>" placeholder="651261392, 6607106176">
    <label>ID do executor (prestador) — vazio até ter</label><input name="executor_id" value="<?= executor() ?: '' ?>" inputmode="numeric">
    <label style="text-transform:none;font-size:15px;margin-top:16px"><input type="checkbox" name="ativar_bot" value="1" style="width:auto"> Ativar/atualizar o bot ao salvar (necessário na 1ª vez e se trocar o token)</label>
    <?php if ($info): ?><p class="dica">Situação do webhook: <?= !empty($info['url']) ? '✔ ativo' : '✖ não ativado' ?><?= !empty($info['last_error_message']) ? ' · último erro: ' . h($info['last_error_message']) : '' ?></p><?php endif; ?>
  </div>
  <div class="card"><strong>Leitura da conversa (Groq)</strong>
    <label>Chave da API do Groq</label><input type="password" name="groq_key" placeholder="<?= h($salvo('groq_key')) ?>">
    <label>Modelo</label><input name="groq_model" value="<?= h(cfg('groq_model', '')) ?>" placeholder="vazio = escolho sozinho o melhor liberado">
  </div>
  <div class="card"><strong>Envio do comprovante (Google Apps Script)</strong>
    <label>Endereço da implantação (…/exec)</label><input name="apps_script_url" value="<?= h(cfg('apps_script_url', '')) ?>" placeholder="https://script.google.com/macros/s/…/exec">
    <label>Segredo (o mesmo que está no script)</label><input type="password" name="apps_script_segredo" placeholder="<?= h($salvo('apps_script_segredo')) ?>">
  </div>
  <div class="card"><strong>Cotação pelo robô (n8n)</strong>
    <label>Endereço do webhook da cotação</label><input name="n8n_cotacao_url" value="<?= h(cfg('n8n_cotacao_url', '')) ?>" placeholder="https://…app.n8n.cloud/webhook/painel-cotacao">
    <label>Segredo (o mesmo cadastrado no n8n)</label><input type="password" name="n8n_cotacao_segredo" placeholder="<?= h($salvo('n8n_cotacao_segredo')) ?>">
  </div>
  <button>Salvar</button>
</form>
<?php pagina_fim();
