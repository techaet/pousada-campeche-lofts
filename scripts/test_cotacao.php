<?php
// Teste do telefone, idioma e mensagem da cotação (o preço vem do robô). Rodar: php scripts/test_cotacao.php
require __DIR__ . '/../admin/cotacao_lib.php';

$d = fn($s) => new DateTimeImmutable($s);

assert(telefone_normalizar('+54 9 11 2345-6789') === '5491123456789');
assert(telefone_normalizar('+54 11 2345-6789') === '5491123456789', 'AR sem o 9 ganha o 9');
assert(telefone_normalizar('(48) 99122-3600') === '5548991223600', 'sem DDI = Brasil');
assert(telefone_normalizar('+1 305 555 0100') === '13055550100');
assert(telefone_normalizar('abc') === null);
assert(idioma_do_ddi('5548991223600') === 'pt' && idioma_do_ddi('5491123456789') === 'es' && idioma_do_ddi('598991234567') === 'es' && idioma_do_ddi('13055550100') === 'en');

$m = cotacao_mensagem('es', 'Juan Zambrano', 10, $d('2026-11-20'), $d('2026-11-30'), composicao_texto('es', 2, []), 295000);
assert(str_contains($m, '¡Hola, Juan Zambrano! Gracias por contactarnos.') && str_contains($m, 'Total: R$ 2.950,00') && !str_contains($m, 'Constância'));
assert(str_contains(cotacao_mensagem('pt', '', 1, $d('2026-11-20'), $d('2026-11-21'), 'x', 1000), 'Olá! Obrigado'), 'sem nome');
assert(composicao_texto('pt', 2, [8]) === '2 adultos e 1 criança' && composicao_texto('en', 1, []) === '1 adult' && composicao_texto('pt', 4, [], 2) === '4 adultos (2 lofts)');
echo "cotação: tudo certo\n";
