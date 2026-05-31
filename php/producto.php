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

$usuarioActual = $_SESSION['usuario_id'] ?? 0;


?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($producto['nombre']); ?>
    </title>

    <link rel="stylesheet" href="../styles/producto.css">

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

            <a href="logout.php">
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

</body>

</html>