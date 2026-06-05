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

    $sql = "UPDATE realidad_virtual
            SET titulo=?,
                descripcion=?,
                video_url=?,
                enlace=?
            WHERE id=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssi",
        $titulo,
        $descripcion,
        $video_url,
        $enlace,
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

        <form method="POST" class="form-vr">

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