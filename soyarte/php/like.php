<?php
session_start();
include("conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
 
$usuario_actual = $_SESSION['usuario_id'];
$obra_id        = intval($_GET['id']       ?? 0);
$redirect       = $_GET['redirect']        ?? 'lista';
 
if ($obra_id > 0) {
    $check = $conn->prepare("SELECT id FROM likes WHERE obra_id = ? AND usuario_id = ?");
    $check->bind_param("ii", $obra_id, $usuario_actual);
    $check->execute();
    $check->store_result();
 
    if ($check->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM likes WHERE obra_id = ? AND usuario_id = ?");
        $del->bind_param("ii", $obra_id, $usuario_actual);
        $del->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO likes (obra_id, usuario_id) VALUES (?, ?)");
        $ins->bind_param("ii", $obra_id, $usuario_actual);
        $ins->execute();
    }
}
 
header($redirect === 'detalle'
    ? "Location: ../detalle.php?id=$obra_id"
    : "Location: ../poesia.php");
exit; 