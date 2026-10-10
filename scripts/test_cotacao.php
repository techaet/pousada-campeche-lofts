<?php
// Teste do telefone da cotação (o texto e o preço vêm do robô). Rodar: php scripts/test_cotacao.php
require __DIR__ . '/../admin/cotacao_lib.php';

assert(telefone_normalizar('+54 9 11 2345-6789') === '5491123456789');
assert(telefone_normalizar('+54 11 2345-6789') === '5491123456789', 'AR sem o 9 ganha o 9');
assert(telefone_normalizar('(48) 99122-3600') === '5548991223600', 'sem DDI = Brasil');
assert(telefone_normalizar('+1 305 555 0100') === '13055550100');
assert(telefone_normalizar('abc') === null);

$ficha = "¿Cuándo pensás viajar este verano?\nEnero/2027\n¿Cómo viene tu grupo?\nPareja con 2 o más hijos/as\nFirst name\nFlavio Ariel\nPhone number\n+543572443257\nEmail\nflaviofantoni@gmail.com";
assert(telefone_do_texto($ficha) === '5493572443257', 'ficha do anúncio (AR ganha o 9)');
assert(telefone_do_texto("Hola, soy Juan, mi whatsapp +54 9 11 2345-6789, somos 2") === '5491123456789');
assert(telefone_do_texto("Oi, sou a Ana, (48) 99122-3600, 2 adultos") === '5548991223600');
assert(telefone_do_texto("sem telefone aqui, 2 adultos em 20/11") === null);

// bloco comercial acrescentado só quando o robô não o trouxe
[$x, $c] = cotacao_completar("¡Hola, Flavio! Soy Constância, de Pousada Campeche Lofts.\n\nEn enero/2027, para una pareja con 2 hijos/as, la diaria queda entre R$ 450,00 y R$ 1.250,00.\n\nPara prepararte la cotización exacta, contame:\n• ¿Qué edades tienen los hijos/as?\n\nConstância");
assert($c === true && str_contains($x, 'Estos precios son para las primeras reservas') && str_contains($x, 'SUPERHOST 5') && str_contains($x, 'Haz clic en este enlace: https://') && str_ends_with($x, "\n\nConstância") && substr_count($x, 'Constância') === 2, 'es com preço');
[$x, $c] = cotacao_completar("Olá! Sou Constância.\n\nAnotei 2 adultos em janeiro. Para enviar o orçamento, quais as datas exatas?\n\nConstância");
assert($c === true && !str_contains($x, 'Esses preços') && str_contains($x, 'Nossa pousada') && str_contains($x, 'Clique neste link'), 'pt sem preço: sem a frase "esses preços"');
[$x, $c] = cotacao_completar("Hello! Total: R$ 2.950,00\n\n🏆 Campeche Lofts is an Airbnb SUPERHOST 5⭐\n\nConstância");
assert($c === false && str_contains($x, 'Total: R$ 2.950,00'), 'cotação completa não é alterada');
echo "cotação: tudo certo\n";
