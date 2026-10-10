<?php
declare(strict_types=1);
require __DIR__ . '/comprovante_lib.php';  // groq_json
require __DIR__ . '/cotacao_lib.php';
exigir_admin();

$campos = ['nome', 'whatsapp', 'checkin', 'checkout', 'adultos', 'criancas', 'idioma'];
$f = array_fill_keys($campos, '');
$conversa = '';
$msg = $erro = $aviso = '';
$res = null;
$idiomaConversa = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $acao = (string) ($_POST['acao'] ?? '');
    $conversa = (string) ($_POST['conversa'] ?? '');
    if ($acao === 'ler') {
        $f['idioma'] = (string) ($_POST['idioma'] ?? '');
        try {
            $j = groq_json("Você lê o texto de um lead de uma pousada em Florianópolis (conversa de WhatsApp ou dados soltos) e extrai o pedido de cotação. Hoje é " . date('Y-m-d') . ".\n"
                . "Responda SOMENTE um objeto JSON com: nome (nome do lead), telefone (WhatsApp do lead exatamente como aparece, com + e código do país se houver), "
                . "checkin e checkout (AAAA-MM-DD; datas escritas assim 10/01 são dia/mês), adultos (inteiro: pessoas com 18 anos ou mais; se o texto só diz 'N pessoas', conte todas como adultas), "
                . "criancas (lista com a IDADE de cada criança/menor, ex.: [8, 14]; use null no lugar da idade que não foi dita), "
                . "idioma_conversa ('pt', 'es' ou 'en', conforme o idioma em que o lead escreve), ano_assumido (true se o ano não estava no texto e você escolheu o próximo ano futuro possível).\n"
                . "Use null para o que NÃO estiver claro no texto. Nunca invente nome, telefone, datas ou idades.", $conversa);
            $f['nome'] = (string) ($j['nome'] ?? '');
            $f['whatsapp'] = (string) ($j['telefone'] ?? '');
            $f['checkin'] = (string) ($j['checkin'] ?? '');
            $f['checkout'] = (string) ($j['checkout'] ?? '');
            $f['adultos'] = isset($j['adultos']) ? (string) (int) $j['adultos'] : '';
            $f['criancas'] = implode(', ', array_map(fn($i) => $i === null ? '?' : (string) (int) $i, (array) ($j['criancas'] ?? [])));
            $idiomaConversa = in_array($j['idioma_conversa'] ?? '', ['pt', 'es', 'en'], true) ? $j['idioma_conversa'] : null;
            if ($idiomaConversa) $f['conversa_idioma'] = $idiomaConversa;
            if (!empty($j['ano_assumido'])) $aviso = 'O texto não dizia o ano das datas: escolhi o próximo ano possível. Confira.';
            $msg = 'Li o texto. Confira os campos abaixo (os vazios não estavam no texto) e clique em "Calcular cotação".';
        } catch (Throwable $e) { $erro = $e->getMessage(); }
    } elseif ($acao === 'atualizar_tarifario') {
        try { tarifario_carregar(true); $msg = 'Tarifário atualizado a partir da planilha.'; } catch (Throwable $e) { $erro = $e->getMessage(); }
    } elseif ($acao === 'calcular') {
        foreach ($campos as $c) $f[$c] = trim((string) ($_POST[$c] ?? ''));
        $idiomaConversa = in_array($_POST['conversa_idioma'] ?? '', ['pt', 'es', 'en'], true) ? $_POST['conversa_idioma'] : null;
        try {
            $ci = data_flex($f['checkin']); $co = data_flex($f['checkout']);
            if (!$ci || !$co) throw new RuntimeException('Informe as datas de check-in e check-out.');
            $idades = [];
            foreach (array_filter(array_map('trim', explode(',', $f['criancas'])), fn($x) => $x !== '') as $x) {
                if (!ctype_digit($x) || (int) $x > 17) throw new RuntimeException('Informe a idade de cada criança em números (ex.: 8, 14). Idade que não sei: pergunte ao lead.');
                $idades[] = (int) $x;
            }
            $t = tarifario_carregar();
            if (!empty($t['aviso'])) $aviso = $t['aviso'];
            $c = cotacao_calcular(tarifario_interpretar($t), $ci, $co, (int) $f['adultos'], $idades);
            $fone = telefone_normalizar($f['whatsapp']);
            $idioma = in_array($f['idioma'], ['pt', 'es', 'en'], true) ? $f['idioma'] : (idioma_do_ddi($fone) ?? $idiomaConversa ?? 'pt');
            $res = ['c' => $c, 'fone' => $fone, 'idioma' => $idioma, 'origem' => in_array($f['idioma'], ['pt', 'es', 'en'], true) ? 'escolhido por você' : ($fone ? 'pelo DDI do número' : 'pelo idioma do texto'),
                'texto' => cotacao_mensagem($idioma, $f['nome'], $c['noites'], $ci, $co, composicao_texto($idioma, (int) $f['adultos'], $idades), $c['total'])];
        } catch (Throwable $e) { $erro = $e->getMessage(); }
    }
}

pagina_topo('Cotação para lead');
if ($msg) echo '<div class="ok">' . h($msg) . '</div>';
if ($aviso) echo '<div class="erro">' . h($aviso) . '</div>';
if ($erro) echo '<div class="erro">' . h($erro) . '</div>';
$i = fn($k, $rot, $extra = '') => '<label>' . $rot . '</label><input name="' . $k . '" value="' . h($f[$k]) . '" ' . $extra . '>';
$sel = fn($v) => $f['idioma'] === $v ? ' selected' : '';
?>
<form method="post" class="card">
  <?= csrf_campo() ?><input type="hidden" name="acao" value="ler">
  <label for="cv">Cole aqui o texto do lead (conversa ou dados soltos)</label>
  <textarea id="cv" name="conversa" placeholder="Ex.: Juan Zambrano +54 9 11 2345-6789, 2 adultos, del 20 al 30 de noviembre"><?= h($conversa) ?></textarea>
  <label>Idioma da cotação</label>
  <select name="idioma"><option value="">Automático (pelo DDI do WhatsApp)</option><option value="pt"<?= $sel('pt') ?>>Português</option><option value="es"<?= $sel('es') ?>>Español</option><option value="en"<?= $sel('en') ?>>English</option></select>
  <button class="sec" onclick="this.textContent='Lendo…'">Ler texto e preencher</button>
</form>

<form method="post" class="card">
  <?= csrf_campo() ?><input type="hidden" name="acao" value="calcular"><input type="hidden" name="conversa" value="<?= h($conversa) ?>">
  <input type="hidden" name="conversa_idioma" value="<?= h($f['conversa_idioma'] ?? ($idiomaConversa ?? '')) ?>">
  <div class="grid dois">
    <div><?= $i('nome', 'Nome do lead') ?></div>
    <div><?= $i('whatsapp', 'WhatsApp do lead (com DDI)', 'inputmode="tel" placeholder="+54 9 11 2345-6789"') ?></div>
    <div><?= $i('checkin', 'Check-in', 'type="date" required') ?></div>
    <div><?= $i('checkout', 'Check-out', 'type="date" required') ?></div>
    <div><?= $i('adultos', 'Adultos (18+)', 'type="number" min="1" max="3" required') ?></div>
    <div><?= $i('criancas', 'Idades das crianças (ex.: 8, 14)', 'placeholder="vazio = sem crianças"') ?></div>
    <div><label>Idioma</label><select name="idioma"><option value="">Automático (pelo DDI)</option><option value="pt"<?= $sel('pt') ?>>Português</option><option value="es"<?= $sel('es') ?>>Español</option><option value="en"<?= $sel('en') ?>>English</option></select></div>
  </div>
  <button>Calcular cotação</button>
</form>
<?php if ($res): $c = $res['c']; ?>
<div class="card">
  <strong>Total para o lead: <?= h(reais($c['total'])) ?></strong>
  <p class="dica">Só você vê isto: <?= $c['noites'] ?> noite(s), tarifa "<?= h($c['coluna']) ?>"<?php foreach ($c['linhas'] as $l) echo ' · ' . h($l['noites'] . '× ' . reais($l['diaria']) . ' (' . $l['periodo'] . ')'); ?>
    = <?= h(reais($c['diarias'])) ?><?= $c['acrescimo'] ? ' + acréscimo interno de ' . $c['acrescimo_pct'] . '% (soma das idades ' . $c['soma_idades'] . ') = ' . h(reais($c['acrescimo'])) : '' ?> + taxa de limpeza embutida <?= h(reais($c['limpeza'])) ?>.</p>
  <p class="dica">Idioma da mensagem: <strong><?= h(['pt' => 'Português', 'es' => 'Español', 'en' => 'English'][$res['idioma']]) ?></strong> (<?= h($res['origem']) ?>).</p>
  <label for="msg">Mensagem pronta (pode editar antes de enviar)</label>
  <textarea id="msg" style="min-height:360px"><?= h($res['texto']) ?></textarea>
  <?php if ($res['fone']): ?>
    <a class="btn" id="wa" target="_blank" rel="noopener" data-fone="<?= h($res['fone']) ?>" href="https://wa.me/<?= h($res['fone']) ?>?text=<?= h(rawurlencode($res['texto'])) ?>">Abrir no WhatsApp do lead (+<?= h($res['fone']) ?>)</a>
  <?php else: ?>
    <p class="erro">Não achei um WhatsApp válido no texto. Digite o número com DDI acima e calcule de novo, ou copie a mensagem.</p>
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
<form method="post" class="card"><?= csrf_campo() ?><input type="hidden" name="acao" value="atualizar_tarifario">
  <span class="dica">O tarifário vem da planilha Campeche Automation (aba tarifario) e é guardado por 10 minutos.</span>
  <button class="sec">Atualizar tarifário agora</button></form>
<?php pagina_fim();
