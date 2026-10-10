<?php
declare(strict_types=1);
require __DIR__ . '/manutencao_lib.php';
exigir_admin();

$aviso = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_checar();
    $todas = array_merge(tarefas_por_status('executada'), tarefas_por_status('paga'));
    $aviso = planilha_enviar($todas) ? 'Planilha atualizada com ' . count($todas) . ' tarefa(s).' : 'Não consegui atualizar a planilha. Confira o Apps Script em Configurações.';
}
pagina_topo('Manutenção');
if ($aviso) echo '<div class="ok">' . h($aviso) . '</div>';
[$q, $soma] = total_em_aberto();
$link = 'https://www.campechelofts.floripa.br/manutencao/?k=' . cfg('executor_token');
?>
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
