<?php
session_start();
include("php/conexion.php");


if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: poesia.php");
    exit();
}

$obra_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Revisa si ya diste like
$sqlCheck = "SELECT id FROM likes WHERE obra_id = ? AND usuario_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $obra_id, $usuario_id);
$stmtCheck->execute();
$resultado = $stmtCheck->get_result();

if ($resultado->num_rows > 0) {
    // Si ya existe, lo quita (Toggle Unlike)
    $sqlAction = "DELETE FROM likes WHERE obra_id = ? AND usuario_id = ?";
} else {
    // Si no existe, lo agrega
    $sqlAction = "INSERT INTO likes (obra_id, usuario_id) VALUES (?, ?)";
}

$stmtAction = $conn->prepare($sqlAction);
$stmtAction->bind_param("ii", $obra_id, $usuario_id);
$stmtAction->execute();

header("Location: poesia.php");
exit();
?>