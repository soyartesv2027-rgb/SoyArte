<?php
require_once 'php/conexion.php';

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM realidad_virtual WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$vr = $stmt->get_result()->fetch_assoc();

if(!$vr){
    die("Experiencia no encontrada");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?php echo $vr['titulo']; ?></title>
<link rel="stylesheet" href="styles/ver_vr.css">
</head>
<body>

<div class="detalle-vr">

    <a href="realidad_virtual.php" class="btn-volver">
        ← Volver
    </a>

    <h1>
        <?php echo htmlspecialchars($vr['titulo']); ?>
    </h1>

    <div class="video">

        <iframe
            src="<?php echo htmlspecialchars($vr['video_url']); ?>"
            allowfullscreen>
        </iframe>

    </div>

    <p class="descripcion">
        <?php echo nl2br(htmlspecialchars($vr['descripcion'])); ?>
    </p>

    <div class="enlace">

        🔗

        <a
            href="<?php echo $vr['enlace']; ?>"
            target="_blank"
        >
            Visitar experiencia
        </a>

    </div>

    <div class="extra">

        <div class="qr">

            <img
                src="uploads/vr/qr/<?php echo $vr['qr_imagen']; ?>"
            >

        </div>

        <div class="comentarios">

            <h3>Comentarios</h3>

            <textarea readonly>
            Próximamente disponible...
            </textarea>

        </div>

    </div>

</div>

</body>
</html>