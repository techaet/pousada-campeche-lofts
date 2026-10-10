<?php
declare(strict_types=1);
require __DIR__ . '/cotacao_lib.php';
exigir_admin();
set_time_limit(120);  // o robô leva alguns segundos para responder

$texto = '';
$fone = '';
$erro = '';
$res = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $texto = trim((string) ($_POST['texto'] ?? ''));
    $fone = (string) ($_POST['whatsapp'] ?? '');
    try {
        if ($texto === '') throw new RuntimeException('Cole o texto do lead.');
        $res = cotacao_pedir_ao_robo($texto);
        if ($fone === '') $fone = (string) telefone_do_texto($texto);  // só preenche se você não digitou o número
    } catch (Throwable $e) { $erro = $e->getMessage(); }
}

pagina_topo('Cotação para lead');
if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
?>
<form method="post" class="card" onsubmit="var b=this.querySelector('button');b.disabled=true;b.textContent='O robô está respondendo…'">
  <?= csrf_campo() ?>
  <label for="tx">Cole aqui o texto do lead (a ficha do anúncio ou a conversa)</label>
  <textarea id="tx" name="texto" style="min-height:220px" placeholder="¿Cuándo pensás viajar este verano?&#10;Enero/2027&#10;¿Cómo viene tu grupo?&#10;Pareja con 2 o más hijos/as&#10;First name&#10;…&#10;Phone number&#10;+54…" required><?= h($texto) ?></textarea>
  <label for="fn">WhatsApp do lead (deixe vazio para eu achar no texto)</label>
  <input id="fn" name="whatsapp" value="<?= h($fone) ?>" inputmode="tel" placeholder="+54 9 11 2345-6789">
  <button>Pedir a cotação ao robô</button>
  <p class="dica">O robô responde como responderia a um lead no WhatsApp (idioma, regras e modelo dele). Nada fica gravado no robô.</p>
</form>
<?php if ($res): $f = telefone_normalizar($fone); ?>
<div class="card">
  <p class="dica">Resposta do robô (<?= h($res['reason'] ?: $res['decision']) ?>). Pode editar antes de enviar.</p>
  <textarea id="msg" style="min-height:420px"><?= h($res['text']) ?></textarea>
  <?php if ($f): ?>
    <a class="btn" id="wa" target="_blank" rel="noopener" data-fone="<?= h($f) ?>" href="https://wa.me/<?= h($f) ?>?text=<?= h(rawurlencode($res['text'])) ?>">Abrir no WhatsApp do lead (+<?= h($f) ?>)</a>
  <?php else: ?>
    <p class="erro">Não achei um WhatsApp válido. Digite o número com DDI acima e peça de novo, ou copie a mensagem.</p>
  <?php endif; ?>
  <button type="button" class="sec" id="copiar">Copiar mensagem</button>
</div>
<script>
(function () {
  var t = document.getElementById('msg'), a = document.getElementById('wa');
  if (a) a.addEventListener('click', function () { a.href = 'https://wa.me/' + a.dataset.fone + '?text=' + encodeURIComponent(t.value); });
  document.getElementById('copiar').addEventListener('click', function () { var b = this; navigator.clipboard.writeText(t.value).then(function () { b.textContent = 'Copiado ✔'; }); });
})();
</script>
<?php endif; ?>
<?php pagina_fim();
