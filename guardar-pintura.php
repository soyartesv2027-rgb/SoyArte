<?php

$conexion = new mysqli("localhost", "root", "", "soyarte");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre_pintura = $_POST['nombre_pintura'];
$autor = $_POST['autor'];
$descripcion = $_POST['descripcion'];

$nombreImagen = $_FILES['imagen']['name'];
$tmpImagen = $_FILES['imagen']['tmp_name'];

$ruta = "uploads/" . time() . "_" . $nombreImagen;

move_uploaded_file($tmpImagen, $ruta);

$sql = "INSERT INTO pinturas
(nombre_pintura, descripcion, autor, imagen)
VALUES
('$nombre_pintura', '$descripcion', '$autor', '$ruta')";

if ($conexion->query($sql)) {
    header("Location: pinturas.php");
    exit();
} else {
    echo "Error al guardar la pintura.";
}

$conexion->close();

?>