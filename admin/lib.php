<?php
declare(strict_types=1);

// Base da área administrativa. Dados (senhas, config, tarefas, mídias) ficam SÓ no servidor, em _dados/ (fora do Git).
const DADOS = __DIR__ . '/_dados';
// sha256 do código de instalação (o código em si só o Leonardo conhece)
const CODIGO_INSTALACAO_SHA256 = 'f80ec50046bef84a15717bb1905f9bccc653d6fea1ada606fce81d33bf311dfc';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
date_default_timezone_set('America/Sao_Paulo');

function brl(float $v): string { return 'R$ ' . number_format($v, 2, ',', '.'); }

/** "R$ 1.234,50" / "150" / "150,5" -> float; null se não for número. */
function valor_parse(string $s): ?float {
    $s = trim(preg_replace('/[^\d.,]/', '', $s) ?? '');
    if ($s === '') return null;
    if (str_contains($s, ',')) $s = str_replace(',', '.', str_replace('.', '', $s));
    elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $s)) $s = str_replace('.', '', $s);
    return is_numeric($s) ? round((float) $s, 2) : null;
}

/** Envia JSON ao Apps Script (com o segredo) e devolve a resposta decodificada, ou null se não configurado/sem resposta. */
function apps_script(array $payload): ?array {
    $url = (string) cfg('apps_script_url', '');
    if ($url === '') return null;
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTPHEADER => ['Content-Type: text/plain'],  // text/plain evita o preflight do Apps Script
        CURLOPT_POSTFIELDS => json_encode(['segredo' => cfg('apps_script_segredo', '')] + $payload)]);
    $r = json_decode((string) curl_exec($ch), true);
    curl_close($ch);
    return is_array($r) ? $r : ['ok' => false, 'erro' => 'sem resposta do Apps Script'];
}

function h($s): string { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

// ---------- armazenamento em JSON com trava ----------
function json_ler(string $nome): array {
    $f = DADOS . "/$nome.json";
    return is_file($f) ? (json_decode((string) file_get_contents($f), true) ?: []) : [];
}

/** Lê, aplica $fn (recebe o array, devolve o novo) e grava, tudo sob trava. Devolve o array novo. */
function json_atualizar(string $nome, callable $fn): array {
    $fp = fopen(DADOS . "/$nome.json", 'c+');
    flock($fp, LOCK_EX);
    $d = json_decode((string) stream_get_contents($fp), true) ?: [];
    $d = $fn($d);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($d, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod(DADOS . "/$nome.json", 0600);
    return $d;
}

function cfg(string $k, $padrao = null) { return json_ler('config')[$k] ?? $padrao; }
function cfg_salvar(array $novo): void { json_atualizar('config', fn($c) => array_merge($c, $novo)); }

// ---------- sessão, CSRF, limite de tentativas ----------
function admin_sessao(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('campeche_admin');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true, 'samesite' => 'Strict',
    ]);
    session_start();
}

function exigir_admin(): void {
    admin_sessao();
    if (empty($_SESSION['admin'])) { header('Location: /admin/', true, 302); exit; }
}

function csrf_campo(): string {
    admin_sessao();
    $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . h($_SESSION['csrf']) . '">';
}

function csrf_checar(): void {
    admin_sessao();
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Sessão expirada. Volte e tente de novo.');
    }
}

/** 5 falhas em 15 min por IP bloqueiam novas tentativas. */
function tentativas_bloqueado(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '?';
    $t = json_ler('tentativas')[$ip] ?? [];
    return count(array_filter($t, fn($x) => $x > time() - 900)) >= 5;
}

function tentativa_falha(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '?';
    json_atualizar('tentativas', function ($d) use ($ip) {
        foreach ($d as $k => $v) {
            $d[$k] = array_values(array_filter($v, fn($x) => $x > time() - 900));
            if (!$d[$k]) unset($d[$k]);
        }
        $d[$ip][] = time();
        return $d;
    });
    usleep(500000);
}

// ---------- layout ----------
function pagina_topo(string $titulo, bool $menu = true): void {
    echo '<!DOCTYPE html><html lang="pt-BR" translate="no"><head><meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1">'
        . '<meta name="robots" content="noindex, nofollow"><meta name="google" content="notranslate">'
        . '<title>' . h($titulo) . ' · Admin Campeche Lofts</title><style>'
        . ':root{--teal:#1d4038;--cream:#f1ece1;--line:#d9d3c6;--ink:#173234;--muted:#5d6b6b;--gold:#c9a96a;--red:#b3261e}'
        . '*{box-sizing:border-box}body{margin:0;font:16px/1.5 system-ui,-apple-system,Segoe UI,sans-serif;color:var(--ink);background:var(--cream)}'
        . 'header{background:var(--teal);color:#fff;padding:14px 20px;display:flex;gap:16px;align-items:center;flex-wrap:wrap}'
        . 'header a{color:#fff;text-decoration:none;opacity:.85}header a:hover{opacity:1}header .sp{flex:1}'
        . 'main{max-width:720px;margin:0 auto;padding:24px 20px 60px}h1{font-size:24px;margin:0 0 18px}'
        . '.card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;margin-bottom:18px}'
        . 'label{display:block;font-size:13px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--muted);margin:14px 0 6px}'
        . 'input,select,textarea{width:100%;padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;font:inherit;background:var(--cream);-webkit-appearance:none;appearance:none;min-width:0}'
        . 'textarea{min-height:140px}input:focus,textarea:focus,select:focus{outline:2px solid var(--teal);background:#fff}'
        . 'button,.btn{display:inline-block;margin-top:18px;padding:13px 22px;border:0;border-radius:10px;background:var(--teal);color:#fff;font:600 16px inherit;font-family:inherit;cursor:pointer;text-decoration:none}'
        . 'button.sec,.btn.sec{background:#fff;color:var(--teal);border:1.5px solid var(--teal)}'
        . '.ok{background:#e3f1e6;border:1px solid #9bc9a5;padding:12px 16px;border-radius:10px;margin-bottom:16px}'
        . '.erro{background:#fbe9e7;border:1px solid #e6a8a1;padding:12px 16px;border-radius:10px;margin-bottom:16px;color:#7a1a14}'
        . '.dica{font-size:14px;color:var(--muted)}.grid{display:grid;gap:14px}@media(min-width:560px){.grid.dois{grid-template-columns:1fr 1fr}}'
        . 'a.item{display:block;text-decoration:none;color:inherit}a.item:hover{border-color:var(--teal)}a.item strong{display:block;font-size:18px}'
        . '</style></head><body><header><strong>Campeche Lofts · Admin</strong>';
    if ($menu) echo '<a href="/admin/">Início</a><span class="sp"></span><a href="/admin/?sair=1">Sair</a>';
    echo '</header><main><h1>' . h($titulo) . '</h1>';
}

function pagina_fim(): void { echo '</main></body></html>'; }
