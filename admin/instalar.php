<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
admin_sessao();

if (cfg('admin_hash')) { header('Location: /admin/', true, 302); exit; }

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $senha = (string) ($_POST['senha'] ?? '');
    if (tentativas_bloqueado()) {
        $erro = 'Muitas tentativas. Espere 15 minutos.';
    } elseif (!hash_equals(CODIGO_INSTALACAO_SHA256, hash('sha256', trim((string) ($_POST['codigo'] ?? ''))))) {
        tentativa_falha();
        $erro = 'Código de instalação incorreto.';
    } elseif (strlen($senha) < 10) {
        $erro = 'A senha precisa ter pelo menos 10 caracteres.';
    } elseif ($senha !== (string) ($_POST['repetir'] ?? '')) {
        $erro = 'As senhas não são iguais.';
    } else {
        cfg_salvar([
            'admin_hash' => password_hash($senha, PASSWORD_DEFAULT),
            'executor_token' => bin2hex(random_bytes(16)),
            'telegram_secret' => bin2hex(random_bytes(16)),
        ]);
        header('Location: /admin/', true, 303);
        exit;
    }
}

pagina_topo('Primeiro acesso', false);
if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
?>
<form method="post" class="card" autocomplete="off">
  <?= csrf_campo() ?>
  <p class="dica">Digite o código de instalação (que você recebeu no chat) e escolha a senha do painel.</p>
  <label for="c">Código de instalação</label><input id="c" name="codigo" required>
  <label for="s">Nova senha do painel (mín. 10 caracteres)</label><input id="s" type="password" name="senha" minlength="10" required>
  <label for="r">Repita a senha</label><input id="r" type="password" name="repetir" minlength="10" required>
  <button>Criar painel</button>
</form>
<?php pagina_fim();
