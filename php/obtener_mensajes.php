<?php
session_start();
require_once "conexion.php";
if (!isset($_SESSION['usuario_id'])) {
    exit();
}
$usuarioActual = $_SESSION['usuario_id'];

//RECIBIR CONVERSACIÓN //
$conversacion = isset($_GET['conversacion'])
    ? intval($_GET['conversacion'])
    : 0;
if ($conversacion <= 0) {
    exit();
}

// VERIFICAR QUE LA CONVERSACIÓN EXISTE //
$sql = "SELECT *
    FROM conversaciones
    WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$conversacion);
$stmt->execute();
$chat = $stmt->get_result()->fetch_assoc();
if(!$chat){
    exit();
}

// VERIFICAR PERMISOS //
// MARCAR MENSAJES COMO ENTREGADOS
$sqlEstado = "
UPDATE mensajes
SET estado = 2
WHERE conversacion_id = ?
AND emisor_id <> ?
AND estado = 1
";

$stmtEstado = $conn->prepare($sqlEstado);
$stmtEstado->bind_param(
    "ii",
    $conversacion,
    $usuarioActual
);
$stmtEstado->execute();
if(

$usuarioActual != $chat['usuario1_id']
&&
$usuarioActual != $chat['usuario2_id']
){
    exit();
}

// OBTENER MENSAJES //
$sql = "
SELECT
m.*,
u.nombre
FROM mensajes m
INNER JOIN usuarios u
ON m.emisor_id=u.id
WHERE conversacion_id=?
ORDER BY fecha_envio ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$conversacion);
$stmt->execute();
$resultado = $stmt->get_result();

// MOSTRAR MENSAJES //
while($mensaje = $resultado->fetch_assoc()){

    $esMio = ($mensaje['emisor_id'] == $usuarioActual);

    $clase = $esMio ? "mio" : "otro";
?>

<div class="mensaje <?php echo $clase; ?>">

    <?php if(!$esMio){ ?>

        <div class="nombre-chat">
            <?php echo htmlspecialchars($mensaje['nombre']); ?>
        </div>

    <?php } ?>

    <div class="burbuja">

        <?php
        // SI ES UNA IMAGEN
        if($mensaje['tipo'] == "imagen"){

            if(!empty($mensaje['archivo'])){
                ?>

                <img
                    src="uploads/chat/<?php echo htmlspecialchars($mensaje['archivo']); ?>"
                    class="imagen-chat"
                    alt="Imagen enviada"
                    onclick="abrirImagen(this.src)">

                <?php
            }

            if(!empty($mensaje['mensaje'])){
                ?>

                <div class="texto-imagen">
                    <?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?>
                </div>

                <?php
            }

        }else{

            // MENSAJE NORMAL
            echo nl2br(htmlspecialchars($mensaje['mensaje']));

        }
        ?>

    </div>

    <small class="info-mensaje">

        <span class="hora">
            <?php echo date("h:i A", strtotime($mensaje['fecha_envio'])); ?>
        </span>

        <?php if($esMio){ ?>

            <?php

            $iconoEstado = "";
            $claseEstado = "";

            switch($mensaje['estado']){

                case 1:
                    $iconoEstado = "✓";
                    $claseEstado = "estado-enviado";
                    break;

                case 2:
                    $iconoEstado = "✓✓";
                    $claseEstado = "estado-entregado";
                    break;

                case 3:
                    $iconoEstado = "✓✓";
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