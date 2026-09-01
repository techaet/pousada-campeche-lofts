<?php
declare(strict_types=1);

const GUIDE_PASSWORD_HASH = '$2y$10$tSFVO0XYCXU4j5zvywoJmOQbSh1Qn8WAWIpG4Tx89sRUDMSOLbxfi';

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    session_name('campeche_guide');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/guia/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
