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
echo "cotação: tudo certo\n";
