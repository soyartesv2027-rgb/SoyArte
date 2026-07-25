<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "soyarte");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Debes iniciar sesión.");
}

$idUsuario = $_SESSION['usuario_id'];

$nombre_pintura = $_POST['nombre_pintura'];
$autor = $_POST['autor'];
$descripcion = $_POST['descripcion'];

/* Imagen */
$imagen = $_FILES['imagen']['name'];
$tmp = $_FILES['imagen']['tmp_name'];

$ruta = "uploads/pinturas/" . basename($imagen);

move_uploaded_file($tmp, $ruta);

/* Guardar en BD */
$sql = "INSERT INTO pinturas
(nombre_pintura, descripcion, autor, imagen, id_usuario)
VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "ssssi",
    $nombre_pintura,
    $descripcion,
    $autor,
    $ruta,
    $idUsuario
);

if ($stmt->execute()) {
    header("Location: pinturas.php");
    exit();
} else {
    echo "Error al guardar la pintura: " . $conexion->error;
}
?>