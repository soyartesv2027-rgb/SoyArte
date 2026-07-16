<?php
// ===== SILENCIAR TODOS LOS ERRORES DE PHP =====
error_reporting(0);
ini_set('display_errors', 0);

// ===== LIMPIAR CUALQUIER SALIDA PREVIA =====
ob_clean();

session_start();
require_once "conexion.php";

// ===== FUNCIÓN DE RESPUESTA =====
function responder($success, $error = null, $data = null) {
    ob_clean(); // Limpiar cualquier error
    header('Content-Type: application/json');
    $respuesta = ['success' => $success];
    if ($error) $respuesta['error'] = $error;
    if ($data) $respuesta['data'] = $data;
    echo json_encode($respuesta);
    exit();
}

if (!isset($_SESSION['usuario_id'])) {
    responder(false, 'No autorizado');
}

$usuario_id = $_SESSION['usuario_id'];
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    responder(false, 'Datos inválidos');
}

$carpeta_base = "../uploads/proyectos/";
if (!is_dir($carpeta_base)) mkdir($carpeta_base, 0777, true);

$nombre_carpeta = uniqid() . '_' . $usuario_id;
$ruta_carpeta = $carpeta_base . $nombre_carpeta;
mkdir($ruta_carpeta, 0777, true);

// Guardar preview
if (!empty($data['preview'])) {
    $img = str_replace('data:image/jpeg;base64,', '', $data['preview']);
    $img = str_replace(' ', '+', $img);
    file_put_contents($ruta_carpeta . '/preview.jpg', base64_decode($img));
}

// Guardar thumbnail
if (!empty($data['thumbnail'])) {
    $img = str_replace('data:image/jpeg;base64,', '', $data['thumbnail']);
    $img = str_replace(' ', '+', $img);
    file_put_contents($ruta_carpeta . '/thumbnail.jpg', base64_decode($img));
}

$ruta_relativa = 'uploads/proyectos/' . $nombre_carpeta . '/';

$sql = "INSERT INTO proyectos_editor (usuario_id, titulo, descripcion, ruta_carpeta, ancho, alto, publico) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssiii", $usuario_id, $data['titulo'], $data['descripcion'], $ruta_relativa, $data['ancho'], $data['alto'], $data['publico']);

if ($stmt->execute()) {
    responder(true, null, ['id' => $stmt->insert_id, 'ruta' => $ruta_relativa]);
} else {
    responder(false, $stmt->error);
}
?>