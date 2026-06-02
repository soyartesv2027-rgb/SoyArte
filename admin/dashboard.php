<?php
session_start();
require_once '../php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SESSION['rol'] != 'admin') {
    die("Acceso denegado");
}

$sql = "SELECT * FROM realidad_virtual ORDER BY fecha_creacion DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel VR - SoyArte</title>
    <link rel="stylesheet" href="../styles/admin_dashboard.css">
</head>

<body>

<div class="contenedor">

    <h1>🥽 Panel de Administración VR</h1>

    <div class="acciones-admin">

    <a href="crear_vr.php" class="btn-admin">
        ➕ Nueva Experiencia
    </a>

    <a href="../realidad_virtual.php" class="btn-admin">
        🥽 Volver a VR
    </a>

    <a href="../index.php" class="btn-admin">
        🏠 Inicio
    </a>

</div>

    <div class="tabla-contenedor">

        <table class="tabla-vr">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Portada</th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>

            </thead>

            <tbody>

            <?php if($resultado->num_rows > 0): ?>

                <?php while($vr = $resultado->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $vr['id']; ?>
                    </td>

                    <td>

                        <img
                            src="../uploads/vr/portadas/<?php echo htmlspecialchars($vr['portada']); ?>"
                            alt="Portada"
                            class="miniatura-vr"
                        >

                    </td>

                    <td>
                        <?php echo htmlspecialchars($vr['titulo']); ?>
                    </td>

                    <td>
                        <?php echo $vr['fecha_creacion']; ?>
                    </td>

                    <td>

                        <a
                            href="../ver_vr.php?id=<?php echo $vr['id']; ?>"
                            class="btn-ver"
                        >
                            Ver
                        </a>

                        <a
                            href="editar_vr.php?id=<?php echo $vr['id']; ?>"
                            class="btn-editar"
                        >
                            Editar
                        </a>

                        <a
                            href="eliminar_vr.php?id=<?php echo $vr['id']; ?>"
                            class="btn-eliminar"
                            onclick="return confirm('¿Eliminar esta experiencia?');"
                        >
                            Eliminar
                        </a>

                    </td>

                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">
                        No hay experiencias registradas.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>