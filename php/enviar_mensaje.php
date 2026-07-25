<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit("Debes iniciar sesión.");
}
$usuarioActual = $_SESSION['usuario_id'];

$carpetaContacto = __DIR__ . "/../uploads/contacto_artista/";
if (!is_dir($carpetaContacto)) {
    mkdir($carpetaContacto, 0755, true);
}

//RECIBIR DATOS //
$conversacion = isset($_POST['conversacion'])
    ? intval($_POST['conversacion'])
    : 0;

$mensaje = isset($_POST['mensaje'])
    ? trim($_POST['mensaje'])
    : "";

// RECIBIR ARCHIVO
$archivo = null;

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] === UPLOAD_ERR_OK
) {
    $archivo = $_FILES['imagen'];
} elseif (
    isset($_FILES['archivo']) &&
    $_FILES['archivo']['error'] === UPLOAD_ERR_OK
) {
    $archivo = $_FILES['archivo'];
}

//VALIDACIONES //

if ($conversacion <= 0) {
    exit("Conversación inválida.");
}

if ($mensaje == "" && $archivo === null) {
    exit("Debes escribir un mensaje o seleccionar un archivo.");
}

//BUSCAR LA CONVERSACIÓN //
$sql = "SELECT *
        FROM conversaciones
        WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$conversacion);
$stmt->execute();
$resultado = $stmt->get_result();
if($resultado->num_rows==0){
    exit("La conversación no existe.");
}
$chat = $resultado->fetch_assoc();

//VERIFICAR PERMISOS //
if(
    $usuarioActual != $chat['usuario1_id']
    &&
    $usuarioActual != $chat['usuario2_id']
){
    exit("No autorizado.");
}

$nombreArchivo = null;
$tipoMensaje = "texto";

if ($archivo !== null) {

    $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));

    $permitidas = ["jpg","jpeg","png","gif","webp","mp4","webm","ogg","mp3","wav"];

    if (!in_array($extension,$permitidas)) {
        exit("Formato de archivo no permitido.");
    }

    $mime = mime_content_type($archivo["tmp_name"]);
    $mimePermitidos = [
        "image/jpeg","image/png","image/gif","image/webp",
        "video/mp4","video/webm","video/ogg",
        "audio/mpeg","audio/wav","audio/ogg"
    ];

    if (!in_array($mime, $mimePermitidos)) {
        exit("Tipo de archivo no permitido.");
    }

    // Tamaño máximo: 5 MB para imágenes, 20 MB para video/audio
    $maxSize = (strpos($mime, "image/") === 0) ? 5 * 1024 * 1024 : 20 * 1024 * 1024;

    if($archivo["size"] > $maxSize){
        exit("El archivo supera el tamaño máximo permitido.");
    }

    $nombreArchivo = uniqid("chat_").".".$extension;

    move_uploaded_file(
        $archivo["tmp_name"],
        "../uploads/contacto_artista/".$nombreArchivo
    );

    $tipoMensaje = (strpos($mime, "image/") === 0) ? "imagen" : "archivo";
}

//GUARDAR MENSAJE //
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

//ACTUALIZAR ÚLTIMO MENSAJE//
$sqlUpdate = "UPDATE conversaciones
SET ultimo_mensaje = NOW()
WHERE id=?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param(
"i",
$conversacion
);
$stmtUpdate->execute();

//RESPUESTA//
echo "OK";
