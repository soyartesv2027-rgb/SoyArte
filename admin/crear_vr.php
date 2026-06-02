<?php
session_start();
require_once '../php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SESSION['rol'] != 'admin') {
    die("Acceso denegado");
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $video_url = $_POST['video_url'];
    $enlace = $_POST['enlace'];

    $portada = time() . "_portada_" . $_FILES['portada']['name'];

    move_uploaded_file(
        $_FILES['portada']['tmp_name'],
        "../uploads/vr/portadas/" . $portada
    );

    $qr = time() . "_qr_" . $_FILES['qr']['name'];

    move_uploaded_file(
        $_FILES['qr']['tmp_name'],
        "../uploads/vr/qr/" . $qr
    );

    $sql = "INSERT INTO realidad_virtual
            (titulo,descripcion,portada,video_url,enlace,qr_imagen)
            VALUES (?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssss",
        $titulo,
        $descripcion,
        $portada,
        $video_url,
        $enlace,
        $qr
    );

    if($stmt->execute()){
        $mensaje = "✅ Experiencia creada correctamente";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear VR</title>
<link rel="stylesheet" href="../styles/form_vr.css">
</head>
<body>
<div class="contenedor">

    <div class="card-form">

        <h1>🥽 Nueva Experiencia VR</h1>

        <?php if($mensaje): ?>
            <div class="mensaje">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-vr">

            <label>Título</label>
            <input
                type="text"
                name="titulo"
                required
            >

            <label>Descripción</label>
            <textarea
                name="descripcion"
                required
            ></textarea>

            <label>Video URL (Embed)</label>
            <input
                type="text"
                name="video_url"
                required
            >

            <label>Enlace Externo</label>
            <input
                type="text"
                name="enlace"
            >

            <label>Portada</label>
            <input
                type="file"
                name="portada"
                required
            >

            <label>Código QR</label>
            <input
                type="file"
                name="qr"
                required
            >

            <div class="botones">

                <button type="submit" class="btn-guardar">
                    Publicar Experiencia
                </button>

                <a href="dashboard.php" class="btn-cancelar">
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>