<?php
// ============================================
// CONEXIÓN A LA BASE DE DATOS
// ============================================

// Detectar si estamos en XAMPP (localhost)
$es_local = (
    $_SERVER['SERVER_NAME'] === 'localhost' ||
    $_SERVER['SERVER_NAME'] === '127.0.0.1'
);

if ($es_local) {
    // ===== CONFIGURACIÓN XAMPP =====
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "soyarte";

} else {
    // ===== CONFIGURACIÓN INFINITYFREE =====
    $host = "sql210.infinityfree.com";
    $user = "if0_42421416";
    $password = "8pFN3U18SB4t";
    $dbname = "if0_42421416_soyarte";
}

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>