<?php
session_start();
require_once "conexion.php";

// Limpiar cualquier salida previa
ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
    exit();
}

$proyecto_id = (int)$data['id'];

// Verificar que el proyecto existe y pertenece al usuario
$sql = "SELECT ruta_carpeta FROM proyectos_editor WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $proyecto_id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Proyecto no encontrado']);
    exit();
}

$proyecto = $resultado->fetch_assoc();
$ruta_carpeta = "../" . $proyecto['ruta_carpeta'];

// Eliminar archivos de la carpeta
if (is_dir($ruta_carpeta)) {
    $archivos = scandir($ruta_carpeta);
    foreach ($archivos as $archivo) {
        if ($archivo !== '.' && $archivo !== '..') {
            @unlink($ruta_carpeta . '/' . $archivo);
        }
    }
    @rmdir($ruta_carpeta);
}

// Eliminar de la base de datos
$sql_delete = "DELETE FROM proyectos_editor WHERE id = ? AND usuario_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("ii", $proyecto_id, $usuario_id);

if ($stmt_delete->execute()) {
    echo json_encode(['success' => true, 'message' => 'Proyecto eliminado']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al eliminar de BD: ' . $stmt_delete->error]);
}

$stmt->close();
$stmt_delete->close();
$conn->close();
?>