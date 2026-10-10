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
  <p class="dica">Resposta do robô (<?= h($res['reason'] ?: $res['decision']) ?>). Pode editar antes de enviar.<?= $res['completou'] ? ' Acrescentei no fim o bloco comercial (promoção, Superhost, pousada, fotos e o link para falar com você), que o robô não põe nas fichas só com o mês.' : '' ?></p>
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
<div class="card" id="trad-card">
  <strong>Tradutor</strong>
  <label for="trad-txt">Digite o texto (em qualquer idioma)</label>
  <textarea id="trad-txt" style="min-height:120px" placeholder="Ex.: Oi Flavio! Já te mando as datas disponíveis."></textarea>
  <label for="trad-idioma">Traduzir para</label>
  <select id="trad-idioma"><?php foreach (IDIOMAS_TRADUCAO as $cod => $i) echo '<option value="' . h($cod) . '">' . h($i[0]) . '</option>'; ?></select>
  <button type="button" id="trad-ir">Traduzir</button>
  <div id="trad-saida" hidden>
    <label for="trad-res">Tradução (pode editar)</label>
    <textarea id="trad-res" style="min-height:120px"></textarea>
    <button type="button" class="sec" id="trad-copiar">Copiar</button>
    <button type="button" class="sec" id="trad-add" hidden>Acrescentar à mensagem da cotação</button>
  </div>
  <p class="erro" id="trad-erro" hidden></p>
  <input type="hidden" id="trad-csrf" value="<?= h($_SESSION['csrf'] ?? '') ?>">
</div>
<script>
(function () {
  var $ = function (i) { return document.getElementById(i); }, msg = $('msg');
  if (msg) $('trad-add').hidden = false;
  $('trad-ir').addEventListener('click', function () {
    var b = this, fd = new FormData();
    fd.append('csrf', $('trad-csrf').value); fd.append('texto', $('trad-txt').value); fd.append('idioma', $('trad-idioma').value);
    $('trad-erro').hidden = true; b.disabled = true; b.textContent = 'Traduzindo…';
    fetch('traduzir.php', { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        if (!j.ok) throw new Error(j.erro || 'Não consegui traduzir.');
        $('trad-res').value = j.traducao; $('trad-saida').hidden = false;
      })
      .catch(function (e) { $('trad-erro').textContent = e.message; $('trad-erro').hidden = false; })
      .then(function () { b.disabled = false; b.textContent = 'Traduzir'; });
  });
  $('trad-copiar').addEventListener('click', function () { var b = this; navigator.clipboard.writeText($('trad-res').value).then(function () { b.textContent = 'Copiado ✔'; setTimeout(function () { b.textContent = 'Copiar'; }, 1500); }); });
  $('trad-add').addEventListener('click', function () { if (msg) { msg.value = msg.value.replace(/\s+$/, '') + '\n\n' + $('trad-res').value; msg.scrollIntoView({ behavior: 'smooth' }); } });
})();
</script>
<?php pagina_fim();
