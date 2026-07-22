<?php
session_start();
include("conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../publicar-poesia.php");
    exit;
}
 
$usuario_id        = (int) $_SESSION['usuario_id'];
$autor             = trim($_POST['autor'] ?? '');
$titulo            = trim($_POST['titulo'] ?? '');
$fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');
$contenido         = trim($_POST['contenido'] ?? '');
$errores           = [];
$imagenBinaria     = null;
 
if ($autor === '') {
    $errores[] = "El autor es obligatorio.";
}
if ($titulo === '') {
    $errores[] = "El nombre de la obra es obligatorio.";
}
if ($fecha_publicacion === '') {
    $errores[] = "La fecha de publicación es obligatoria.";
}
 
// Procesar la imagen de portada (se guarda como BLOB en obras.imagen)
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
 
    if (in_array($extension, $extensionesPermitidas)) {
        $imagenBinaria = file_get_contents($_FILES['imagen']['tmp_name']);
    } else {
        $errores[] = "Formato de imagen no permitido. Usa jpg, png, gif o webp.";
    }
}
 
// Si algo fallo, regresar al formulario con los errores y los datos que ya habia escrito
if (!empty($errores)) {
    $_SESSION['errores_publicar'] = $errores;
    $_SESSION['datos_publicar'] = [
        'autor'             => $autor,
        'titulo'            => $titulo,
        'fecha_publicacion' => $fecha_publicacion,
        'contenido'         => $contenido,
    ];
    header("Location: ../publicar-poesia.php");
    exit;
}
 
$sql = "INSERT INTO obras (usuario_id, autor, titulo, contenido, fecha_publicacion, imagen)
        VALUES (?, ?, ?, ?, ?, ?)";

// 1. Verificación de existencia de la conexión
if (!isset($conn)) {
    die("Error fatal: La variable de conexión no existe. Revisa tu archivo conexion.php y asegúrate de que se llame \$conn.");
}

$stmt = $conn->prepare($sql);

// 2. Verificación de la consulta SQL
if ($stmt === false) {
    die("Error al preparar la consulta SQL: " . $conn->error);
}

// 3. Vincular los parámetros y ejecutar
$stmt->bind_param("isssss", $usuario_id, $autor, $titulo, $contenido, $fecha_publicacion, $imagenBinaria);

if ($stmt->execute()) {
    header("Location: ../poesia.php");
    exit;
} else {
    // Capturamos el error real de la base de datos para saber qué está fallando
    $_SESSION['errores_publicar'] = ["Error en la base de datos: " . $stmt->error];
    $_SESSION['datos_publicar'] = [
        'autor'             => $autor,
        'titulo'            => $titulo,
        'fecha_publicacion' => $fecha_publicacion,
        'contenido'         => $contenido,
    ];
    header("Location: ../publicar-poesia.php");
    exit;
}