<?php
session_start();
require_once "php/conexion.php";
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
$usuarioActual = (int)$_SESSION['usuario_id'];

$sql = "
SELECT
    c.id,
    c.usuario1_id,
    c.usuario2_id,
    c.producto_id,
    c.ultimo_mensaje,
    u1.nombre AS nombre1,
    u1.foto_perfil AS foto1,
    u2.nombre AS nombre2,
    u2.foto_perfil AS foto2,
    p.nombre AS producto_nombre,
    p.imagen AS producto_imagen,
    p.precio AS producto_precio,
    (
        SELECT m.mensaje
        FROM mensajes m
        WHERE m.conversacion_id = c.id
        ORDER BY m.fecha DESC
        LIMIT 1
    ) AS ultimo_texto,
    (
        SELECT COUNT(*)
        FROM mensajes m
        WHERE m.conversacion_id = c.id
        AND m.emisor_id <> ?
        AND m.leido = 0
    ) AS no_leidos
FROM conversaciones c
INNER JOIN usuarios u1
ON c.usuario1_id = u1.id
INNER JOIN usuarios u2
ON c.usuario2_id = u2.id
INNER JOIN productos p
ON c.producto_id = p.id
WHERE
(
    c.usuario1_id = ?
    OR
    c.usuario2_id = ?
)
AND
(
    (c.usuario1_id = ? AND c.oculto_usuario1 = 0)
    OR
    (c.usuario2_id = ? AND c.oculto_usuario2 = 0)
)
ORDER BY c.ultimo_mensaje DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $usuarioActual, $usuarioActual, $usuarioActual, $usuarioActual, $usuarioActual);
$stmt->execute();
$conversaciones = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes | SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles/mensajes.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("components/navbar.php"); ?>
    <div class="contenedor-mensajes">
        <div class="cabecera-mensajes">
            <a href="index.php" class="volver-inicio">← Volver al inicio</a>
            <h1>Mis conversaciones</h1>
        </div>

        <div class="lista-conversaciones">
            <?php if ($conversaciones->num_rows === 0): ?>
                <div class="sin-mensajes">
                    No tienes conversaciones activas.
                </div>
            <?php endif; ?>

            <?php while ($chat = $conversaciones->fetch_assoc()):
                $esUsuario1 = ($chat['usuario1_id'] == $usuarioActual);
                $otroNombre = $esUsuario1 ? $chat['nombre2'] : $chat['nombre1'];
                $otroFoto  = $esUsuario1 ? $chat['foto2'] : $chat['foto1'];
                $noLeidos  = (int)$chat['no_leidos'];
                $ultimoTexto = $chat['ultimo_texto'] ?? "";
                $ultimoTexto = mb_strlen($ultimoTexto) > 80
                    ? mb_substr($ultimoTexto, 0, 80) . "…"
                    : $ultimoTexto;

                $tiempo = "";
                if ($chat['ultimo_mensaje']) {
                    $hora = strtotime($chat['ultimo_mensaje']);
                    $diff = time() - $hora;
                    if ($diff < 60) {
                        $tiempo = "Ahora";
                    } elseif ($diff < 3600) {
                        $tiempo = "Hace " . floor($diff / 60) . " min";
                    } elseif ($diff < 86400) {
                        $tiempo = "Hace " . floor($diff / 3600) . " h";
                    } elseif ($diff < 172800) {
                        $tiempo = "Ayer";
                    } else {
                        $tiempo = date("d/m/Y", $hora);
                    }
                }

                $clasePendiente = $noLeidos > 0 ? " conversacion-pendiente" : "";
                $fotoSrc = !empty($otroFoto) ? "uploads/perfiles/" . htmlspecialchars($otroFoto) : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='72' height='72'%3E%3Ccircle cx='36' cy='36' r='36' fill='%23ddd'/%3E%3Ccircle cx='36' cy='27' r='10' fill='%23999'/%3E%3Cellipse cx='36' cy='54' rx='20' ry='14' fill='%23999'/%3E%3C/svg%3E";
                $prodImg = !empty($chat['producto_imagen']) ? "uploads/" . htmlspecialchars($chat['producto_imagen']) : "";
            ?>
                <a href="chat.php?id=<?php echo $chat['id']; ?>" class="conversacion<?php echo $clasePendiente; ?>">
                    <img
                        src="<?php echo $fotoSrc; ?>"
                        alt="Foto de <?php echo htmlspecialchars($otroNombre); ?>"
                        class="foto-usuario">

                    <div class="info-conversacion">
                        <div class="fila-conversacion">
                            <strong><?php echo htmlspecialchars($otroNombre); ?></strong>
                            <time><?php echo $tiempo; ?></time>
                        </div>

                        <div class="resumen-conversacion">
                            <p>
                                <?php if (!empty($ultimoTexto)): ?>
                                    <?php echo htmlspecialchars($ultimoTexto); ?>
                                <?php else: ?>
                                    <em>Sin mensajes aún</em>
                                <?php endif; ?>
                            </p>
                            <?php if ($noLeidos > 0): ?>
                                <b><?php echo $noLeidos; ?></b>
                            <?php endif; ?>
                        </div>

                        <div class="producto-conversacion">
                            <?php if ($prodImg): ?>
                                <img
                                    src="<?php echo $prodImg; ?>"
                                    alt="<?php echo htmlspecialchars($chat['producto_nombre']); ?>"
                                    class="foto-producto">
                            <?php endif; ?>
                            <span class="nombre-producto"><?php echo htmlspecialchars($chat['producto_nombre']); ?></span>
                            <span class="precio-producto">$<?php echo number_format($chat['producto_precio'], 2); ?></span>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="JavaScript/script.js"></script>
</body>
</html>
