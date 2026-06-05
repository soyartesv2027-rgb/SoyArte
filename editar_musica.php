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
$stmt->bind_param("i",$id);
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

    $nombre_cancion = $_POST['nombre_cancion'];
    $nombre_cantante = $_POST['nombre_cantante'];
    $descripcion = $_POST['descripcion'];
    $video = $_POST['video'];

    $portada = $musica['portada'];

    if(
        isset($_FILES['portada']) &&
        $_FILES['portada']['error'] == 0
    ){

        $portada = time() . "_" . $_FILES['portada']['name'];

        move_uploaded_file(
            $_FILES['portada']['tmp_name'],
            "uploads/musica/" . $portada
        );
    }

    $sqlUpdate = "
        UPDATE musica
        SET
            nombre_cancion=?,
            nombre_cantante=?,
            descripcion=?,
            video=?,
            portada=?
        WHERE musica_id=?
    ";

    $stmt = $conn->prepare($sqlUpdate);

    $stmt->bind_param(
        "sssssi",
        $nombre_cancion,
        $nombre_cantante,
        $descripcion,
        $video,
        $portada,
        $id
    );

    if($stmt->execute()){

        header("Location: ver_musica.php?id=".$id);
        exit();

    }else{

        $mensaje = "Error al actualizar";

    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar Música</title>

<link rel="stylesheet" href="styles/publicar_musica.css">
</head>
<body>

<div class="contenedor">

    <div class="card-form">

        <a href="ver_musica.php?id=<?php echo $id; ?>" class="btn-volver">
            ← Volver
        </a>

        <h1>✏️ Editar Música</h1>

        <?php if($mensaje): ?>

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

            <label>Video de YouTube</label>

            <input
                type="text"
                name="video"
                value="<?php echo htmlspecialchars($musica['video']); ?>"
                required
            >

            <label>Portada actual</label>

            <img
                src="uploads/musica/<?php echo $musica['portada']; ?>"
                class="preview-portada"
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