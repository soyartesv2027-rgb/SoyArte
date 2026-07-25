<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

$id = (int)$_SESSION['usuario_id'];

$tipo_usuario = $_POST['tipo_usuario'] ?? '';

$intereses = '';
if (isset($_POST['intereses'])) {
    $intereses = implode(', ', $_POST['intereses']);
}

$tipo_tutorial = $_POST['tipo_tutorial'] ?? '';
$frecuencia = $_POST['frecuencia'] ?? '';
$aprendizaje = $_POST['manualidades'] ?? '';

$sql = "UPDATE usuarios SET
        tipo_usuario = ?,
        intereses = ?,
        tipo_tutorial = ?,
        frecuencia = ?,
        aprendizaje = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en prepare(): " . $conn->error);
}

$stmt->bind_param(
    "sssssi",
    $tipo_usuario,
    $intereses,
    $tipo_tutorial,
    $frecuencia,
    $aprendizaje,
    $id
);

if ($stmt->execute()) {
    header("Location: ../perfil.php?msg=perfil_actualizado");
} else {
    header("Location: ../perfil.php?msg=error");
}

$stmt->close();
$conn->close();
?>
