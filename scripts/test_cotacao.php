<?php
// Teste da cotação com o tarifário real (cópia de out/2026). Rodar: php scripts/test_cotacao.php
require __DIR__ . '/../admin/cotacao_lib.php';

$t = tarifario_interpretar(['tarifario' => [
    ['Tarifa', 'date_start', 'date_end', '1 adulto', '2 adultos', '3 adultos', '2 adultos + 1 criança', '2 adultos + 2 crianças'],
    ['Inverno (maio até outubro)', '2026-05-01', '2026-10-31', '15000', '25000', '35000', '25000', '30000'],
    ['Primavera (novembro até dezembro)', '2026-10-31', '2026-12-23', '18000', '28000', '38000', '28000', '35000'],
    ['Dezembro (início alta temporada)', '2026-12-23', '2026-12-30', '40000', '49000', '58000', '55000', '58000'],
    ['Virada de Ano', '2026-12-30', '2027-01-03', '75000', '95000', '125000', '115000', '125000'],
], 'configuracao' => [['chave', 'valor'], ['cleaning_fee_cents', '15000'], ['child_age_sum_surcharge_threshold', '18'], ['child_age_sum_surcharge_percent', '20']]]);
$d = fn($s) => new DateTimeImmutable($s);
$total = fn($ci, $co, $a, $i = []) => cotacao_calcular($t, $d($ci), $d($co), $a, $i)['total'];
$erra = function (callable $f) { try { $f(); return false; } catch (RuntimeException) { return true; } };

assert($total('2026-11-20', '2026-11-30', 2) === 295000, 'exemplo do Leonardo: R$ 2.950,00');
assert($total('2026-10-29', '2026-11-02', 2) === 121000, 'virada de período: 2×250 + 2×280 + limpeza');
assert($total('2026-11-20', '2026-11-21', 2) === 28000 + 15000, '1 noite');
assert($total('2026-11-20', '2026-11-21', 2, [8]) === 28000 + 15000, 'criança de 8 usa tarifa de 2 adultos');
assert(cotacao_calcular($t, $d('2026-11-20'), $d('2026-11-21'), 2, [14])['coluna'] === '2 adultos + 1 criança', '14 anos = criança na composição');
assert($total('2026-11-20', '2026-11-21', 2, [14]) === 28000 + 15000, '14 anos: soma 14 ≤ 18, sem acréscimo');
assert($total('2026-11-20', '2026-11-21', 2, [13, 15]) === 35000 + 7000 + 15000, '13+15=28 > 18: +20% nas diárias');
assert(cotacao_calcular($t, $d('2026-11-20'), $d('2026-11-21'), 2, [16])['coluna'] === '3 adultos', '16+ vira adulto');
assert($total('2026-11-20', '2026-11-21', 2, [5, 7]) === 28000 + 15000, 'duas crianças pequenas: sem adicional');
assert($erra(fn() => $total('2027-06-01', '2027-06-03', 2)), 'fora do tarifário');
assert($erra(fn() => $total('2026-11-20', '2026-11-20', 2)), 'check-out igual ao check-in');
assert($erra(fn() => $total('2026-11-20', '2026-11-22', 4)), '4 adultos sem tarifa');

assert(telefone_normalizar('+54 9 11 2345-6789') === '5491123456789');
assert(telefone_normalizar('+54 11 2345-6789') === '5491123456789', 'AR sem o 9 ganha o 9');
assert(telefone_normalizar('(48) 99122-3600') === '5548991223600', 'sem DDI = Brasil');
assert(telefone_normalizar('+1 305 555 0100') === '13055550100');
assert(telefone_normalizar('abc') === null);
assert(idioma_do_ddi('5548991223600') === 'pt' && idioma_do_ddi('5491123456789') === 'es' && idioma_do_ddi('598991234567') === 'es' && idioma_do_ddi('13055550100') === 'en');

$m = cotacao_mensagem('es', 'Juan Zambrano', 10, $d('2026-11-20'), $d('2026-11-30'), composicao_texto('es', 2, []), 295000);
assert(str_contains($m, '¡Hola, Juan Zambrano! Gracias por contactarnos.') && str_contains($m, 'Total: R$ 2.950,00') && !str_contains($m, 'Constância'));
assert(str_contains(cotacao_mensagem('pt', '', 1, $d('2026-11-20'), $d('2026-11-21'), 'x', 1000), 'Olá! Obrigado'), 'sem nome');
assert(composicao_texto('pt', 2, [8]) === '2 adultos e 1 criança' && composicao_texto('en', 1, []) === '1 adult');
echo "cotação: tudo certo\n";
