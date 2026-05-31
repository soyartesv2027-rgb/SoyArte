<?php

session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if (
    !isset($_FILES['foto_perfil']) ||
    $_FILES['foto_perfil']['error'] != 0
) {
    die("Error al subir la imagen");
}

$archivo = $_FILES['foto_perfil'];

$extension = strtolower(
    pathinfo($archivo['name'], PATHINFO_EXTENSION)
);

$permitidas = ['jpg', 'jpeg', 'png', 'webp'];

if (!in_array($extension, $permitidas)) {
    die("Formato no permitido");
}

$nombreArchivo =
    "perfil_" .
    $usuario_id .
    "_" .
    time() .
    "." .
    $extension;

$rutaDestino =
    "uploads/perfiles/" .
    $nombreArchivo;

if (!move_uploaded_file(
    $archivo['tmp_name'],
    $rutaDestino
)) {
    die("No se pudo guardar la imagen");
}

/* Buscar foto anterior */

$sql = "SELECT foto_perfil
        FROM usuarios
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

/* Eliminar foto vieja */

if (
    !empty($usuario['foto_perfil'])
) {

    $fotoVieja =
        "uploads/perfiles/" .
        $usuario['foto_perfil'];

    if (file_exists($fotoVieja)) {
        unlink($fotoVieja);
    }
}

/* Guardar nueva foto */

$sql = "UPDATE usuarios
        SET foto_perfil = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "si",
    $nombreArchivo,
    $usuario_id
);

$stmt->execute();

header("Location: perfil.php");
exit();