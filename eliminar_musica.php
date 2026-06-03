<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.html");
    exit();
}

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM musica WHERE musica_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$musica = $stmt->get_result()->fetch_assoc();

if(!$musica){
    die("Canción no encontrada");
}

if($_SESSION['usuario_id'] != $musica['usuario_id']){
    die("No tienes permiso para eliminar esta publicación");
}

/* Eliminar portada */

$rutaImagen = "uploads/musica/" . $musica['portada'];

if(
    !empty($musica['portada']) &&
    file_exists($rutaImagen)
){
    unlink($rutaImagen);
}

/* Eliminar registro */

$sqlDelete = "DELETE FROM musica WHERE musica_id = ?";

$stmt = $conn->prepare($sqlDelete);
$stmt->bind_param("i", $id);

if($stmt->execute()){

    header("Location: musica.php");
    exit();

}else{

    echo "Error al eliminar la publicación";

}
?>