<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.html");
    exit();
}

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM musica WHERE musica_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$musica = $stmt->get_result()->fetch_assoc();

if(!$musica){
    die("Canción no encontrada");
}

if($_SESSION['usuario_id'] != $musica['usuario_id']){
    die("No tienes permiso para editar esta publicación");
}

$mensaje = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $nombre_cancion = trim($_POST['nombre_cancion']);
    $nombre_cantante = trim($_POST['nombre_cantante']);
    $descripcion = trim($_POST['descripcion']);

    $audio = $musica['audio'];
    $portada = $musica['portada'];

    // CAMBIAR AUDIO
    if(
        isset($_FILES['audio']) &&
        $_FILES['audio']['error'] == 0
    ){

        $extension = strtolower(
            pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION)
        );

        $permitidos = ['mp3','wav','ogg'];

        if(in_array($extension, $permitidos)){

            $audio = time() . "_audio." . $extension;

            move_uploaded_file(
                $_FILES['audio']['tmp_name'],
                "uploads/musica/" . $audio
            );
        }
    }

    // CAMBIAR PORTADA
    if(
        isset($_FILES['portada']) &&
        $_FILES['portada']['error'] == 0
    ){

        $extensionPortada = strtolower(
            pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION)
        );

        $portada = time() . "_cover." . $extensionPortada;

        move_uploaded_file(
            $_FILES['portada']['tmp_name'],
            "uploads/musica/" . $portada
        );
    }

    $sqlUpdate = "
        UPDATE musica
        SET
            nombre_cancion = ?,
            nombre_cantante = ?,
            descripcion = ?,
            audio = ?,
            portada = ?
        WHERE musica_id = ?
    ";

    $stmt = $conn->prepare($sqlUpdate);

    $stmt->bind_param(
        "sssssi",
        $nombre_cancion,
        $nombre_cantante,
        $descripcion,
        $audio,
        $portada,
        $id
    );

    if($stmt->execute()){

        header("Location: ver_musica.php?id=" . $id);
        exit();

    }else{

        $mensaje = "❌ Error al actualizar la publicación";

    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Música</title>

<link rel="stylesheet" href="styles/publicar_musica.css?v=<?php echo time(); ?>">
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">

<style>
.preview-portada{
    width:200px;
    border-radius:10px;
    margin-top:10px;
    margin-bottom:15px;
}

.audio-preview{
    width:100%;
    margin-top:10px;
    margin-bottom:15px;
}
</style>

</head>
<body>

<div class="contenedor">

    <div class="card-form">

        <a href="ver_musica.php?id=<?php echo $id; ?>" class="btn-volver">
            ← Volver
        </a>

        <h1>✏️ Editar Música</h1>

        <?php if(!empty($mensaje)): ?>

            <div class="mensaje">
                <?php echo $mensaje; ?>
            </div>

        <?php endif; ?>

        <form
            method="POST"
            enctype="multipart/form-data"
            class="form-musica"
        >

            <label>Nombre de la canción</label>

            <input
                type="text"
                name="nombre_cancion"
                value="<?php echo htmlspecialchars($musica['nombre_cancion']); ?>"
                required
            >

            <label>Nombre del cantante</label>

            <input
                type="text"
                name="nombre_cantante"
                value="<?php echo htmlspecialchars($musica['nombre_cantante']); ?>"
                required
            >

            <label>Descripción</label>

            <textarea
                name="descripcion"
                required
            ><?php echo htmlspecialchars($musica['descripcion']); ?></textarea>

            <label>Audio actual</label>

            <audio controls class="audio-preview">

                <source
                    src="uploads/musica/<?php echo htmlspecialchars($musica['audio']); ?>">

            </audio>

            <label>Cambiar audio</label>

            <input
                type="file"
                name="audio"
                accept=".mp3,.wav,.ogg"
            >

            <label>Portada actual</label>

            <img
                src="uploads/musica/<?php echo htmlspecialchars($musica['portada']); ?>"
                class="preview-portada"
                alt="Portada"
            >

            <label>Cambiar portada</label>

            <input
                type="file"
                name="portada"
                accept="image/*"
            >

            <button type="submit">
                Guardar Cambios
            </button>

        </form>

    </div>

</div>

</body>
</html>