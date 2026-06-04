<?php

$conexion = new mysqli("localhost", "root", "", "soyarte");

if ($conexion->connect_error) {
    die("Error de conexión");
}

$nombre_pintura = $_POST['nombre_pintura'];
$autor = $_POST['autor'];
$descripcion = $_POST['descripcion'];

$sql = "INSERT INTO pinturas
(nombre_pintura, descripcion, autor)
VALUES
('$nombre_pintura', '$descripcion', '$autor')";

$conexion->query($sql);

header("Location: index.php");

?>