<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.html");
    exit();
}

$usuario_id    = (int)$_SESSION['usuario_id'];
$categoria_id  = (int)($_POST['categoria_id'] ?? 0);
$categoria_slug = $_POST['categoria_slug'] ?? '';
$titulo        = trim($_POST['titulo'] ?? '');
$contenido     = trim($_POST['contenido'] ?? '');

if ($categoria_id <= 0 || $titulo === '' || $contenido === '') {
    header("Location: ../../crear_tema.php?categoria=" . urlencode($categoria_slug) . "&error=vacio");
    exit();
}

$slug = generarSlug($titulo);

$slug_original = $slug;
$contador = 1;
$stmt = $conn->prepare("SELECT id FROM foro_temas WHERE slug=?");
$stmt->bind_param("s", $slug);
$stmt->execute();
while ($stmt->get_result()->fetch_row()) {
    $slug = $slug_original . '-' . $contador;
    $contador++;
    $stmt->bind_param("s", $slug);
    $stmt->execute();
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO foro_temas (categoria_id, usuario_id, titulo, contenido, slug, ultima_actividad) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iisss", $categoria_id, $usuario_id, $titulo, $contenido, $slug);

if ($stmt->execute()) {
    $tema_id = $stmt->insert_id;

    $stmt->close();

    $stmt = $conn->prepare("UPDATE foro_categorias SET num_temas = num_temas + 1 WHERE id = ?");
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO foro_visitas (tema_id, usuario_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $tema_id, $usuario_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ../../tema.php?slug=$slug");
} else {
    header("Location: ../../crear_tema.php?categoria=" . urlencode($categoria_slug) . "&error=error");
}
$conn->close();
