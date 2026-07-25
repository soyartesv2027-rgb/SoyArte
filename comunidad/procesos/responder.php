<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.html");
    exit();
}

$usuario_id = (int)$_SESSION['usuario_id'];
$tema_id    = (int)($_POST['tema_id'] ?? 0);
$contenido  = trim($_POST['contenido'] ?? '');

$archivo      = null;
$tipo_mensaje = 'texto';
$nombre_archivo = null;

if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['archivo'];
}

if ($tema_id <= 0 || ($contenido === '' && $archivo === null)) {
    header("Location: ../../tema.php?slug=" . urlencode($_POST['slug'] ?? '') . "&error=vacio");
    exit();
}

$stmt = $conn->prepare("SELECT id, es_cerrado, slug FROM foro_temas WHERE id=?");
$stmt->bind_param("i", $tema_id);
$stmt->execute();
$tema = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tema || $tema['es_cerrado'] == 1) {
    header("Location: ../../foro.php");
    exit();
}

$slug = $tema['slug'];

if ($archivo !== null) {
    $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
    $img_permitidas = ["jpg","jpeg","png","gif","webp"];
    $audio_permitidas = ["mp3","wav","ogg","aac","m4a"];
    $permitidas = array_merge($img_permitidas, $audio_permitidas);

    if (!in_array($extension, $permitidas)) {
        header("Location: ../../tema.php?slug=$slug&error=formato");
        exit();
    }

    $mime = mime_content_type($archivo["tmp_name"]);
    $mime_permitidos = [
        "image/jpeg","image/png","image/gif","image/webp",
        "audio/mpeg","audio/wav","audio/ogg","audio/aac","audio/mp4","audio/x-m4a"
    ];

    if (!in_array($mime, $mime_permitidos)) {
        header("Location: ../../tema.php?slug=$slug&error=formato");
        exit();
    }

    $max_size = 10 * 1024 * 1024;
    if ($archivo["size"] > $max_size) {
        header("Location: ../../tema.php?slug=$slug&error=peso");
        exit();
    }

    $nombre_archivo = uniqid("foro_") . "." . $extension;
    $destino = __DIR__ . "/../../uploads/foro/" . $nombre_archivo;
    move_uploaded_file($archivo["tmp_name"], $destino);

    $tipo_mensaje = in_array($extension, $img_permitidas) ? 'imagen' : 'audio';
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO foro_respuestas (tema_id, usuario_id, contenido, tipo, archivo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $tema_id, $usuario_id, $contenido, $tipo_mensaje, $nombre_archivo);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE foro_temas SET num_respuestas = num_respuestas + 1, ultimo_usuario_id = ?, ultima_actividad = NOW() WHERE id = ?");
    $stmt->bind_param("ii", $usuario_id, $tema_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    header("Location: ../../tema.php?slug=$slug#respuestas");
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../../tema.php?slug=$slug&error=error");
}
