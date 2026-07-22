<?php
session_start();
require_once 'php/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autenticado']);
    exit;
}

if (!isset($_POST['musica_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID no recibido']);
    exit;
}

$musica_id = intval($_POST['musica_id']);
$usuario_id = $_SESSION['usuario_id'];

// Verificar si ya existe like
$check = $conn->prepare("SELECT id FROM likes_musica WHERE musica_id = ? AND usuario_id = ?");
$check->bind_param("ii", $musica_id, $usuario_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $delete = $conn->prepare("DELETE FROM likes_musica WHERE musica_id = ? AND usuario_id = ?");
    $delete->bind_param("ii", $musica_id, $usuario_id);
    $delete->execute();
    $liked = false;
} else {
    $insert = $conn->prepare("INSERT INTO likes_musica (musica_id, usuario_id) VALUES (?, ?)");
    $insert->bind_param("ii", $musica_id, $usuario_id);
    $insert->execute();
    $liked = true;
}

// Contar likes
$count = $conn->prepare("SELECT COUNT(*) as total FROM likes_musica WHERE musica_id = ?");
$count->bind_param("i", $musica_id);
$count->execute();
$total = $count->get_result()->fetch_assoc();

// Actualizar tabla musica
$update = $conn->prepare("UPDATE musica SET likes = ? WHERE musica_id = ?");
$update->bind_param("ii", $total['total'], $musica_id);
$update->execute();

echo json_encode([
    'success' => true,
    'likes' => intval($total['total']),
    'liked' => $liked
]);
?>