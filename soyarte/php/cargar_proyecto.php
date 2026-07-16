<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$proyecto_id = (int)$_GET['id'];

$sql = "SELECT * FROM proyectos_editor WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $proyecto_id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Proyecto no encontrado']);
    exit();
}

$proyecto = $resultado->fetch_assoc();
$ruta_base = "../" . $proyecto['ruta_carpeta'];

$respuesta = [
    'success' => true,
    'id' => $proyecto['id'],
    'titulo' => $proyecto['titulo'],
    'descripcion' => $proyecto['descripcion'],
    'ancho' => $proyecto['ancho'],
    'alto' => $proyecto['alto'],
    'publico' => $proyecto['publico'],
    'ruta' => $proyecto['ruta_carpeta']
];

// Cargar preview (buscar jpg o png)
$preview_file = $ruta_base . 'preview.jpg';
if (!file_exists($preview_file)) {
    $preview_file = $ruta_base . 'preview.png';
}
if (file_exists($preview_file)) {
    $image_data = file_get_contents($preview_file);
    $mime = mime_content_type($preview_file);
    $respuesta['preview'] = 'data:' . $mime . ';base64,' . base64_encode($image_data);
}

// Limpiar y responder
ob_clean();
header('Content-Type: application/json');
echo json_encode($respuesta);
exit();
?>