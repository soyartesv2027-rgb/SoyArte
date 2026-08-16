<?php
session_start();
require_once __DIR__ . "/conexion.php";

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Manualidad no encontrada.");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM manualidades WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows==0){
    die("Manualidad no encontrada.");
}

$manualidad = $resultado->fetch_assoc();

if($_SESSION['usuario_id'] != $manualidad['usuario_id']){
    die("No tienes permiso para eliminar esta publicación.");
}

/* Eliminar imagen */

if(
    !empty($manualidad['imagen']) &&
    file_exists(__DIR__ . "/../" . $manualidad['imagen'])
){
    unlink(__DIR__ . "/../" . $manualidad['imagen']);
}

/* Eliminar comentarios */

$sql = "DELETE FROM comentarios_manualidades
        WHERE manualidad_id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

/* Eliminar publicación */

$sql = "DELETE FROM manualidades
        WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);

if($stmt->execute()){

    header("Location: manualidad.php");
    exit();

}else{

    echo "Error al eliminar la publicación.";

}
?>