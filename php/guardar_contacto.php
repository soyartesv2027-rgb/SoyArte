<?php

require_once "conexion.php";

$nombre = trim($_POST["nombre"]);
$correo = trim($_POST["correo"]);
$asunto = trim($_POST["asunto"]);
$mensaje = trim($_POST["mensaje"]);

if(
    empty($nombre) ||
    empty($correo) ||
    empty($asunto) ||
    empty($mensaje)
){
    exit("Complete todos los campos.");
}

if(!filter_var($correo,FILTER_VALIDATE_EMAIL)){
    exit("Correo inválido.");
}

$sql = "INSERT INTO mensajes_contacto
(nombre,correo,asunto,mensaje)
VALUES(?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssss",
    $nombre,
    $correo,
    $asunto,
    $mensaje
);

if($stmt->execute()){

    echo "ok";

}else{

    echo "No fue posible guardar el mensaje.";

}

$stmt->close();
$conn->close();

?>