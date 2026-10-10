<?php
declare(strict_types=1);
require __DIR__ . '/lib.php';
exigir_admin();

$msg = $erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $nova = (string) ($_POST['nova'] ?? '');
    if (strlen($nova) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($nova !== (string) ($_POST['repetir'] ?? '')) {
        $erro = 'As senhas não são iguais.';
    } else {
        // guia/session.php lê este arquivo; o deploy nunca o sobrescreve
        $f = DADOS . '/guia_hash.txt';
        file_put_contents($f, password_hash($nova, PASSWORD_DEFAULT), LOCK_EX);
        @chmod($f, 0600);
        $msg = 'Senha do guia trocada. Vale já para o próximo login; quem já está dentro continua até fechar o navegador.';
    }
}

pagina_topo('Senha do Guia do Hóspede');
if ($msg) echo '<div class="ok">' . h($msg) . '</div>';
if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
?>
<form method="post" class="card" autocomplete="off">
  <?= csrf_campo() ?>
  <label for="n">Nova senha</label><input id="n" type="password" name="nova" minlength="8" required>
  <label for="r">Repita a nova senha</label><input id="r" type="password" name="repetir" minlength="8" required>
  <p class="dica">Esta é a senha que os hóspedes digitam em campechelofts.floripa.br/guia/. Lembre de avisar a equipe e atualizar o texto de boas-vindas, se a senha estiver nele.</p>
  <button>Trocar senha</button>
</form>
<?php pagina_fim();
