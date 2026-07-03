<?php
session_start();
require_once 'php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$id = (int)$_SESSION['usuario_id'];
$mensajesPendientes = 0;

$sql = "SELECT *
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
    $stmtMensajes->bind_param("iii", $id, $id, $id);
    $stmtMensajes->execute();
    $resultadoMensajes = $stmtMensajes->get_result()->fetch_assoc();
    $mensajesPendientes = (int)$resultadoMensajes['total'];
}
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
            <div class="perfil-artistico">

                <h3>🎨 Perfil Artístico</h3>

                <div class="datos-artista">

                    <div class="dato-card">
                        <span>👤</span>
                        <h4>Tipo de usuario</h4>
                        <p><?php echo htmlspecialchars($usuario['tipo_usuario'] ?? 'No especificado'); ?></p>
                    </div>

                    <div class="dato-card">

                        <span>🎨</span>

                        <h4>Intereses</h4>

                        <div class="intereses-chips">

                            <?php

                            if(!empty($usuario['intereses'])){

                                $intereses = explode(',', $usuario['intereses']);

                                foreach($intereses as $interes){

                                    echo '<span class="chip">'.trim($interes).'</span>';

                                }

                            }else{

                                echo '<span class="chip">No especificado</span>';

                            }

                            ?>

                        </div>

                    </div>

                    <div class="dato-card">
                        <span>🎥</span>
                        <h4>Formato favorito</h4>
                        <p><?php echo htmlspecialchars($usuario['tipo_tutorial'] ?? 'No especificado'); ?></p>
                    </div>

                    <div class="dato-card">
                        <span>📅</span>
                        <h4>Frecuencia</h4>
                        <p><?php echo htmlspecialchars($usuario['frecuencia'] ?? 'No especificado'); ?></p>
                    </div>

                    <div class="dato-card full">
                        <span>📚</span>
                        <h4>Quiero aprender</h4>
                        <p><?php echo htmlspecialchars($usuario['aprendizaje'] ?? 'No especificado'); ?></p>
                    </div>

                </div>

            </div>

            <div class="acciones-perfil">
                <a href="index.php" class="btn-volver">
                    Volver al inicio
                </a>

                <a href="mensajes.php" class="btn-volver">
                    <span id="textoMensajesPerfil">
                        &#128172; Mensajes<?php echo $mensajesPendientes > 0 ? " (" . $mensajesPendientes . ")" : ""; ?>
                    </span>
                </a>

                <a href="php/logout.php" class="btn-volver btn-salir">
                    Cerrar sesi&oacute;n
                </a>
            </div>

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
                                href="editar_producto.php?id=<?php echo $obra['id']; ?>"
                                class="btn-editar"
                            >
                                Editar
                            </a>

                            <a
                                href="php/eliminar_producto.php?id=<?php echo $obra['id']; ?>"
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

    <script>
    const textoMensajesPerfil = document.getElementById("textoMensajesPerfil");

    function actualizarContadorPerfil() {
        if (!textoMensajesPerfil) {
            return;
        }

        fetch("php/contador_mensajes.php")
            .then(res => res.json())
            .then(data => {
                const total = Number(data.total || 0);
                textoMensajesPerfil.textContent = total > 0
                    ? "\u{1F4AC} Mensajes (" + total + ")"
                    : "\u{1F4AC} Mensajes";
            })
            .catch(error => console.error(error));
    }

    setInterval(actualizarContadorPerfil, 15000);
    </script>
</body>
</html>
