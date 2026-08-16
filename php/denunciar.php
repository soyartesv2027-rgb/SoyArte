<?php
// ============================================
// DENUNCIAR UNA PUBLICACIÓN (usuario normal)
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/mod_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Debe estar autenticado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['mod_error'] = 'Debes iniciar sesión para denunciar contenido.';
    header('Location: ../login.html');
    exit;
}

$denuncianteId = (int)$_SESSION['usuario_id'];
$tipo = isset($_POST['tipo']) ? mod_tipo_valido($_POST['tipo']) : null;
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$motivo = trim($_POST['motivo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if (!$tipo || $id <= 0) {
    die('Parámetros no válidos.');
}

if (!in_array($motivo, mod_motivos())) {
    die('Motivo no válido.');
}

$cfg = mod_tipos()[$tipo];
$tabla = $cfg['tabla'];
$idCol = $cfg['id_col'];
$usuarioCol = $cfg['usuario'];

// Verificar que la publicación existe y obtener su autor
$stmt = $conn->prepare("SELECT `$usuarioCol` AS usuario_id FROM `$tabla` WHERE `$idCol` = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$fila = $stmt->get_result()->fetch_assoc();

if (!$fila) {
    die('Publicación no encontrada.');
}

// No se permite denunciar contenido propio
if ((int)$fila['usuario_id'] === $denuncianteId) {
    $_SESSION['mod_error'] = 'No puedes denunciar tu propio contenido.';
    header('Location: ../' . $cfg['url'] . '?id=' . $id);
    exit;
}

// Evitar denuncias duplicadas del mismo usuario sobre la misma publicación
$stmt = $conn->prepare("SELECT id FROM denuncias WHERE tipo_contenido = ? AND id_contenido = ? AND id_denunciante = ? AND estado != 'resuelta' LIMIT 1");
$stmt->bind_param('sii', $tipo, $id, $denuncianteId);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['mod_error'] = 'Ya has denunciado esta publicación.';
    header('Location: ../' . $cfg['url'] . '?id=' . $id);
    exit;
}

// Insertar denuncia
$stmt = $conn->prepare("INSERT INTO denuncias (tipo_contenido, id_contenido, id_denunciante, motivo, descripcion) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('siiss', $tipo, $id, $denuncianteId, $motivo, $descripcion);

if ($stmt->execute()) {
    $_SESSION['mod_mensaje'] = 'Gracias por tu reporte. El equipo de moderación lo revisará.';
} else {
    $_SESSION['mod_error'] = 'Error al enviar la denuncia. Intenta de nuevo.';
}

header('Location: ../' . $cfg['url'] . '?id=' . $id);
exit;