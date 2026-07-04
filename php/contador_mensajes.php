<?php
session_start();
require_once "conexion.php";

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["total" => 0]);
    exit();
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$sql = "SELECT COUNT(*) AS total
        FROM mensajes m
        INNER JOIN conversaciones c
        ON m.conversacion_id = c.id
        WHERE m.emisor_id <> ?
        AND m.leido = 0
        AND
        (
            (
                c.usuario1_id = ?
                AND c.oculto_usuario1 = 0
            )
            OR
            (
                c.usuario2_id = ?
                AND c.oculto_usuario2 = 0
            )
        )";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuarioActual, $usuarioActual, $usuarioActual);
$stmt->execute();
$resultado = $stmt->get_result()->fetch_assoc();

echo json_encode(["total" => (int)$resultado['total']]);
