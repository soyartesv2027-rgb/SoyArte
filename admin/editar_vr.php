<?php
session_start();
require_once '../php/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    die("Acceso denegado");
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare(
    "SELECT * FROM realidad_virtual WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$vr = $stmt->get_result()->fetch_assoc();

if(!$vr){
    die("Experiencia no encontrada");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $video_url = $_POST['video_url'];
    $enlace = $_POST['enlace'];
    $portada = $vr['portada'];
    $qr = $vr['qr_imagen'];

    $carpetaPortadas = "../uploads/vr/portadas/";
    $carpetaQr = "../uploads/vr/qr/";

    if (!is_dir($carpetaPortadas)) {
        mkdir($carpetaPortadas, 0777, true);
    }

    if (!is_dir($carpetaQr)) {
        mkdir($carpetaQr, 0777, true);
    }

    if (isset($_FILES['portada']) && $_FILES['portada']['error'] == UPLOAD_ERR_OK) {
        $nuevaPortada = time() . "_portada_" . basename($_FILES['portada']['name']);

        if (move_uploaded_file($_FILES['portada']['tmp_name'], $carpetaPortadas . $nuevaPortada)) {
            if (!empty($portada) && file_exists($carpetaPortadas . $portada)) {
                unlink($carpetaPortadas . $portada);
            }

            $portada = $nuevaPortada;
        }
    }

    if (isset($_FILES['qr']) && $_FILES['qr']['error'] == UPLOAD_ERR_OK) {
        $nuevoQr = time() . "_qr_" . basename($_FILES['qr']['name']);

        if (move_uploaded_file($_FILES['qr']['tmp_name'], $carpetaQr . $nuevoQr)) {
            if (!empty($qr) && file_exists($carpetaQr . $qr)) {
                unlink($carpetaQr . $qr);
            }

            $qr = $nuevoQr;
        }
    }

    $sql = "UPDATE realidad_virtual
            SET titulo=?,
                descripcion=?,
                video_url=?,
                enlace=?,
                portada=?,
                qr_imagen=?
            WHERE id=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssssi",
        $titulo,
        $descripcion,
        $video_url,
        $enlace,
        $portada,
        $qr,
        $id
    );

    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar VR</title>
<link rel="stylesheet" href="../styles/form_vr.css">
</head>
<body>

<div class="contenedor">

    <div class="card-form">

        <h1>✏️ Editar Experiencia VR</h1>

        <form method="POST" enctype="multipart/form-data" class="form-vr">

            <label>Título</label>
            <input
                type="text"
                name="titulo"
                value="<?php echo htmlspecialchars($vr['titulo']); ?>"
                required
            >

            <label>Descripción</label>
            <textarea
                name="descripcion"
                required
            ><?php echo htmlspecialchars($vr['descripcion']); ?></textarea>

            <label>Video URL</label>
            <input
                type="text"
                name="video_url"
                value="<?php echo htmlspecialchars($vr['video_url']); ?>"
            >

            <label>Enlace Externo</label>
            <input
                type="text"
                name="enlace"
                value="<?php echo htmlspecialchars($vr['enlace']); ?>"
            >

            <label>Portada actual</label>
            <img
                src="../uploads/vr/portadas/<?php echo htmlspecialchars($vr['portada']); ?>"
                alt="Portada actual"
                style="max-width:180px;border-radius:12px;"
            >

            <label>Cambiar portada</label>
            <input
                type="file"
                name="portada"
            >

            <label>QR actual</label>
            <img
                src="../uploads/vr/qr/<?php echo htmlspecialchars($vr['qr_imagen']); ?>"
                alt="QR actual"
                style="max-width:180px;border-radius:12px;"
            >

            <label>Cambiar QR</label>
            <input
                type="file"
                name="qr"
            >

            <div class="botones">

                <button type="submit" class="btn-guardar">
                    Guardar Cambios
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
