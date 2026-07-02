<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit("Debes iniciar sesión.");
}
$usuarioActual = $_SESSION['usuario_id'];

//RECIBIR DATOS //
$conversacion = isset($_POST['conversacion'])
    ? intval($_POST['conversacion'])
    : 0;

$mensaje = isset($_POST['mensaje'])
    ? trim($_POST['mensaje'])
    : "";

// RECIBIR IMAGEN
$imagen = null;

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] === UPLOAD_ERR_OK
) {
    $imagen = $_FILES['imagen'];
}
//VALIDACIONES //

if ($conversacion <= 0) {
    exit("Conversación inválida.");
}

if ($mensaje == "" && $imagen === null) {
    exit("Debes escribir un mensaje o seleccionar una imagen.");
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

if ($imagen !== null) {

    $extension = strtolower(pathinfo($imagen["name"], PATHINFO_EXTENSION));

    $permitidas = ["jpg","jpeg","png","gif","webp"];
    // Tamaño máximo: 5 MB
    if($imagen["size"] > 5 * 1024 * 1024){

        exit("La imagen supera los 5 MB.");

    }
    if (!in_array($extension,$permitidas)) {

        exit("Formato de imagen no permitido.");

    }

    $nombreArchivo = uniqid("chat_").".".$extension;

    move_uploaded_file(
        $imagen["tmp_name"],
        "../uploads/chat/".$nombreArchivo
    );

    $tipoMensaje = "imagen";

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