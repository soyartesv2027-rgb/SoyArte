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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Mensaje no válido");
}

$id = (int)$_GET['id'];

/* Marcar como leído */
$sqlUpdate = "UPDATE mensajes_contacto
              SET estado='Leído'
              WHERE id=?";

$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("i", $id);
$stmtUpdate->execute();

/* Obtener mensaje */
$sql = "SELECT * FROM mensajes_contacto WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Mensaje no encontrado");
}

$mensaje = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Ver Mensaje</title>

<style>

body{
    margin:0;
    padding:30px;
    background:#f5f5f5;
    font-family:Arial, Helvetica, sans-serif;
}

.contenedor{
    max-width:900px;
    margin:auto;
}

.tarjeta{

    background:white;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 5px 15px rgba(0,0,0,.1);

}

.encabezado{

    background:#4576a8;
    color:white;
    padding:20px;

}

.contenido{

    padding:30px;

}

.info{

    margin-bottom:18px;

}

.label{

    color:#4576a8;
    font-weight:bold;
    display:block;
    margin-bottom:5px;

}

.mensaje{

    background:#f7fbff;
    border-left:5px solid #4576a8;
    padding:20px;
    border-radius:8px;
    line-height:1.7;

}

.botones{

    margin-top:25px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;

}

.btn{

    text-decoration:none;
    padding:10px 18px;
    border-radius:8px;
    color:white;
    font-weight:bold;
    transition:.3s;

}

.btn-volver{

    background:#4576a8;

}

.btn-volver:hover{

    background:#c7e3ff;
    color:#4576a8;

}

.btn-correo{

    background:#28a745;

}

.btn-correo:hover{

    opacity:.85;

}

.btn-eliminar{

    background:#dc3545;

}

.btn-eliminar:hover{

    opacity:.85;

}
@media (max-width:768px){

    body{
        padding:15px;
    }

    .contenido{
        padding:20px;
    }

    .encabezado h1{
        font-size:24px;
    }

    .botones{
        flex-direction:column;
    }

    .btn{
        width:100%;
        text-align:center;
        box-sizing:border-box;
    }

}

@media (max-width:480px){

    body{
        padding:10px;
    }

    .mensaje{
        font-size:14px;
    }

    .label{
        font-size:15px;
    }

}

</style>

</head>

<body>

<div class="contenedor">

    <div class="tarjeta">

        <div class="encabezado">

            <h1>
                📩 Mensaje #<?php echo $mensaje['id']; ?>
            </h1>

        </div>

        <div class="contenido">

            <div class="info">

                <span class="label">Nombre</span>

                <?php echo htmlspecialchars($mensaje['nombre']); ?>

            </div>

            <div class="info">

                <span class="label">Correo</span>

                <?php echo htmlspecialchars($mensaje['correo']); ?>

            </div>

            <div class="info">

                <span class="label">Asunto</span>

                <?php echo htmlspecialchars($mensaje['asunto']); ?>

            </div>

            <div class="info">

                <span class="label">Fecha</span>

                <?php echo date(
                    "d/m/Y H:i",
                    strtotime($mensaje['fecha'])
                ); ?>

            </div>

            <div class="info">

                <span class="label">Mensaje</span>

                <div class="mensaje">

                    <?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?>

                </div>

            </div>

            <div class="botones">

                <a
                    href="mensajes.php"
                    class="btn btn-volver">
                    ← Volver
                </a>

                <a
                    href="mailto:<?php echo htmlspecialchars($mensaje['correo']); ?>"
                    class="btn btn-correo">
                    ✉ Responder
                </a>

                <a
                    href="eliminar_mensaje.php?id=<?php echo $mensaje['id']; ?>"
                    class="btn btn-eliminar"
                    onclick="return confirm('¿Eliminar este mensaje?');">
                    🗑 Eliminar
                </a>

            </div>

        </div>

    </div>

</div>

</body>
</html>