<?php
declare(strict_types=1);
// Tradutor da página de cotação (chamado por JavaScript, devolve JSON). Usa o Groq já configurado.
require __DIR__ . '/cotacao_lib.php';
ini_set('display_errors', '0');  // aviso do PHP não pode vazar para dentro do JSON
exigir_admin();
header('Content-Type: application/json; charset=utf-8');
set_time_limit(90);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit(json_encode(['ok' => false, 'erro' => 'método inválido'])); }
csrf_checar();
try {
    $texto = trim((string) ($_POST['texto'] ?? ''));
    if ($texto === '') throw new RuntimeException('Digite o texto para traduzir.');
    echo json_encode(['ok' => true, 'traducao' => traduzir_texto($texto, (string) ($_POST['idioma'] ?? 'es-AR'))], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'erro' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
