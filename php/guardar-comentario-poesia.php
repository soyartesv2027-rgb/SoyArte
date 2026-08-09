<?php
session_start();
include("conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../php/login.php");
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../poesia.php");
    exit;
}
 
$usuario_id = (int) $_SESSION['usuario_id'];
$obra_id    = (int) ($_POST['obra_id'] ?? 0);
$texto      = trim($_POST['texto'] ?? '');
 
if ($obra_id === 0 || $texto === '') {
    header("Location: ../detalle.php?id=" . $obra_id);
    exit;
}
 
$stmt = $conn->prepare("INSERT INTO comentarios_poesia (obra_id, usuario_id, texto) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $obra_id, $usuario_id, $texto);
$stmt->execute();
 
header("Location: ../detalle.php?id=" . $obra_id);
exit;
 