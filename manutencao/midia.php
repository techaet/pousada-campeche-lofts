<?php
declare(strict_types=1);
// Entrega uma mídia de tarefa só para quem tem o link do executor ou está logado no admin.
require __DIR__ . '/../admin/manutencao_lib.php';
admin_sessao();
$k = (string) ($_GET['k'] ?? '');
$ok = !empty($_SESSION['admin']) || ($k !== '' && cfg('executor_token') && hash_equals((string) cfg('executor_token'), $k));
$f = basename((string) ($_GET['f'] ?? ''));
$caminho = DADOS . '/midia/' . $f;
if (!$ok || $f === '' || !is_file($caminho)) { http_response_code(404); exit; }
$tipos = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif',
    'mp4' => 'video/mp4', 'mov' => 'video/quicktime', 'oga' => 'audio/ogg', 'ogg' => 'audio/ogg', 'mp3' => 'audio/mpeg', 'm4a' => 'audio/mp4', 'pdf' => 'application/pdf'];
$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
// só tipos conhecidos são exibidos; o resto vai como download (nunca executa)
header('Content-Type: ' . ($tipos[$ext] ?? 'application/octet-stream'));
if (!isset($tipos[$ext])) header('Content-Disposition: attachment');
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . filesize($caminho));
header('Cache-Control: private, max-age=3600');
readfile($caminho);
