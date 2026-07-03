<?php
session_start();
require_once "conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit();
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$conversacion = isset($_GET['conversacion'])
    ? (int)$_GET['conversacion']
    : 0;

if ($conversacion <= 0) {
    exit();
}

$sql = "SELECT *
        FROM conversaciones
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversacion);
$stmt->execute();
$chat = $stmt->get_result()->fetch_assoc();

if (!$chat) {
    exit();
}

if (
    $usuarioActual !== (int)$chat['usuario1_id'] &&
    $usuarioActual !== (int)$chat['usuario2_id']
) {
    exit();
}

$sqlEstado = "UPDATE mensajes
              SET estado = 2
              WHERE conversacion_id = ?
              AND emisor_id <> ?
              AND estado = 1";
$stmtEstado = $conn->prepare($sqlEstado);
$stmtEstado->bind_param("ii", $conversacion, $usuarioActual);
$stmtEstado->execute();

$sql = "SELECT
            m.*,
            u.nombre
        FROM mensajes m
        INNER JOIN usuarios u
        ON m.emisor_id = u.id
        WHERE conversacion_id = ?
        ORDER BY fecha ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversacion);
$stmt->execute();
$resultado = $stmt->get_result();

while ($mensaje = $resultado->fetch_assoc()) {
    $esMio = ((int)$mensaje['emisor_id'] === $usuarioActual);
    $clase = $esMio ? "mio" : "otro";
    ?>

    <div class="mensaje <?php echo $clase; ?>">
        <?php if (!$esMio) { ?>
            <div class="nombre-chat">
                <?php echo htmlspecialchars($mensaje['nombre']); ?>
            </div>
        <?php } ?>

        <div class="burbuja">
            <?php if ($mensaje['tipo'] === "imagen") { ?>
                <?php if (!empty($mensaje['archivo'])) { ?>
                    <img
                        src="uploads/chat/<?php echo htmlspecialchars($mensaje['archivo']); ?>"
                        class="imagen-chat"
                        alt="Imagen enviada"
                        onclick="abrirImagen(this.src)">
                <?php } ?>

                <?php if (!empty($mensaje['mensaje'])) { ?>
                    <div class="texto-imagen">
                        <?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?>
            <?php } ?>
        </div>

        <small class="info-mensaje">
            <span class="hora">
                <?php echo date("h:i A", strtotime($mensaje['fecha'])); ?>
            </span>

            <?php if ($esMio) {
                $iconoEstado = "";
                $claseEstado = "";

                switch ((int)$mensaje['estado']) {
                    case 1:
                        $iconoEstado = "&#10003;";
                        $claseEstado = "estado-enviado";
                        break;
                    case 2:
                        $iconoEstado = "&#10003;&#10003;";
                        $claseEstado = "estado-entregado";
                        break;
                    case 3:
                        $iconoEstado = "&#10003;&#10003;";
                        $claseEstado = "estado-leido";
                        break;
                }
                ?>

                <span class="estado-mensaje <?php echo $claseEstado; ?>">
                    <?php echo $iconoEstado; ?>
                </span>
            <?php } ?>
        </small>
    </div>

    <?php
}
