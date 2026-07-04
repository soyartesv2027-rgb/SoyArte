<?php

session_start();
require_once 'php/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Producto inválido");
}

$sql = "SELECT * FROM productos WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Producto no encontrado");
}

$producto = $resultado->fetch_assoc();

$usuarioActual = (int)($_SESSION['usuario_id'] ?? 0);
$mensajesPendientes = 0;

if ($usuarioActual > 0) {
    $sqlMensajes = "SELECT COUNT(*) AS total
                    FROM mensajes m
                    INNER JOIN conversaciones c
                    ON m.conversacion_id = c.id
                    WHERE m.emisor_id <> ?
                    AND m.leido = 0
                    AND
                    (
                        (
                            c.usuario1_id = ?
                            AND c.oculto_usuario1 = 0
                        )
                        OR
                        (
                            c.usuario2_id = ?
                            AND c.oculto_usuario2 = 0
                        )
                    )";

    $stmtMensajes = $conn->prepare($sqlMensajes);

    if ($stmtMensajes) {
        $stmtMensajes->bind_param("iii", $usuarioActual, $usuarioActual, $usuarioActual);
        $stmtMensajes->execute();
        $resultadoMensajes = $stmtMensajes->get_result()->fetch_assoc();
        $mensajesPendientes = (int)$resultadoMensajes['total'];
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo htmlspecialchars($producto['nombre']); ?>
    </title>
    <link rel="stylesheet" href="styles/producto.css">
</head>
<body>
    <!-- Barra superior -->
    <div class="barra-superior">
        <a href="tienda.php" class="btn-volver">
            ← Volver a la tienda
        </a>
        <div class="acciones">
            <a href="perfil.php">
                👤 Mi Perfil
            </a>
            <a href="mensajes.php">
                <span id="textoMensajesProducto">
                    &#128172; Mensajes<?php echo $mensajesPendientes > 0 ? " (" . $mensajesPendientes . ")" : ""; ?>
                </span>
            </a>
            <a href="php/logout.php">
                🚪 Salir
            </a>
        </div>
    </div>
    <!-- Producto -->
    <div class="detalle-producto">
        <div class="imagen-producto">
            <img
                src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>"
                alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
            >
        </div>
        <div class="info-producto">
            <?php if (!empty($producto['categoria'])): ?>
                <span class="categoria">
                    <?php echo htmlspecialchars($producto['categoria']); ?>
                </span>
            <?php endif; ?>
            <h1>
                <?php echo htmlspecialchars($producto['nombre']); ?>
            </h1>
            <p>
                <?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?>
            </p>
            <h2>
                $<?php echo number_format($producto['precio'], 2); ?>
            </h2>
            <button class="btn-comprar">
                Comprar
            </button>
            <br><br>
            <?php if ($usuarioActual != $producto['usuario_id']): ?>
            <a href="php/crear_chat.php?producto=<?php echo $producto['id']; ?>"
            class="btn-contactar">
                💬 Contactar al artista
            </a>
            <?php endif; ?>
            <!-- Botones solo para el propietario -->
            <?php if ($usuarioActual == $producto['usuario_id']): ?>
                <div class="acciones-propietario">
                    <a
                        href="editar_producto.php?id=<?php echo $producto['id']; ?>"
                        class="btn-editar"
                    >
                        ✏️ Editar
                    </a>
                    <a
                        href="eliminar_producto.php?id=<?php echo $producto['id']; ?>"
                        class="btn-eliminar"
                        onclick="return confirm('¿Seguro que deseas eliminar esta obra?');"
                    >
                        🗑️ Eliminar
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($usuarioActual > 0): ?>
    <script>
    const textoMensajesProducto = document.getElementById("textoMensajesProducto");

    function actualizarContadorProducto() {
        if (!textoMensajesProducto) {
            return;
        }

        fetch("php/contador_mensajes.php")
            .then(res => res.json())
            .then(data => {
                const total = Number(data.total || 0);
                textoMensajesProducto.textContent = total > 0
                    ? "\u{1F4AC} Mensajes (" + total + ")"
                    : "\u{1F4AC} Mensajes";
            })
            .catch(error => console.error(error));
    }

    setInterval(actualizarContadorProducto, 15000);
    </script>
    <?php endif; ?>
</body>

</html>
