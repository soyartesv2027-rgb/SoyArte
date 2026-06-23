<?php

$conexion = new mysqli("localhost", "root", "", "soyarte");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "DELETE FROM pinturas WHERE ID = $id";

$conexion->query($sql);

header("Location: pinturas.php");
exit();

?>