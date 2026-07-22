<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['usuario_id'])){
    header("Location: ../login.html");
    exit();
}

$idUsuario = $_SESSION['usuario_id'];
$idPintura = $_POST['id_pintura'];
$comentario = trim($_POST['comentario']);

if($comentario != ""){

    $sql = "INSERT INTO comentarios_pinturas
            (id_pintura,id_usuario,comentario)
            VALUES(?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis",$idPintura,$idUsuario,$comentario);
    $stmt->execute();
}

header("Location: ../ver_pintura.php?id=".$idPintura);
exit();