<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit();
}

$usuarioActual = (int)$_SESSION['usuario_id'];

$sql = "SELECT
            c.id,
            c.producto_id,
            c.usuario1_id,
            c.usuario2_id,
            c.ultimo_mensaje,
            p.nombre AS producto,
            p.imagen,
            p.precio,
            u1.nombre AS usuario1_nombre,
            u2.nombre AS usuario2_nombre,
            ultimo.mensaje AS ultimo_texto,
            ultimo.tipo AS ultimo_tipo,
            ultimo.fecha AS ultimo_fecha,
            (
                SELECT COUNT(*)
                FROM mensajes pendientes
                WHERE pendientes.conversacion_id = c.id
                AND pendientes.emisor_id <> ?
                AND pendientes.leido = 0
            ) AS no_leidos
        FROM conversaciones c
        INNER JOIN productos p
        ON c.producto_id = p.id
        INNER JOIN usuarios u1
        ON c.usuario1_id = u1.id
        INNER JOIN usuarios u2
        ON c.usuario2_id = u2.id
        LEFT JOIN mensajes ultimo
        ON ultimo.id = (
            SELECT m.id
            FROM mensajes m
            WHERE m.conversacion_id = c.id
            ORDER BY m.fecha DESC, m.id DESC
            LIMIT 1
        )
        WHERE
        (
            c.usuario1_id = ?
            AND c.oculto_usuario1 = 0
        )
        OR
        (
            c.usuario2_id = ?
            AND c.oculto_usuario2 = 0
        )
        ORDER BY COALESCE(ultimo.fecha, c.ultimo_mensaje, c.fecha_creacion) DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $usuarioActual, $usuarioActual, $usuarioActual);
$stmt->execute();
$conversaciones = $stmt->get_result();

if ($conversaciones->num_rows === 0) {
    ?>
    <p class="sin-mensajes">Aun no tienes conversaciones.</p>
    <?php
    exit();
}

while ($chat = $conversaciones->fetch_assoc()) {
    $nombreContacto = ((int)$chat['usuario1_id'] === $usuarioActual)
        ? $chat['usuario2_nombre']
        : $chat['usuario1_nombre'];

    $ultimoMensaje = "Sin mensajes todavia.";

    if (!empty($chat['ultimo_fecha'])) {
        if ($chat['ultimo_tipo'] === "imagen") {
            $ultimoMensaje = trim((string)$chat['ultimo_texto']) !== ""
                ? $chat['ultimo_texto']
                : "Imagen enviada";
        } else {
            $ultimoMensaje = $chat['ultimo_texto'];
        }
    }

    $hora = !empty($chat['ultimo_fecha'])
        ? date("h:i A", strtotime($chat['ultimo_fecha']))
        : date("h:i A", strtotime($chat['ultimo_mensaje']));

    $noLeidos = (int)$chat['no_leidos'];
    ?>

    <a
        class="conversacion <?php echo $noLeidos > 0 ? "conversacion-pendiente" : ""; ?>"
        href="chat.php?id=<?php echo (int)$chat['id']; ?>">
        <img
            src="uploads/<?php echo htmlspecialchars($chat['imagen']); ?>"
            alt="<?php echo htmlspecialchars($chat['producto']); ?>">

        <div class="info-conversacion">
            <div class="fila-conversacion">
                <strong><?php echo htmlspecialchars($nombreContacto); ?></strong>
                <time><?php echo htmlspecialchars($hora); ?></time>
            </div>

            <span><?php echo htmlspecialchars($chat['producto']); ?></span>

            <div class="resumen-conversacion">
                <p><?php echo htmlspecialchars($ultimoMensaje); ?></p>

                <?php if ($noLeidos > 0) { ?>
                    <b><?php echo $noLeidos; ?></b>
                <?php } ?>
            </div>
        </div>
    </a>

    <?php
}
