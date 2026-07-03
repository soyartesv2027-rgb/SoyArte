<?php
session_start();
require_once "php/conexion.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$usuarioActual = (int)$_SESSION['usuario_id'];

if (!isset($_GET['id'])) {
    die("Conversacion no encontrada.");
}

$conversacionID = (int)$_GET['id'];

$sql = "SELECT *
        FROM conversaciones
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $conversacionID);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("La conversacion no existe.");
}

$conversacion = $resultado->fetch_assoc();

if (
    $usuarioActual !== (int)$conversacion['usuario1_id'] &&
    $usuarioActual !== (int)$conversacion['usuario2_id']
) {
    die("No tienes permiso para entrar a este chat.");
}

$otroUsuario = ($usuarioActual === (int)$conversacion['usuario1_id'])
    ? (int)$conversacion['usuario2_id']
    : (int)$conversacion['usuario1_id'];

$sqlUsuario = "SELECT id, nombre, foto_perfil
               FROM usuarios
               WHERE id = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $otroUsuario);
$stmtUsuario->execute();
$usuario = $stmtUsuario->get_result()->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado.");
}

$sqlProducto = "SELECT id, nombre, precio, imagen
                FROM productos
                WHERE id = ?";
$stmtProducto = $conn->prepare($sqlProducto);
$stmtProducto->bind_param("i", $conversacion['producto_id']);
$stmtProducto->execute();
$producto = $stmtProducto->get_result()->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado.");
}

$fotoPerfil = !empty($usuario['foto_perfil'])
    ? "uploads/perfiles/" . $usuario['foto_perfil']
    : "images/Logo-Nuevo.png";

$imagenProducto = "uploads/" . $producto['imagen'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | SoyArte</title>
    <link rel="stylesheet" href="styles/chat.css">
</head>
<body>
    <div class="contenedor-chat">
        <div class="cabecera-chat">
            <a href="mensajes.php" class="volver">&#8592; Volver</a>

            <div class="usuario-chat">
                <img
                    src="<?php echo htmlspecialchars($fotoPerfil); ?>"
                    alt="Perfil"
                    class="foto-perfil">

                <div>
                    <h2><?php echo htmlspecialchars($usuario['nombre']); ?></h2>
                    <span class="estado">Artista</span>
                </div>
            </div>
        </div>

        <div class="producto-chat">
            <img
                src="<?php echo htmlspecialchars($imagenProducto); ?>"
                alt="Producto">

            <div class="info-producto">
                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                <p class="precio">$<?php echo number_format((float)$producto['precio'], 2); ?></p>

                <a
                    href="producto.php?id=<?php echo (int)$producto['id']; ?>"
                    class="btn-publicacion">
                    Ver publicacion
                </a>
            </div>
        </div>

        <div class="mensajes" id="mensajes">
            <div class="mensaje-sistema">
                Conversacion con
                <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
            </div>
        </div>

        <div class="escribir-mensaje">
            <form id="formMensaje">
                <input
                    type="hidden"
                    id="conversacion"
                    value="<?php echo (int)$conversacion['id']; ?>">

                <div class="acciones-chat">
                    <button
                        type="button"
                        id="btnEmoji"
                        class="btn-chat"
                        title="Emojis">
                        &#128522;
                    </button>

                    <button
                        type="button"
                        id="btnImagen"
                        class="btn-chat"
                        title="Enviar imagen">
                        &#128247;
                    </button>

                    <input
                        type="file"
                        id="imagenChat"
                        accept="image/*"
                        hidden>
                </div>

                <input
                    type="text"
                    id="mensaje"
                    placeholder="Escribe un mensaje..."
                    autocomplete="off">

                <button
                    type="submit"
                    class="btn-enviar"
                    title="Enviar">
                    &#10148;
                </button>
            </form>

            <div id="previewImagen" class="preview-imagen" hidden>
                <img id="imgPreview" src="" alt="Vista previa">

                <div class="acciones-preview">
                    <button
                        type="button"
                        id="cancelarImagen"
                        class="btn-cancelar">
                        Cancelar
                    </button>

                    <button
                        type="button"
                        id="enviarImagen"
                        class="btn-enviar-preview">
                        Enviar imagen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="visorImagen" class="visor-imagen">
        <span id="cerrarVisor">&times;</span>
        <img id="imagenGrande" src="" alt="Imagen">
    </div>

    <script src="JavaScript/chat.js"></script>
</body>
</html>
