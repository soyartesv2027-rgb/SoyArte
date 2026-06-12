<?php
session_start();
include("php/conexion.php");

// Solo usuarios con sesión pueden publicar
if (!isset($_SESSION['usuario_id'])) {
    header("Location: php/login.php");
    exit;
}

$usuario_actual = $_SESSION['usuario_id'];


// Verificar que el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: publicar.php");
    exit;
}

$titulo    = trim($_POST['titulo']    ?? '');
$contenido = trim($_POST['contenido'] ?? '');
$imagen    = null;
$errores   = [];

// --- Validaciones ---
if (empty($titulo)) {
    $errores[] = "El título es obligatorio.";
}
if (empty($contenido)) {
    $errores[] = "El contenido del poema es obligatorio.";
}
if (strlen($titulo) > 200) {
    $errores[] = "El título no puede superar 200 caracteres.";
}

// --- Procesar imagen ---
if (!empty($_FILES['imagen']['tmp_name'])) {
    $tipo_permitido = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $tipo           = $_FILES['imagen']['type'];
    $tamano         = $_FILES['imagen']['size'];
    $max_tamano     = 5 * 1024 * 1024; // 5MB

    if (!in_array($tipo, $tipo_permitido)) {
        $errores[] = "La imagen debe ser JPG, PNG, GIF o WEBP.";
    } elseif ($tamano > $max_tamano) {
        $errores[] = "La imagen no puede superar 5MB.";
    } else {
        $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
    }
}

// --- Si hay errores, volver al formulario con mensajes ---
if (!empty($errores)) {
    $_SESSION['errores_publicar']  = $errores;
    $_SESSION['datos_publicar']    = ['titulo' => $titulo, 'contenido' => $contenido];
    header("Location: publicar.php");
    exit;
}

// --- Guardar en la base de datos ---
$stmt = $conn->prepare(
    "INSERT INTO obras (usuario_id, titulo, contenido, imagen) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("issb", $usuario_actual, $titulo, $contenido, $imagen);

if ($stmt->execute()) {
    $nuevo_id = $conn->insert_id;
    // Redirigir al detalle del poema recién creado
    header("Location: detalle.php?id=$nuevo_id");
    exit;
} else {
    $_SESSION['errores_publicar'] = ["Error al guardar el poema: " . $conn->error];
    $_SESSION['datos_publicar']   = ['titulo' => $titulo, 'contenido' => $contenido];
    header("Location: publicar.php");
    exit;
}
