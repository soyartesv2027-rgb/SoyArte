<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../funciones_foro.php';

if (!isset($_SESSION['usuario_id']) || !esAdmin()) {
    header("Location: ../../login.html");
    exit();
}

$tema_id = (int)($_POST['tema_id'] ?? 0);
$slug    = $_POST['slug'] ?? '';
$accion  = $_POST['accion'] ?? '';

if ($tema_id <= 0) {
    header("Location: ../foro.php");
    exit();
}

if ($accion === 'fijar') {
    $stmt = $conn->prepare("UPDATE foro_temas SET es_fijado = 1 WHERE id = ?");
} else {
    $stmt = $conn->prepare("UPDATE foro_temas SET es_fijado = 0 WHERE id = ?");
}
$stmt->bind_param("i", $tema_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: ../tema.php?slug=" . urlencode($slug));
