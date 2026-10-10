<?php
declare(strict_types=1);
require __DIR__ . '/manutencao_lib.php';
exigir_admin();

$aviso = $falha = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'nova') {
    csrf_checar();
    $texto = trim((string) ($_POST['texto'] ?? ''));
    $midias = [];
    try {
        $u = $_FILES['midias'] ?? ['name' => []];
        foreach ((array) $u['name'] as $i => $nome) {
            if ($u['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
            $midias[] = midia_salvar_upload((string) $nome, (string) $u['tmp_name'][$i], (int) $u['error'][$i], (int) $u['size'][$i]);
        }
        if ($texto === '' && !$midias) throw new RuntimeException('Escreva a demanda ou anexe uma foto, vídeo ou áudio.');
        $t = tarefa_criar(0, $texto, $midias, (int) ($_POST['prioridade'] ?? 2));
        $aviso = "Demanda #{$t['id']} registrada. O executor a vê em /tarefas no Telegram e na página dele.";
    } catch (Throwable $e) {
        foreach ($midias as $m) @unlink(DADOS . '/midia/' . $m['arquivo']);  // não deixa meia demanda no disco
        $falha = $e->getMessage();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $todas = array_merge(tarefas_por_status('executada'), tarefas_por_status('paga'));
    $aviso = planilha_enviar($todas) ? 'Planilha atualizada com ' . count($todas) . ' tarefa(s).' : 'Não consegui atualizar a planilha. Confira o Apps Script em Configurações.';
}
pagina_topo('Manutenção');
if ($aviso) echo '<div class="ok">' . h($aviso) . '</div>';
if ($falha) echo '<div class="erro">' . h($falha) . '</div>';
[$q, $soma] = total_em_aberto();
$link = 'https://www.campechelofts.floripa.br/manutencao/?k=' . cfg('executor_token');
?>
<form method="post" enctype="multipart/form-data" class="card">
  <?= csrf_campo() ?><input type="hidden" name="acao" value="nova">
  <strong>Nova demanda de manutenção</strong>
  <label for="tx">O que precisa ser feito</label>
  <textarea id="tx" name="texto" placeholder="Ex.: Torneira da cozinha do loft 4 está pingando"><?= h($_POST['texto'] ?? '') ?></textarea>
  <label for="md">Fotos, vídeos ou áudio (opcional)</label>
  <input id="md" type="file" name="midias[]" multiple accept="image/*,video/*,audio/*">
  <p class="dica">Até 50 MB por arquivo (o servidor aceita no máximo <?= h(ini_get('upload_max_filesize')) ?> por arquivo).</p>
  <label for="pr">Prioridade</label>
  <select id="pr" name="prioridade"><?php foreach (PRIORIDADES as $n => $nome) echo '<option value="' . $n . '"' . ($n === 2 ? ' selected' : '') . '>' . h($nome) . '</option>'; ?></select>
  <button onclick="if(this.form.checkValidity()){this.textContent='Enviando…'}">Registrar demanda</button>
</form>
<div class="card"><strong>Em aberto para pagamento: <?= h(brl($soma)) ?></strong> <span class="dica">(<?= $q ?> tarefa<?= $q === 1 ? '' : 's' ?> executada<?= $q === 1 ? '' : 's' ?>)</span>
  <p class="dica">Para pagar, use /pagar no bot do Telegram e envie o comprovante.</p></div>
<div class="card"><strong>Planilha de custos (Drive)</strong>
  <p class="dica">Cada tarefa executada vira uma linha; quando é paga, a linha muda para "Paga".</p>
  <?php if (cfg('planilha_url')): ?><a href="<?= h(cfg('planilha_url')) ?>" target="_blank" rel="noopener">Abrir a planilha</a><?php else: ?><span class="dica">Ainda não criada (nasce na primeira tarefa executada).</span><?php endif; ?>
  <form method="post"><?= csrf_campo() ?><button class="sec">Atualizar planilha com tudo (executadas e pagas)</button></form></div>
<div class="card"><label>Link do executor (envie só para ele)</label><input readonly value="<?= h($link) ?>" onfocus="this.select()"></div>
<?php
foreach (['aberta' => 'Em aberto', 'executada' => 'Executadas (a pagar)', 'paga' => 'Pagas'] as $st => $titulo) {
    $l = tarefas_por_status($st);
    if ($st === 'paga') $l = array_reverse($l);
    echo '<h2>' . h($titulo) . ' (' . count($l) . ')</h2>';
    foreach ($l as $t) {
        echo '<div class="card"><strong>#' . $t['id'] . ' · ' . h(PRIORIDADES[$t['prioridade']]) . '</strong>'
            . ($t['valor'] !== null ? ' · ' . h(brl((float) $t['valor'])) : '');
        if (trim((string) $t['texto']) !== '') echo '<p>' . nl2br(h($t['texto'])) . '</p>';
        echo midia_html($t, '') . '</div>';
    }
}
echo '<style>img,video{max-width:100%;border-radius:10px;display:block;margin:8px 0}audio{width:100%}h2{font-size:18px;margin:26px 0 10px}</style>';
pagina_fim();
