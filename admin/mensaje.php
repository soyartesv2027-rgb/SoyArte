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

$sql = "SELECT * FROM mensajes_contacto ORDER BY fecha DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mensajes de Contacto</title>

    <link rel="stylesheet" href="../styles/mensaje.css">

</head>

<body>

<div class="contenedor">

    <h1>📩 Mensajes de Contacto</h1>

    <div class="acciones-admin">

        <a href="../contacto.php" class="btn-admin">
            ← Volver
        </a>

    </div>

    <div class="tabla-contenedor">

        <table class="tabla-mensajes">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Asunto</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

            <?php if($resultado->num_rows > 0): ?>

                <?php while($fila = $resultado->fetch_assoc()): ?>

                <tr>

                    <td><?= $fila['id']; ?></td>

                    <td><?= htmlspecialchars($fila['nombre']); ?></td>

                    <td><?= htmlspecialchars($fila['correo']); ?></td>

                    <td><?= htmlspecialchars($fila['asunto']); ?></td>

                    <td><?= date("d/m/Y H:i", strtotime($fila['fecha'])); ?></td>

                    <td>

                        <?php if($fila['estado']=="No leído"): ?>

                            <span class="estado estado-no">
                                No leído
                            </span>

                        <?php else: ?>

                            <span class="estado estado-si">
                                Leído
                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <a
                            href="ver_mensaje.php?id=<?= $fila['id']; ?>"
                            class="btn-ver">

                            Ver

                        </a>

                        <a
                            href="eliminar_mensaje.php?id=<?= $fila['id']; ?>"
                            class="btn-eliminar">

                            Eliminar

                        </a>

                    </td>

                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td colspan="7">

                        No existen mensajes.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>