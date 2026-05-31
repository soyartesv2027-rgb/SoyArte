<?php
session_start();
require_once 'php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$id = $_SESSION['usuario_id'];

$sql = "SELECT nombre, correo, rol, biografia, foto_perfil
        FROM usuarios
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$sqlObras = "SELECT * FROM productos
             WHERE usuario_id = ?";

$stmtObras = $conn->prepare($sqlObras);
$stmtObras->bind_param("i", $id);
$stmtObras->execute();

$misObras = $stmtObras->get_result();
$totalObras = $misObras->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - SoyArte</title>

    <link rel="stylesheet" href="styles/perfil.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="container">

    <div class="perfil-card">

        <div class="perfil-header">

            <div class="foto-perfil">

                <?php if (!empty($usuario['foto_perfil'])): ?>

                    <img
                        src="uploads/perfiles/<?php echo htmlspecialchars($usuario['foto_perfil']); ?>"
                        alt="Foto de perfil"
                    >

                <?php else: ?>

                    <div class="avatar-default">
                        🎨
                    </div>

                <?php endif; ?>

            </div>

            <div class="info-usuario">

                <h1>
                    <?php echo htmlspecialchars($usuario['nombre']); ?>
                </h1>

                <p>
                    <?php echo htmlspecialchars($usuario['correo']); ?>
                </p>

                <span class="rol">
                    <?php echo htmlspecialchars($usuario['rol']); ?>
                </span>

            </div>

        </div>

        <form
            action="subir_foto.php"
            method="POST"
            enctype="multipart/form-data"
            class="form-foto"
        >

            <input
                type="file"
                name="foto_perfil"
                accept="image/*"
                required
            >

            <button type="submit">
                Cambiar Foto
            </button>

        </form>

        <div class="estadisticas">

            <div class="stat">
                <h2><?php echo $totalObras; ?></h2>
                <span>Obras</span>
            </div>

            <div class="stat">
                <h2>0</h2>
                <span>Ventas</span>
            </div>

            <div class="stat">
                <h2>0</h2>
                <span>Seguidores</span>
            </div>

        </div>

        <div class="biografia">

            <h3>Sobre mí</h3>

            <textarea readonly><?php echo htmlspecialchars($usuario['biografia']); ?></textarea>

        </div>

        <a href="index.php" class="btn-volver">
            Volver al inicio
        </a>

    </div>

    <div class="mis-obras">

        <h2>🎨 Mis Obras</h2>

        <div class="contenedor-obras">

            <?php while ($obra = $misObras->fetch_assoc()): ?>

                <div class="card-obra">

                    <img
                        src="uploads/<?php echo htmlspecialchars($obra['imagen']); ?>"
                        alt="<?php echo htmlspecialchars($obra['nombre']); ?>"
                    >

                    <div class="info-obra">

                        <h3>
                            <?php echo htmlspecialchars($obra['nombre']); ?>
                        </h3>

                        <p class="precio">
                            $<?php echo number_format($obra['precio'], 2); ?>
                        </p>

                        <div class="acciones-obra">

                            <a
                                href="producto.php?id=<?php echo $obra['id']; ?>"
                                class="btn-ver"
                            >
                                Ver
                            </a>

                            <a
                                href="eliminar_producto.php?id=<?php echo $obra['id']; ?>"
                                class="btn-eliminar"
                                onclick="return confirm('¿Eliminar esta obra?');"
                            >
                                Eliminar
                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</div>

</body>
</html>