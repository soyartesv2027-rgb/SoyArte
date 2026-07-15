<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT id, titulo, ruta_carpeta, fecha_creacion, fecha_actualizacion 
        FROM proyectos_editor 
        WHERE usuario_id = ? 
        ORDER BY fecha_actualizacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$proyectos = [];
while ($row = $resultado->fetch_assoc()) {
    // Verificar thumbnail
    $ruta_thumbnail = "../" . $row['ruta_carpeta'] . 'thumbnail.jpg';
    if (!file_exists($ruta_thumbnail)) {
        $ruta_thumbnail = "../" . $row['ruta_carpeta'] . 'thumbnail.png';
    }
    $row['ruta_thumbnail'] = file_exists($ruta_thumbnail) ? $row['ruta_carpeta'] . 'thumbnail.jpg' : '';
    $proyectos[] = $row;
}

// Limpiar cualquier salida previa
ob_clean();
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'proyectos' => $proyectos
]);
exit();
?>