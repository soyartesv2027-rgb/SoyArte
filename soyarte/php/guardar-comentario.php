<?php
session_start();
require_once "conexion.php";

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Error: Debes iniciar sesión para comentar.");
}

// Verificar que llegaron los datos del formulario
if (!isset($_POST['vr_id']) || !isset($_POST['comentario'])) {
    die("Error: Datos incompletos.");
}

$vr_id = (int)$_POST['vr_id'];
$comentario = trim($_POST['comentario']);
$usuario = $_SESSION['usuario_id'];

// Evitar comentarios vacíos
if ($comentario == "") {
    die("El comentario no puede estar vacío.");
}

// Guardar comentario
$sql = "INSERT INTO comentarios_vr (vr_id, usuario_id, comentario)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}

$stmt->bind_param("iis", $vr_id, $usuario, $comentario);

if ($stmt->execute()) {

    header("Location: ../ver_vr.php?id=" . $vr_id);
    exit();

} else {

    die("Error al guardar el comentario: " . $stmt->error);

}