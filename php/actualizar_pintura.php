<?php

$conexion = new mysqli("localhost", "root", "", "soyarte");

$id = $_POST['id'];
$nombre = $_POST['nombre_pintura'];
$autor = $_POST['autor'];
$descripcion = $_POST['descripcion'];

$stmt = $conexion->prepare(
    "UPDATE pinturas
     SET nombre_pintura = ?,
         autor = ?,
         descripcion = ?
     WHERE ID = ?"
);

$stmt->bind_param(
    "sssi",
    $nombre,
    $autor,
    $descripcion,
    $id
);

$stmt->execute();

header("Location: ver_pintura.php?id=".$id);
exit();

?>