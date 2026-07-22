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

    $carpetaPortadas = "../uploads/vr/portadas/";
    $carpetaQr = "../uploads/vr/qr/";

    if (!is_dir($carpetaPortadas)) {
        mkdir($carpetaPortadas, 0777, true);
    }

    if (!is_dir($carpetaQr)) {
        mkdir($carpetaQr, 0777, true);
    }

    if ($_FILES['portada']['error'] !== UPLOAD_ERR_OK || $_FILES['qr']['error'] !== UPLOAD_ERR_OK) {
        $mensaje = "Error: no se pudieron subir la portada o el QR.";
    } else {
        $portada = time() . "_portada_" . basename($_FILES['portada']['name']);
        $qr = time() . "_qr_" . basename($_FILES['qr']['name']);

        $portadaGuardada = move_uploaded_file(
            $_FILES['portada']['tmp_name'],
            $carpetaPortadas . $portada
        );

        $qrGuardado = move_uploaded_file(
            $_FILES['qr']['tmp_name'],
            $carpetaQr . $qr
        );

        if (!$portadaGuardada || !$qrGuardado) {
            $mensaje = "Error: no se pudieron guardar la portada o el QR.";
        } else {
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

            if ($stmt->execute()) {
                $mensaje = "Experiencia creada correctamente";
            } else {
                $mensaje = "Error: no se pudo guardar la experiencia.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Crear VR</title>
<link rel="stylesheet" href="../styles/form_vr.css">
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>
<div class="contenedor">

    <div class="card-form">

        <h1>Nueva Experiencia VR</h1>

        <?php if($mensaje): ?>
            <div class="mensaje">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-vr">

            <label>T&iacute;tulo</label>
            <input
                type="text"
                name="titulo"
                required
            >

            <label>Descripci&oacute;n</label>
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

            <label>C&oacute;digo QR</label>
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
