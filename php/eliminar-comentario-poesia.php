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
 
$usuario_id    = (int) $_SESSION['usuario_id'];
$comentario_id = (int) ($_POST['comentario_id'] ?? 0);
$obra_id       = (int) ($_POST['obra_id'] ?? 0);
 
// Verificar que el comentario le pertenece al usuario
$check = $conn->prepare("SELECT id FROM comentarios_poesia WHERE id = ? AND usuario_id = ?");
$check->bind_param("ii", $comentario_id, $usuario_id);
$check->execute();
 
if ($check->get_result()->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM comentarios_poesia WHERE id = ?");
    $del->bind_param("i", $comentario_id);
    $del->execute();
}
 
header("Location: ../detalle.php?id=" . $obra_id);
exit;
 