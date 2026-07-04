<?php
session_start();
require_once "conexion.php";

if(!isset($_SESSION['usuario_id'])){
    die("Debes iniciar sesión.");
}

$id = intval($_POST['id']);

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
    die("No autorizado.");
}

$nombre = trim($_POST['nombre']);
$autor = trim($_POST['autor']);
$descripcion = trim($_POST['descripcion']);

$imagen = $manualidad['imagen'];

if(isset($_FILES['imagen']) && $_FILES['imagen']['error']==0){

    $carpeta = "../uploads/manualidades/";

    if(!is_dir($carpeta)){
        mkdir($carpeta,0777,true);
    }

    $extension = pathinfo($_FILES['imagen']['name'],PATHINFO_EXTENSION);

    $nombreImagen = time().".".$extension;

    $rutaFisica = $carpeta.$nombreImagen;

    $rutaBD = "uploads/manualidades/".$nombreImagen;

    if(move_uploaded_file($_FILES['imagen']['tmp_name'],$rutaFisica)){

        if(file_exists("../".$manualidad['imagen'])){
            unlink("../".$manualidad['imagen']);
        }

        $imagen = $rutaBD;
    }

}

$sql = "UPDATE manualidades
SET
nombre=?,
autor=?,
descripcion=?,
imagen=?
WHERE id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"ssssi",
$nombre,
$autor,
$descripcion,
$imagen,
$id
);

if($stmt->execute()){

    header("Location: ../ver_manualidad.php?id=".$id);
    exit();

}else{

    echo "Error al actualizar.";

}
?>