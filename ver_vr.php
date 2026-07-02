<?php
session_start();
require_once 'php/conexion.php';

// Obtener ID de la experiencia VR
$id = $_GET['id'] ?? 0;

// Validar que el ID sea numérico
if (!$id || !is_numeric($id)) {
    die("ID inválido");
}

// Obtener experiencia VR
$sql = "SELECT * FROM realidad_virtual WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$vr = $stmt->get_result()->fetch_assoc();

if (!$vr) {
    die("Experiencia no encontrada");
}

// Contar comentarios
$sqlTotal = "SELECT COUNT(*) AS total FROM comentarios_vr WHERE vr_id = ?";
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bind_param("i", $vr['id']);
$stmtTotal->execute();
$total = $stmtTotal->get_result()->fetch_assoc();

// Obtener comentarios con nombres de usuarios
$sqlComentarios = "
    SELECT c.*, u.nombre
    FROM comentarios_vr c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.vr_id = ?
    ORDER BY c.fecha DESC
";
$stmtComentarios = $conn->prepare($sqlComentarios);
$stmtComentarios->bind_param("i", $vr['id']);
$stmtComentarios->execute();
$resultado = $stmtComentarios->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($vr['titulo']); ?></title>
    <link rel="stylesheet" href="styles/ver_vr.css?v=<?php echo filemtime(__DIR__ . '/styles/ver_vr.css'); ?>">
</head>
<body>
    <div class="detalle-vr">
        <a href="realidad_virtual.php" class="btn-volver">← Volver</a>

        <h1><?php echo htmlspecialchars($vr['titulo']); ?></h1>

        <div class="video">
            <iframe src="<?php echo htmlspecialchars($vr['video_url']); ?>" allowfullscreen></iframe>
        </div>

        <p class="descripcion">
            <?php echo nl2br(htmlspecialchars($vr['descripcion'])); ?>
        </p>

        <div class="enlace">
            🔗 <a href="<?php echo htmlspecialchars($vr['enlace']); ?>" target="_blank">Visitar experiencia</a>
        </div>

        <div class="extra">
            <div class="qr">
                <img src="uploads/vr/qr/<?php echo htmlspecialchars($vr['qr_imagen']); ?>" alt="Código QR">
            </div>

            <div class="comentarios">
                <h3>💬 Comentarios (<?php echo (int)$total['total']; ?>)</h3>

                <form action="php/guardar-comentario.php" method="POST">
                    <input type="hidden" name="vr_id" value="<?php echo (int)$vr['id']; ?>">
                    <textarea name="comentario" placeholder="Comparte tu opinión sobre esta experiencia..." required></textarea>
                    <button type="submit" class="btn-comentar">Publicar comentario</button>
                </form>

                <hr>

                <div class="lista-comentarios">
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <div class="comentario">
                            <div class="comentario-header">
                                <div class="avatar">
                                    <?php echo strtoupper(substr($fila['nombre'], 0, 1)); ?>
                                </div>
                                <div>
                                    <strong><?php echo htmlspecialchars($fila['nombre']); ?></strong><br>
                                    <small><?php echo date("d/m/Y H:i", strtotime($fila['fecha'])); ?></small>
                                </div>
                            </div>
                            <p class="texto-comentario">
                                <?php echo nl2br(htmlspecialchars($fila['comentario'])); ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
