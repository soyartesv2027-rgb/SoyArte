<?php

session_start();
require_once "php/conexion.php";
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
$usuarioActual = $_SESSION['usuario_id'];


// OBTENER EL ID DEL CHAT //
if (!isset($_GET['id'])) {
    die("Conversación no encontrada.");
}
$conversacionID = intval($_GET['id']);


// BUSCAR LA CONVERSACIÓN //
$sql = "SELECT *
        FROM conversaciones
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$conversacionID);
$stmt->execute();
$resultado = $stmt->get_result();
if($resultado->num_rows == 0){
    die("La conversación no existe.");
}
$conversacion = $resultado->fetch_assoc();

// VERIFICAR QUE EL USUARIO PERTENECE AL CHAT //
if(
    $usuarioActual != $conversacion['usuario1_id']
    &&
    $usuarioActual != $conversacion['usuario2_id']
){
    die("No tienes permiso para entrar a este chat.");
}


// OBTENER EL OTRO USUARIO //

$otroUsuario = ($usuarioActual == $conversacion['usuario1_id'])
    ? $conversacion['usuario2_id']
    : $conversacion['usuario1_id'];

//OBTENER DATOS DEL OTRO USUARIO //
$sqlUsuario = "SELECT id,nombre,foto_perfil
    FROM usuarios
    WHERE id=?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i",$otroUsuario);
$stmtUsuario->execute();

$usuario = $stmtUsuario->get_result()->fetch_assoc();

// OBTENER DATOS DEL PRODUCTO //
$sqlProducto = "SELECT id,nombre,precio,imagen
    FROM productos
    WHERE id=?";
$stmtProducto = $conn->prepare($sqlProducto);
$stmtProducto->bind_param("i",$conversacion['producto_id']);
$stmtProducto->execute();
$producto = $stmtProducto->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat | SoyArte</title>
    <link rel="stylesheet" href="styles/chat.css">
</head>
<body>

    <div class="contenedor-chat">

        <!-- CABECERA -->
        <div class="cabecera-chat">

            <a href="mensajes.php" class="volver">
                ← Volver
            </a>

            <div class="usuario-chat">

                <img
                    src="uploads/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>"
                    alt="Perfil"
                    class="foto-perfil">

                <div>

                    <h2>
                        <?php echo htmlspecialchars($usuario['nombre']); ?>
                    </h2>

                    <span class="estado">
                        Artista
                    </span>

                </div>

            </div>

        </div>

        <!-- INFORMACIÓN DEL PRODUCTO -->

        <div class="producto-chat">

            <img
                src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>"
                alt="Producto">

            <div class="info-producto">

                <h3>

                    <?php echo htmlspecialchars($producto['nombre']); ?>

                </h3>

                <p class="precio">

                    $<?php echo number_format($producto['precio'],2); ?>

                </p>

                <a
                    href="producto.php?id=<?php echo $producto['id']; ?>"
                    class="btn-publicacion">

                    👁 Ver publicación

                </a>

            </div>

        </div>

        <!-- MENSAJES -->

        <div
            class="mensajes"
            id="mensajes">

            <div class="mensaje-sistema">

                Conversación con
                <strong>

                    <?php echo htmlspecialchars($usuario['nombre']); ?>

                </strong>

            </div>

        </div>

        <!-- ESCRIBIR MENSAJE -->

        <div class="escribir-mensaje">

        <form id="formMensaje">

            <input
            type="hidden"
            id="conversacion"
            value="<?php echo $conversacion['id']; ?>">

            <div class="acciones-chat">

            <button
            type="button"
            id="btnEmoji"
            class="btn-chat"
            title="Emojis">

            😊

            <button
            type="button"
            id="btnImagen"
            class="btn-chat"
            title="Enviar imagen">

            📷

            </button>

            <input
            type="file"
            id="imagenChat"
            accept="image/*"
            style="display:none;">

            <button
            type="button"
            id="btnArchivo"
            class="btn-chat"
            title="Enviar archivo">

            📎

            </button>

            </div>

            <input
            type="text"
            id="mensaje"
            placeholder="Escribe un mensaje..."
            autocomplete="off"
            required>

            <button
            type="submit"
            class="btn-enviar">

            ➤

            </button>

        </form>
        <div id="previewImagen" class="preview-imagen" style="display:none;">

            <img id="imgPreview" src="" alt="Vista previa">

            <div class="acciones-preview">

                <button
                    type="button"
                    id="cancelarImagen"
                    class="btn-cancelar">

                    ❌ Cancelar

                </button>

                <button
                    type="button"
                    id="enviarImagen"
                    class="btn-enviar-preview">

                    ➤ Enviar imagen

                </button>

            </div>

        </div>
        </div>

    </div> 
    
    <!-- VISOR DE IMÁGENES -->
    <div id="visorImagen" class="visor-imagen">

        <span id="cerrarVisor">&times;</span>

        <img id="imagenGrande" src="" alt="Imagen">

    </div>

    <script src="JavaScript/chat.js"></script>
</body>
</html>
