<?php
declare(strict_types=1);
require __DIR__ . '/manutencao_lib.php';
exigir_admin();

pagina_topo('Manutenção');
[$q, $soma] = total_em_aberto();
$link = 'https://www.campechelofts.floripa.br/manutencao/?k=' . cfg('executor_token');
?>
<div class="card"><strong>Em aberto para pagamento: <?= h(brl($soma)) ?></strong> <span class="dica">(<?= $q ?> tarefa<?= $q === 1 ? '' : 's' ?> executada<?= $q === 1 ? '' : 's' ?>)</span>
  <p class="dica">Para pagar, use /pagar no bot do Telegram e envie o comprovante.</p></div>
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
