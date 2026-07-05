<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit();
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    exit();
}

$sql = "SELECT m.*, c.usuario1_id, c.usuario2_id
        FROM mensajes m
        INNER JOIN conversaciones c
        ON m.conversacion_id = c.id
        WHERE m.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$mensaje = $stmt->get_result()->fetch_assoc();

if (!$mensaje) {
    exit();
}

if (
    $usuarioActual != $mensaje['usuario1_id'] &&
    $usuarioActual != $mensaje['usuario2_id']
) {
    exit();
}

$sqlUpdate = "UPDATE mensajes
              SET leido = 1,
                  estado = 3
              WHERE id = ?
              AND emisor_id <> ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("ii", $id, $usuarioActual);
$stmtUpdate->execute();

echo "OK";
