<?php
session_start();
require_once 'php/conexion.php';

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.html");
    exit();
}

$mensaje = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $usuario_id = $_SESSION['usuario_id'];

    $nombre_cancion = trim($_POST['nombre_cancion']);
    $nombre_cantante = trim($_POST['nombre_cantante']);
    $descripcion = trim($_POST['descripcion']);

    $audio = "";
    $portada = "";

    // SUBIR AUDIO
    if(isset($_FILES['audio']) && $_FILES['audio']['error'] == 0){

        $permitidos = ['mp3','wav','ogg'];

        $extension = strtolower(
            pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION)
        );

        if(!in_array($extension, $permitidos)){
            $mensaje = "❌ Formato de audio no permitido";
        }else{

            if($_FILES['audio']['size'] > 20 * 1024 * 1024){
                $mensaje = "❌ El audio supera los 20MB";
            }else{

                $audio = time() . "_audio." . $extension;

                move_uploaded_file(
                    $_FILES['audio']['tmp_name'],
                    "uploads/musica/" . $audio
                );
            }
        }
    }

    // SUBIR PORTADA
    if(isset($_FILES['portada']) && $_FILES['portada']['error'] == 0){

        $extensionPortada = strtolower(
            pathinfo($_FILES['portada']['name'], PATHINFO_EXTENSION)
        );

        $portada = time() . "_cover." . $extensionPortada;

        move_uploaded_file(
            $_FILES['portada']['tmp_name'],
            "uploads/musica/" . $portada
        );
    }

    if(empty($mensaje)){

        $sql = "INSERT INTO musica
        (
            usuario_id,
            nombre_cancion,
            nombre_cantante,
            descripcion,
            audio,
            portada
        )
        VALUES (?,?,?,?,?,?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "isssss",
            $usuario_id,
            $nombre_cancion,
            $nombre_cantante,
            $descripcion,
            $audio,
            $portada
        );

        if($stmt->execute()){

            $mensaje = "✅ Música publicada correctamente";

        }else{

            $mensaje = "❌ Error al guardar la publicación";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Publicar Música</title>

<link rel="stylesheet" href="styles/publicar_musica.css">
</head>
<body>

<div class="contenedor">

    <div class="card-form">

        <a href="musica.php" class="btn-volver">
            ← Volver
        </a>

        <h1>🎵 Publicar Música</h1>

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

    <label for="nombre_cancion">
        Nombre de la canción
    </label>

    <input
        type="text"
        id="nombre_cancion"
        name="nombre_cancion"
        required
    >

    <label for="nombre_cantante">
        Nombre del cantante
    </label>

    <input
        type="text"
        id="nombre_cantante"
        name="nombre_cantante"
        required
    >

    <label for="descripcion">
        Descripción
    </label>

    <textarea
        id="descripcion"
        name="descripcion"
        required
    ></textarea>

    <label for="audio">
        Archivo de audio
    </label>

    <input
        type="file"
        id="audio"
        name="audio"
        accept=".mp3,.wav,.ogg"
        required
    >

    <label for="portada">
        Portada
    </label>

    <input
        type="file"
        id="portada"
        name="portada"
        accept="image/*"
        required
    >

    <button type="submit">
        Publicar Música
    </button>

</form>

    </div>

</div>

</body>
</html>