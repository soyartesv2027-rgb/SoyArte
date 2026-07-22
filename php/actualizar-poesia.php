<?php
session_start();
include("conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../poesia.php");
    exit;
}
 
$obra_id    = (int) ($_POST['id'] ?? 0);
$usuario_id = (int) $_SESSION['usuario_id'];
 
// Verificar que la obra exista y le pertenezca al usuario logueado
$check = $conn->prepare("SELECT usuario_id, imagen FROM obras WHERE id = ?");
$check->bind_param("i", $obra_id);
$check->execute();
$obra = $check->get_result()->fetch_assoc();
 
if (!$obra || (int) $obra['usuario_id'] !== $usuario_id) {
    echo "No tienes permiso para editar esta obra.";
    exit;
}
 
$autor             = trim($_POST['autor'] ?? '');
$titulo            = trim($_POST['titulo'] ?? '');
$fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');
$contenido         = trim($_POST['contenido'] ?? '');
$errores           = [];
$nuevaImagen       = $obra['imagen']; // por ley pantiene la imagen actual
 
if ($autor === '') {
    $errores[] = "El autor es obligatorio.";
}
if ($titulo === '') {
    $errores[] = "El nombre de la obra es obligatorio.";
}
if ($fecha_publicacion === '') {
    $errores[] = "La fecha de publicación es obligatoria.";
}
 
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
 
    if (in_array($extension, $extensionesPermitidas)) {
        $nuevaImagen = file_get_contents($_FILES['imagen']['tmp_name']);
    } else {
        $errores[] = "Formato de imagen no permitido. Usa jpg, png, gif o webp.";
    }
}
 
if (!empty($errores)) {
    $_SESSION['errores_editar'] = $errores;
    $_SESSION['datos_editar'] = [
        'autor'             => $autor,
        'titulo'            => $titulo,
        'fecha_publicacion' => $fecha_publicacion,
        'contenido'         => $contenido,
    ];
    header("Location: ../editar.php?id=" . $obra_id);
    exit;
}
 
$sql = "UPDATE obras SET autor = ?, titulo = ?, contenido = ?, fecha_publicacion = ?, imagen = ?
        WHERE id = ?";
$upd = $conn->prepare($sql);
$upd->bind_param("sssssi", $autor, $titulo, $contenido, $fecha_publicacion, $nuevaImagen, $obra_id);
 
if ($upd->execute()) {
    header("Location: ../detalle.php?id=" . $obra_id);
    exit;
} else {
    $_SESSION['errores_editar'] = ["Ocurrió un error al guardar los cambios. Intenta de nuevo."];
    header("Location: ../editar.php?id=" . $obra_id);
    exit;
}
 