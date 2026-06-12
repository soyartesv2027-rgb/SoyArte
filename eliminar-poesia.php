<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: php/login.php");
    exit;
}

$usuario_actual = $_SESSION['usuario_id'];
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // El WHERE usuario_id asegura que solo el dueño puede eliminar
    $stmt = $conn->prepare("DELETE FROM obras WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_actual);
    $stmt->execute();
}

header("Location: poesia.php");
exit;
