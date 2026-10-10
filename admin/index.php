<?php
declare(strict_types=1);
require __DIR__ . '/manutencao_lib.php';
admin_sessao();

if (!cfg('admin_hash')) { header('Location: instalar.php', true, 302); exit; }

if (isset($_GET['sair'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: /admin/', true, 302);
    exit;
}

$erro = '';
if (empty($_SESSION['admin']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (tentativas_bloqueado()) {
        $erro = 'Muitas tentativas. Espere 15 minutos.';
    } elseif (password_verify((string) ($_POST['senha'] ?? ''), (string) cfg('admin_hash'))) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: /admin/', true, 303);
        exit;
    } else {
        tentativa_falha();
        $erro = 'Senha incorreta.';
    }
}

if (empty($_SESSION['admin'])) {
    pagina_topo('Entrar', false);
    if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
    echo '<form method="post" class="card"><label for="s">Senha</label><input id="s" type="password" name="senha" autocomplete="current-password" autofocus required>'
        . '<button>Entrar</button></form>';
    pagina_fim();
    exit;
}

bot_username_garantir();
pagina_topo('Painel');
?>
<div class="grid">
  <a class="card item" href="senha.php"><strong>Senha do Guia do Hóspede</strong><span class="dica">Trocar a senha que os hóspedes usam em /guia/</span></a>
  <a class="card item" href="comprovante.php"><strong>Comprovante de reserva</strong><span class="dica">Colar a conversa, conferir e enviar ao hóspede</span></a>
  <a class="card item" href="manutencao.php"><strong>Manutenção</strong><span class="dica">Tarefas, valores e pagamentos</span></a>
  <a class="card item" href="config.php"><strong>Configurações</strong><span class="dica">Bot do Telegram, Groq e envio de e-mail</span></a>
</div>
<p class="dica">PHP <?= h(PHP_VERSION) ?></p>
<?php pagina_fim();
