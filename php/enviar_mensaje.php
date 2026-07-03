<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit("Debes iniciar sesion.");
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$conversacion = isset($_POST['conversacion'])
    ? (int)$_POST['conversacion']
    : 0;

$mensaje = isset($_POST['mensaje'])
    ? trim($_POST['mensaje'])
    : "";

$imagen = null;

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] === UPLOAD_ERR_OK
) {
    $imagen = $_FILES['imagen'];
}

if ($conversacion <= 0) {
    exit("Conversacion invalida.");
}

if ($mensaje === "" && $imagen === null) {
    exit("Debes escribir un mensaje o seleccionar una imagen.");
}

$sql = "SELECT *
        FROM conversaciones
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversacion);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    exit("La conversacion no existe.");
}

$chat = $resultado->fetch_assoc();

if (
    $usuarioActual !== (int)$chat['usuario1_id'] &&
    $usuarioActual !== (int)$chat['usuario2_id']
) {
    exit("No autorizado.");
}

$nombreArchivo = null;
$tipoMensaje = "texto";

if ($imagen !== null) {
    $extension = strtolower(pathinfo($imagen["name"], PATHINFO_EXTENSION));
    $permitidas = ["jpg", "jpeg", "png", "gif", "webp"];

    if ($imagen["size"] > 5 * 1024 * 1024) {
        exit("La imagen supera los 5 MB.");
    }

    if (!in_array($extension, $permitidas, true)) {
        exit("Formato de imagen no permitido.");
    }

    $carpetaDestino = __DIR__ . "/../uploads/chat";

    if (!is_dir($carpetaDestino) && !mkdir($carpetaDestino, 0775, true)) {
        exit("No se pudo preparar la carpeta de imagenes.");
    }

    $nombreArchivo = uniqid("chat_") . "." . $extension;

    if (!move_uploaded_file(
        $imagen["tmp_name"],
        $carpetaDestino . "/" . $nombreArchivo
    )) {
        exit("No se pudo guardar la imagen.");
    }

    $tipoMensaje = "imagen";
}

$sqlInsert = "INSERT INTO mensajes
(
    conversacion_id,
    emisor_id,
    tipo,
    mensaje,
    archivo
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?
)";

$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param(
    "iisss",
    $conversacion,
    $usuarioActual,
    $tipoMensaje,
    $mensaje,
    $nombreArchivo
);
$stmtInsert->execute();

if ($stmtInsert->affected_rows <= 0) {
    exit("No se pudo guardar el mensaje.");
}

$sqlUpdate = "UPDATE conversaciones
              SET ultimo_mensaje = NOW()
              WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("i", $conversacion);
$stmtUpdate->execute();

echo "OK";
