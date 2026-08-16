<?php
// ============================================
// PROTECCIÓN DE PÁGINAS ADMINISTRATIVAS
// Uso: $ruta_login = 'login.html'; (o '../login.html' desde php/)
//      require_once 'php/admin_check.php';
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexion.php';

// --- ¿Autenticado? ---
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . ($GLOBALS['ruta_login'] ?? '../login.html'));
    exit;
}

// --- ¿Es administrador? (SIEMPRE en el servidor) ---
if (($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    die('Acceso denegado: esta área es exclusiva para administradores.');
}

// --- Token CSRF ---
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function csrf_token() {
    return $_SESSION['csrf'];
}

function csrf_check() {
    if (($_POST['csrf'] ?? '') !== $_SESSION['csrf']) {
        http_response_code(403);
        die('Solicitud no válida. Intenta de nuevo.');
    }
}
