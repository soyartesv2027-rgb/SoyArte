<?php
session_start();
require_once "php/conexion.php";

if(!isset($_GET['id'])){
    die("Manualidad no encontrada.");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM manualidades WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows==0){
    die("Manualidad no encontrada.");
}

$manualidad = $resultado->fetch_assoc();

$esAdmin = ($_SESSION['rol'] ?? '') === 'admin';
if (($manualidad['estado'] ?? 'publicada') !== 'publicada' && !$esAdmin) {
    include("components/flash.php");
    die("Esta publicación no está disponible.");
}

include("components/flash.php");

/* ==========================
   COMENTARIOS
========================== */

$sqlComentarios = "SELECT c.*, u.nombre
                   FROM comentarios_manualidades c
                   INNER JOIN usuarios u
                   ON c.usuario_id=u.id
                   WHERE c.manualidad_id=?
                   ORDER BY c.id DESC";

$stmtComentarios = $conn->prepare($sqlComentarios);
$stmtComentarios->bind_param("i",$id);
$stmtComentarios->execute();

$comentarios = $stmtComentarios->get_result();

/* ==========================
   USUARIO ACTUAL
========================== */

$usuarioActual = $_SESSION['usuario_id'] ?? 0;

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

<?php echo htmlspecialchars($manualidad['nombre']); ?>

</title>
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
<link rel="stylesheet"
href="styles/ver_manualidad.css?v=<?php echo time();?>">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

<div class="contenedor">

<a
href="manualidad.php"
class="volver">

<i class="fa-solid fa-arrow-left"></i>

Volver

</a>

<div class="card">

<img
src="<?php echo htmlspecialchars($manualidad['imagen']); ?>"
class="imagen"
alt="Imagen de la manualidad">

<h1>

<?php echo htmlspecialchars($manualidad['nombre']); ?>

</h1>

<p class="autor">

<i class="fa-regular fa-user"></i>

<?php echo htmlspecialchars($manualidad['autor']); ?>

</p>

<p class="fecha">

<i class="fa-regular fa-calendar"></i>

<?php

if(isset($manualidad['fecha'])){

    echo date("d/m/Y",strtotime($manualidad['fecha']));

}else{

    echo date("d/m/Y");

}

?>

</p>

<p class="descripcion">

<?php echo nl2br(htmlspecialchars($manualidad['descripcion'])); ?>

</p>

<!-- ==========================
     FORMULARIO COMENTARIOS
========================== -->

<?php if(isset($_SESSION['usuario_id'])): ?>

<form
action="php/guardar_comentario_manualidad.php"
method="POST"
class="form-comentario">

<input
type="hidden"
name="manualidad_id"
value="<?php echo $manualidad['id']; ?>">

<textarea
name="comentario"
placeholder="Escribe un comentario..."
required></textarea>

<button type="submit">

<i class="fa-regular fa-comment"></i>

Comentar

</button>

</form>

<?php else: ?>

<p class="mensaje-login">

Debes iniciar sesión para comentar.

</p>

<?php endif; ?>

<!-- ==========================
     LISTA DE COMENTARIOS
========================== -->

<div class="comentarios">

<h2>

Comentarios

</h2>

<?php if($comentarios->num_rows>0): ?>

<?php while($comentario=$comentarios->fetch_assoc()): ?>

<div class="comentario">

<div class="comentario-header">

<strong>

<?php echo htmlspecialchars($comentario['nombre']); ?>

</strong>

<small>

<?php echo date("d/m/Y H:i",strtotime($comentario['fecha'])); ?>

</small>

</div>

<p>

<?php echo nl2br(htmlspecialchars($comentario['comentario'])); ?>

</p>

</div>

<?php endwhile; ?>

<?php else: ?>

<p class="sin-comentarios">

Todavía no hay comentarios.

Sé el primero en comentar.

</p>

<?php endif; ?>

</div>

<!-- ==========================
     BOTONES DEL PROPIETARIO
========================== -->

<?php if($usuarioActual == $manualidad['usuario_id']): ?>

<div class="acciones">

<a
href="editar_manualidad.php?id=<?php echo $manualidad['id']; ?>"
class="editar">

<i class="fa-solid fa-pen"></i>

Editar

</a>

<a
href="php/eliminar_manualidad.php?id=<?php echo $manualidad['id']; ?>"
class="eliminar"
onclick="return confirm('¿Seguro que deseas eliminar esta manualidad?');">

<i class="fa-solid fa-trash"></i>

Eliminar

</a>

</div>

<?php endif; ?>

<?php
$mod_tipo = 'manualidad';
$mod_id = (int)$manualidad['id'];
include("components/denunciar.php");
?>

</div>

</div>

</body>
</html>