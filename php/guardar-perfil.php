<?php

$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "soyarte"
);

if($conexion->connect_error){
    die("Error de conexión");
}

$nombre_pintura = $_POST['nombre_pintura'];
$descripcion = $_POST['descripcion'];
$autor = $_POST['autor'];

$imagen = $_FILES['imagen']['name'];
$temp = $_FILES['imagen']['tmp_name'];

$ruta = "uploads/" . $imagen;

move_uploaded_file($temp, $ruta);

$sql = "INSERT INTO pinturas
(nombre_pintura, descripcion, autor, imagen)

VALUES
('$nombre_pintura',
 '$descripcion',
 '$autor',
 '$ruta')";

$conexion->query($sql);

header("Location: index.php");

?>