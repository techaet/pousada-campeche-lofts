<?php
declare(strict_types=1);
// Baixa um comprovante já emitido (só admin logado).
require __DIR__ . '/lib.php';
exigir_admin();
$f = basename((string) ($_GET['f'] ?? ''));
$p = DADOS . '/comprovantes/' . $f;
if (!preg_match('/^\d{4}-\d{3}_[a-z0-9_]+\.pdf$/', $f) || !is_file($p)) { http_response_code(404); exit; }
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $f . '"');
readfile($p);
