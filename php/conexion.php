<?php
// ============================================
// CONEXIÓN A LA BASE DE DATOS
// DETECCIÓN AUTOMÁTICA DE ENTORNO
// ============================================

// Detectar si estamos en local o en online
$es_local = (
    $_SERVER['SERVER_NAME'] === 'localhost' || 
    $_SERVER['SERVER_NAME'] === '127.0.0.1' ||
    strpos($_SERVER['SERVER_NAME'], 'localhost') !== false
);

if ($es_local) {
    // ===== CONFIGURACIÓN LOCAL (XAMPP/WAMP) =====
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "soyarte";
} else {
    // ===== CONFIGURACIÓN PARA INFINITYFREE =====
    // Estos datos los encuentras en el panel de InfinityFree
    // Ve a: https://client.infinityfree.com/ → "Manage" → "MySQL Database"
    
    $host = "sql123.infinityfree.com";  // ← Cambia por el host que te da InfinityFree
    $user = "if0_12345678";              // ← Cambia por tu usuario de BD
    $password = "TuContraseña123";       // ← Cambia por tu contraseña de BD
    $dbname = "if0_12345678_soyarte";    // ← Cambia por el nombre de tu BD
}

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

define('ENTORNO_LOCAL', $es_local);
?>s