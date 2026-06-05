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
    $video = trim($_POST['video']);

    $portada = "";

    if(isset($_FILES['portada']) && $_FILES['portada']['error'] == 0){

        $portada = time() . "_" . basename($_FILES['portada']['name']);

        move_uploaded_file(
            $_FILES['portada']['tmp_name'],
            "uploads/musica/" . $portada
        );
    }

    $sql = "INSERT INTO musica
    (
        usuario_id,
        nombre_cancion,
        nombre_cantante,
        descripcion,
        video,
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
        $video,
        $portada
    );

    if($stmt->execute()){

        $mensaje = "✅ Música publicada correctamente";

    }else{

        $mensaje = "❌ Error al guardar la publicación";

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

            <label>Nombre de la canción</label>

            <input
                type="text"
                name="nombre_cancion"
                required
            >

            <label>Nombre del cantante</label>

            <input
                type="text"
                name="nombre_cantante"
                required
            >

            <label>Descripción</label>

            <textarea
                name="descripcion"
                required
            ></textarea>

            <label>Video de YouTube (Embed)</label>

            <input
                type="text"
                name="video"
                placeholder="https://www.youtube.com/embed/..."
                required
            >

            <label>Portada</label>

            <input
                type="file"
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