<?php
declare(strict_types=1);
require __DIR__ . '/session.php';

if (!empty($_SESSION['guide_authenticated'])) {
    header('Location: index.php', true, 302);
    exit;
}

$error = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if (password_verify($password, GUIDE_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['guide_authenticated'] = true;
        $_SESSION['guide_failures'] = 0;
        header('Location: index.php', true, 303);
        exit;
    }

    $_SESSION['guide_failures'] = min(5, (int) ($_SESSION['guide_failures'] ?? 0) + 1);
    usleep((int) $_SESSION['guide_failures'] * 250000);
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" translate="no">
<head>
  <meta charset="UTF-8">
  <meta name="google" content="notranslate">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Acesso ao Guia do Hóspede | Campeche Lofts</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=Playfair+Display:opsz,wght@5..1200,500;5..1200,600;5..1200,700&display=swap"><link rel="stylesheet" href="campeche.css?v=20260820-guia-protegido-v1">
  <style>
    .guide-login { min-height: 100svh; display: grid; grid-template-rows: auto 1fr auto; background: var(--paper); }
    .guide-login-main { display: grid; place-items: center; padding: clamp(2rem, 6vw, 4.5rem) 24px; }
    .guide-login-card { width: min(100%, 520px); padding: clamp(1.75rem, 5vw, 3.25rem); background: var(--white); border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
    .guide-login-card h1 { margin: 0; font-family: "Playfair Display", Georgia, serif; font-size: clamp(2.2rem, 6vw, 3.7rem); font-weight: 600; letter-spacing: -.05em; line-height: .96; }
    .guide-login-card h1 em { color: var(--ocean); font-style: italic; }
    .guide-login-card p { margin: 1rem 0 0; color: var(--ink-soft); }
    .guide-login-form { display: grid; gap: .8rem; margin-top: 2rem; }
    .guide-login-form label { color: var(--ink); font-size: .85rem; font-weight: 700; }
    .guide-login-form input { min-height: 52px; width: 100%; padding: .8rem 1rem; color: var(--ink); background: var(--paper); border: 1px solid var(--line); border-radius: 12px; outline: none; }
    .guide-login-form input:focus { border-color: var(--ocean); box-shadow: 0 0 0 3px rgba(31,98,112,.12); }
    .guide-login-form .button { width: 100%; margin-top: .5rem; }
    .guide-login-error { margin: 1rem 0 0 !important; padding: .8rem 1rem; color: #8c2f23 !important; background: #fff0ec; border-left: 3px solid #bd553f; font-size: .88rem; }
    .guide-login-footer { padding: 1.25rem 24px 1.75rem; color: var(--ink-soft); font-size: .75rem; text-align: center; }
  </style>
</head>
<body class="guide-login">
  <header class="site-header guide-header">
    <div class="header-inner">
      <a class="brand" href="../index.html" aria-label="Campeche Lofts — página inicial"><span class="brand-logo-frame"><img class="brand-logo" src="images/logo_campeche_lofts_horizontal.webp" alt="Campeche Lofts" width="460" height="307"></span></a>
    </div>
  </header>
  <main class="guide-login-main">
    <section class="guide-login-card" aria-labelledby="titulo-acesso">
      <p class="eyebrow">Área do hóspede</p>
      <h1 id="titulo-acesso">Guia do<br><em>hóspede.</em></h1>
      <p>Informe a senha recebida para consultar as orientações da sua estadia.</p>
      <?php if ($error): ?>
        <p class="guide-login-error" role="alert">Senha incorreta. Confira a informação recebida e tente novamente.</p>
      <?php endif; ?>
      <form class="guide-login-form" method="post" action="login.php">
        <label for="password">Senha de acesso</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required autofocus>
        <button class="button" type="submit">Acessar o guia</button>
      </form>
    </section>
  </main>
  <footer class="guide-login-footer">Campeche Lofts · Rua das Corticeiras, 270 · Florianópolis/SC</footer>
</body>
</html>
