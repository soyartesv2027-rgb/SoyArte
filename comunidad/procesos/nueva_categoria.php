<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.html");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$nombre     = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$icono      = trim($_POST['icono'] ?? 'fa-comments');
$color      = trim($_POST['color'] ?? '#6c63ff');

if ($nombre === '') {
    header("Location: ../nueva_categoria.php?error=vacio");
    exit();
}

$slug = generarSlug($nombre);

$slug_original = $slug;
$contador = 1;
$stmt = $conn->prepare("SELECT id FROM foro_categorias WHERE slug=?");
$stmt->bind_param("s", $slug);
$stmt->execute();
while ($stmt->get_result()->fetch_row()) {
    $slug = $slug_original . '-' . $contador;
    $contador++;
    $stmt->bind_param("s", $slug);
    $stmt->execute();
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO foro_categorias (nombre, descripcion, icono, color, usuario_id, estado, slug) VALUES (?, ?, ?, ?, ?, 'pendiente', ?)");
$stmt->bind_param("ssssis", $nombre, $descripcion, $icono, $color, $usuario_id, $slug);

if ($stmt->execute()) {
    header("Location: ../foro.php?msg=categoria_enviada");
} else {
    header("Location: ../nueva_categoria.php?error=error");
}
$stmt->close();
$conn->close();
