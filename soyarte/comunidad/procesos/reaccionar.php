<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../funciones_foro.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'no_auth']);
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$tipo       = $_POST['tipo'] ?? '';
$target_id  = (int)($_POST['target_id'] ?? 0);

if (!in_array($tipo, ['tema', 'respuesta']) || $target_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'invalid']);
    exit();
}

$ya_reacciono = usuarioReacciono($conn, $usuario_id, $tipo, $target_id);

if ($ya_reacciono) {
    $stmt = $conn->prepare("DELETE FROM foro_reacciones WHERE usuario_id=? AND tipo=? AND target_id=?");
    $stmt->bind_param("isi", $usuario_id, $tipo, $target_id);
    $stmt->execute();
    $stmt->close();
    $accion = 'elimino';
} else {
    $stmt = $conn->prepare("INSERT INTO foro_reacciones (usuario_id, tipo, target_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $usuario_id, $tipo, $target_id);
    $stmt->execute();
    $stmt->close();
    $accion = 'inserto';
}

$total = contarReacciones($conn, $tipo, $target_id);
$conn->close();

echo json_encode([
    'success' => true,
    'accion'  => $accion,
    'total'   => $total
]);
