<?php

session_start();
require_once "conexion.php";

if(!isset($_SESSION['usuario_id'])){
    exit("No hay sesión");
}

$usuario = $_SESSION['usuario_id'];

$sql = "UPDATE usuarios
        SET ultima_actividad = NOW()
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if(!$stmt){
    exit("Error en prepare");
}

$stmt->bind_param("i",$usuario);

if($stmt->execute()){
    echo "OK";
}else{
    echo "ERROR";
}