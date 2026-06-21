<?php

$conexion = new mysqli("localhost", "root", "", "soyarte");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre_pintura = $_POST['nombre_pintura'];
$autor = $_POST['autor'];
$descripcion = $_POST['descripcion'];


/* Imagen */
$imagen = $_FILES['imagen']['name'];
$tmp = $_FILES['imagen']['tmp_name'];

$ruta = "uploads/pinturas/" . $imagen;

move_uploaded_file($tmp, $ruta);

/* Guardar en BD */

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

header("Location: pinturas.php");
exit();

?>