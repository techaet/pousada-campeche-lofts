<?php
declare(strict_types=1);
require __DIR__ . '/comprovante_lib.php';
exigir_admin();

$msg = $erro = '';
$avisos = $erros = [];
$campos = ['nome', 'documento', 'email', 'idioma', 'loft', 'hospedes', 'checkin', 'checkout', 'total', 'sinal'];
$f = array_fill_keys($campos, '');
$f['idioma'] = 'pt';
$conversa = '';
$resultado = null;
$assumido = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $acao = (string) ($_POST['acao'] ?? '');
    if ($acao === 'ler') {
        $conversa = (string) ($_POST['conversa'] ?? '');
        try {
            $j = groq_extrair($conversa);
            foreach ($campos as $c) if (isset($j[$c]) && !is_array($j[$c])) $f[$c] = (string) $j[$c];
            $assumido = !empty($j['ano_assumido']);
            $msg = 'Conferi a conversa. Revise os campos abaixo (os vazios não estavam na conversa) e gere o comprovante.';
        } catch (Throwable $e) { $erro = $e->getMessage(); }
    } elseif ($acao === 'gerar') {
        foreach ($campos as $c) $f[$c] = trim((string) ($_POST[$c] ?? ''));
        [$dados, $erros, $avisos] = comprovante_validar($f);
        if ($dados && (!$avisos || !empty($_POST['forcar']))) {
            try { $resultado = comprovante_emitir($dados); }
            catch (Throwable $e) { $erro = 'Não consegui gerar o PDF: ' . $e->getMessage(); }
        }
    }
}

pagina_topo('Comprovante de reserva');
if ($resultado) {
    $r = $resultado;
    echo '<div class="ok"><strong>Comprovante ' . h($r['numero']) . ' gerado.</strong><br>'
        . ($r['enviado'] === true ? '✔ Enviado por e-mail ao hóspede.<br>' : ($r['enviado'] === false ? '✖ O e-mail não foi enviado.<br>' : ''))
        . ($r['drive'] ? '✔ Salvo no Drive: <a href="' . h($r['drive']) . '" target="_blank" rel="noopener">abrir pasta</a><br>' : '')
        . '<a href="comprovante_pdf.php?f=' . h(rawurlencode($r['arquivo'])) . '" target="_blank">Abrir o PDF</a></div>';
    foreach ($r['avisos'] as $a) echo '<div class="erro">' . h($a) . '</div>';
    echo '<a class="btn" href="comprovante.php">Fazer outro</a>';
    pagina_fim();
    exit;
}
if ($msg) echo '<div class="ok">' . h($msg) . '</div>';
if ($assumido) echo '<div class="erro">A conversa não dizia o ano das datas: escolhi o próximo ano possível. Confira.</div>';
if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
foreach ($erros as $e) echo '<div class="erro">' . h($e) . '</div>';
foreach ($avisos as $a) echo '<div class="erro">Atenção: ' . h($a) . '</div>';
$i = fn($k, $rot, $extra = '') => '<label>' . $rot . '</label><input name="' . $k . '" value="' . h($f[$k]) . '" ' . $extra . '>';
?>
<form method="post" class="card">
  <?= csrf_campo() ?><input type="hidden" name="acao" value="ler">
  <label for="cv">Cole aqui a conversa do WhatsApp (ou os dados soltos)</label>
  <textarea id="cv" name="conversa" placeholder="Ex.: Maria Silva, CPF 123.456.789-00, loft 5, 2 adultos, 10 a 14/01, total R$ 1.800, maria@email.com"><?= h($conversa) ?></textarea>
  <button class="sec" onclick="this.textContent='Lendo…'">Ler conversa e preencher</button>
</form>

<form method="post" class="card" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='Gerando…'">
  <?= csrf_campo() ?><input type="hidden" name="acao" value="gerar">
  <div class="grid dois">
    <div><?= $i('nome', 'Nome completo', 'required') ?></div>
    <div><?= $i('documento', 'CPF (BR) · DNI/Pasaporte (AR)', 'required') ?></div>
    <div><?= $i('email', 'E-mail do hóspede (vazio = não envia)', 'type="email"') ?></div>
    <div><label>Idioma</label><select name="idioma"><option value="pt"<?= $f['idioma'] === 'pt' ? ' selected' : '' ?>>Português</option><option value="es"<?= $f['idioma'] === 'es' ? ' selected' : '' ?>>Español (argentino)</option></select></div>
    <div><?= $i('loft', 'Loft (3 a 9)', 'type="number" min="3" max="9" required') ?></div>
    <div><?= $i('hospedes', 'Hóspedes', 'placeholder="2 adultos" required') ?></div>
    <div><?= $i('checkin', 'Check-in', 'type="date" required') ?></div>
    <div><?= $i('checkout', 'Check-out', 'type="date" required') ?></div>
    <div><?= $i('total', 'Valor total (R$)', 'inputmode="decimal" required') ?></div>
    <div><?= $i('sinal', 'Sinal (R$) — vazio = 50%', 'inputmode="decimal"') ?></div>
  </div>
  <?php if ($avisos): ?><label style="text-transform:none;font-size:15px"><input type="checkbox" name="forcar" value="1" style="width:auto"> Está certo, gerar mesmo assim</label><?php endif; ?>
  <button>Gerar e enviar comprovante</button>
  <p class="dica">Vai gerar o PDF numerado, enviar ao e-mail do hóspede e salvar na pasta do Drive. Confira tudo antes.</p>
</form>
<?php pagina_fim();
