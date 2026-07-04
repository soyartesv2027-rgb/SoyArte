<?php
session_start();
require_once "conexion.php";

if(!isset($_SESSION['usuario_id'])){
    die("Debes iniciar sesión para comentar.");
}

if($_SERVER['REQUEST_METHOD']!="POST"){
    die("Acceso no permitido.");
}

$usuario_id = $_SESSION['usuario_id'];

$manualidad_id = intval($_POST['manualidad_id']);

$comentario = trim($_POST['comentario']);

if(empty($comentario)){
    die("El comentario está vacío.");
}

$sql = "INSERT INTO comentarios_manualidades
(manualidad_id,usuario_id,comentario)
VALUES (?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"iis",
$manualidad_id,
$usuario_id,
$comentario
);

if($stmt->execute()){

    header("Location: ../ver_manualidad.php?id=".$manualidad_id);
    exit();

}else{

    echo "Error al guardar el comentario.";

}
?>