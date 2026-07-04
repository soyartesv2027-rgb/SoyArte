<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
<<<<<<< HEAD
    exit("Debes iniciar sesión.");
}
$usuarioActual = $_SESSION['usuario_id'];

//RECIBIR DATOS //
$conversacion = isset($_POST['conversacion'])
    ? intval($_POST['conversacion'])
=======
    exit("Debes iniciar sesion.");
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$conversacion = isset($_POST['conversacion'])
    ? (int)$_POST['conversacion']
>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
    : 0;

$mensaje = isset($_POST['mensaje'])
    ? trim($_POST['mensaje'])
    : "";

<<<<<<< HEAD
// RECIBIR IMAGEN
=======
>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
$imagen = null;

if (
    isset($_FILES['imagen']) &&
    $_FILES['imagen']['error'] === UPLOAD_ERR_OK
) {
    $imagen = $_FILES['imagen'];
}
<<<<<<< HEAD
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
=======

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
>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
    exit("No autorizado.");
}

$nombreArchivo = null;
$tipoMensaje = "texto";

if ($imagen !== null) {
<<<<<<< HEAD

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
=======
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
>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
