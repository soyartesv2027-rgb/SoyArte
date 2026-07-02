<?php
include("conexion.php");

// Verificar que se recibió el ID
if (!isset($_GET['id'])) {
    die("No se recibió el ID de la pintura.");
}

$id = intval($_GET['id']);

// Buscar la imagen antes de eliminar
$sql = "SELECT imagen FROM pinturas WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("La pintura no existe.");
}

$pintura = $resultado->fetch_assoc();

// Eliminar la imagen del servidor si existe
if (!empty($pintura['imagen']) && file_exists($pintura['imagen'])) {
    unlink($pintura['imagen']);
}

// Eliminar el registro de la base de datos
$sqlEliminar = "DELETE FROM pinturas WHERE ID = ?";
$stmtEliminar = $conn->prepare($sqlEliminar);
$stmtEliminar->bind_param("i", $id);

if ($stmtEliminar->execute()) {
    header("Location: ../pinturas.php");
exit();
 
} else {
    echo "Error al eliminar la pintura.";
}

$stmt->close();
$stmtEliminar->close();
$conn->close();
?>