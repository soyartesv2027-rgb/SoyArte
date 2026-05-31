<?php

session_start();
require_once 'conexion.php';

$id = $_GET['id'] ?? 0;

$usuarioActual = $_SESSION['usuario_id'] ?? 0;

$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows === 0){
    die("Producto no encontrado");
}

$producto = $resultado->fetch_assoc();

if($producto['usuario_id'] != $usuarioActual){
    die("No tienes permiso para eliminar esta obra");
}

$rutaImagen = "uploads/" . $producto['imagen'];

if(file_exists($rutaImagen)){
    unlink($rutaImagen);
}

$sql = "DELETE FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: tienda.php");
exit;