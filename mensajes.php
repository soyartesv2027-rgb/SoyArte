<?php
session_start();
require_once "php/conexion.php";
<<<<<<< HEAD
=======

>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
<<<<<<< HEAD
$usuarioActual = $_SESSION['usuario_id'];
$sql = "
SELECT
    c.id,
    c.producto_id,
    c.usuario1_id,
    c.usuario2_id,
    c.ultimo_mensaje,

    p.nombre AS producto,
    p.imagen,
    p.precio,

    u1.nombre AS usuario1_nombre,
    u2.nombre AS usuario2_nombre

FROM conversaciones c
INNER JOIN productos p
ON c.producto_id = p.id
INNER JOIN usuarios u1
ON c.usuario1_id = u1.id
INNER JOIN usuarios u2
ON c.usuario2_id = u2.id
WHERE
c.usuario1_id = ?
OR
c.usuario2_id = ?
ORDER BY c.ultimo_mensaje DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ii",
    $usuarioActual,
    $usuarioActual
);
$stmt->execute();
$conversaciones = $stmt->get_result();
=======

>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
=======
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
    <title>Mensajes | SoyArte</title>
    <link rel="stylesheet" href="styles/mensajes.css">
</head>
<body>
<<<<<<< HEAD
    <h1>Mis conversaciones</h1>
    <?php while($chat = $conversaciones->fetch_assoc()): ?>
        <div>
        <?php
        if($chat['usuario1_id'] == $usuarioActual){
            echo $chat['usuario2_nombre'];
        }else{
            echo $chat['usuario1_nombre'];
        }
        ?>
    <?php endwhile; ?>

</div>
</body>
</html>
=======
    <main class="contenedor-mensajes">
        <h1>Mis conversaciones</h1>

        <div class="lista-conversaciones" id="listaConversaciones">
            <?php require "php/obtener_conversaciones.php"; ?>
        </div>
    </main>

    <script src="JavaScript/mensajes.js"></script>
</body>
</html>
>>>>>>> 682a91e15b08aca335d43e066466df33210a2e4b
