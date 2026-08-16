<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "soyarte");

if ($conexion->connect_error) {
    die("Error de conexión");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conexion->prepare("SELECT * FROM pinturas WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$pintura = $resultado->fetch_assoc();


if (!$pintura) {
    die("Pintura no encontrada");
}

$esAdmin = ($_SESSION['rol'] ?? '') === 'admin';
if (($pintura['estado'] ?? 'publicada') !== 'publicada' && !$esAdmin) {
    include("components/flash.php");
    die("Esta publicación no está disponible.");
}

include("components/flash.php");

$esPropietario = false;

if (
    isset($_SESSION['usuario_id']) &&
    isset($pintura['id_usuario']) &&
    $_SESSION['usuario_id'] == $pintura['id_usuario']
) {
    $esPropietario = true;
}

$sqlComentarios = "
SELECT c.*, u.nombre
FROM comentarios_pinturas c
INNER JOIN usuarios u
ON c.id_usuario = u.id
WHERE c.id_pintura = ?
ORDER BY c.fecha DESC";

$stmtComentarios = $conexion->prepare($sqlComentarios);
$stmtComentarios->bind_param("i", $id);
$stmtComentarios->execute();

$comentarios = $stmtComentarios->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title><?php echo htmlspecialchars($pintura['nombre_pintura']); ?></title>

<link rel="stylesheet" href="styles/ver_pintura.css?v=<?php echo time(); ?>">
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

<a href="pinturas.php">

<div class="flecha">
<i class="fa-solid fa-arrow-left"></i>
</div>

</a>

<div class="detalle-pintura">

<img
src="<?php echo $pintura['imagen']; ?>"
class="imagen-detalle"
alt="Pintura">

<div class="contenido">

<h1 class="titulo-detalle">
<?php echo htmlspecialchars($pintura['nombre_pintura']); ?>
</h1>

<p class="autor">
Por <?php echo htmlspecialchars($pintura['autor']); ?>
</p>

<div class="descripcion-box">

<h3>Descripción</h3>

<p>
<?php echo htmlspecialchars($pintura['descripcion']); ?>
</p>

</div>


<?php if($esPropietario){ ?>

<div class="acciones">

<a href="editar_pinturas.php?id=<?php echo $pintura['ID']; ?>" class="btn-editar">

<i class="fa-solid fa-pen"></i>
Editar

</a>

<a
href="php/eliminar_pintura.php?id=<?php echo $pintura['ID']; ?>"
class="btn-eliminar"
onclick="return confirm('¿Estás seguro de eliminar esta pintura?')">

<i class="fa-solid fa-trash"></i>
Eliminar

</a>

</div>

<?php } ?>

<?php
$mod_tipo = 'pintura';
$mod_id = $id;
include("components/denunciar.php");
?>

<hr>

<h2>Comentarios</h2>

<?php if(isset($_SESSION['usuario_id'])){ ?>

<form action="php/comentar.php" method="POST">

<input
type="hidden"
name="id_pintura"
value="<?php echo $id; ?>">

<textarea
name="comentario"
placeholder="Escribe un comentario..."
required></textarea>

<button type="submit">
Comentar
</button>

</form>

<?php } else { ?>

<p>Debes iniciar sesión para comentar.</p>

<?php } ?>

<br>

<?php if($comentarios->num_rows > 0){ ?>

<?php while($comentario = $comentarios->fetch_assoc()){ ?>

<div class="comentario">

<h4>

<?php echo htmlspecialchars($comentario['nombre']); ?>

</h4>

<p>

<?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?>

</p>

<small>

<?php echo $comentario['fecha']; ?>

</small>

</div>

<?php } ?>

<?php } else { ?>

<p>Aún no hay comentarios.</p>

<?php } ?>

</div>

</div>

</body>
</html>