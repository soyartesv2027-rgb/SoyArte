<?php
session_start();
require_once "php/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes | SoyArte</title>
    <link rel="stylesheet" href="styles/mensajes.css">
</head>
<body>
    <main class="contenedor-mensajes">
        <h1>Mis conversaciones</h1>

        <div class="lista-conversaciones" id="listaConversaciones">
            <?php require "php/obtener_conversaciones.php"; ?>
        </div>
    </main>

    <script src="JavaScript/mensajes.js"></script>
</body>
</html>
