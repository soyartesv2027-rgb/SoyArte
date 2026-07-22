<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit("Debes iniciar sesión.");
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$mensaje = isset($_POST['mensaje']) ? trim($_POST['mensaje']) : "";

if ($id <= 0 || $mensaje === "") {
    exit("Datos inválidos.");
}

$sql = "SELECT * FROM mensajes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    exit("El mensaje no existe.");
}

$mensajeData = $resultado->fetch_assoc();

if ((int)$mensajeData['emisor_id'] !== $usuarioActual) {
    exit("No puedes editar este mensaje.");
}

$sqlUpdate = "UPDATE mensajes SET mensaje = ? WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $mensaje, $id);

if ($stmtUpdate->execute()) {
    echo "OK";
} else {
    echo "Error al actualizar el mensaje.";
}
