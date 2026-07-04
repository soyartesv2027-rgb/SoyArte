<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit();
}

$usuarioActual = $_SESSION['usuario_id'];

$conversacion = isset($_POST['conversacion'])
    ? intval($_POST['conversacion'])
    : 0;

if ($conversacion <= 0) {
    exit();
}

// Verificar que la conversación exista
$sql = "SELECT *
        FROM conversaciones
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversacion);
$stmt->execute();

$chat = $stmt->get_result()->fetch_assoc();

if (!$chat) {
    exit();
}

// Verificar permisos
if (
    $usuarioActual != $chat['usuario1_id'] &&
    $usuarioActual != $chat['usuario2_id']
) {
    exit();
}

// Marcar como entregados los mensajes recibidos
$sql = "UPDATE mensajes
        SET estado = 2
        WHERE conversacion_id = ?
        AND emisor_id <> ?
        AND estado = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ii",
    $conversacion,
    $usuarioActual
);

$stmt->execute();

echo "OK";