<?php
session_start();
require_once "php/conexion.php";

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Manualidad no encontrada");
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM manualidades WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows==0){
    die("Manualidad no encontrada");
}

$manualidad = $resultado->fetch_assoc();

if($_SESSION['usuario_id'] != $manualidad['usuario_id']){
    die("No tienes permiso para editar esta publicación.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Editar Manualidad</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">

<link rel="stylesheet"
href="styles/agregar_manualidad.css?v=<?php echo time();?>">

</head>

<body>

<div class="contenedor-form">

<div class="form-card">

<div class="form-header">

<i class="fa-solid fa-pen-to-square"></i>

<h2>Editar Manualidad</h2>

</div>

<form
action="php/actualizar_manualidad.php"
method="POST"
enctype="multipart/form-data"
>

<input
type="hidden"
name="id"
value="<?php echo $manualidad['id']; ?>"
>

<div class="campo">

<label>Nombre</label>

<input
type="text"
name="nombre"
required
value="<?php echo htmlspecialchars($manualidad['nombre']); ?>"
>

</div>

<div class="campo">

<label>Autor</label>

<input
type="text"
name="autor"
required
value="<?php echo htmlspecialchars($manualidad['autor']); ?>"
>

</div>

<div class="campo">

<label>Descripción</label>

<textarea
name="descripcion"
rows="5"
required
><?php echo htmlspecialchars($manualidad['descripcion']); ?></textarea>

</div>

<div class="campo">

<label>Imagen actual</label>

<img
src="<?php echo htmlspecialchars($manualidad['imagen']); ?>"
style="width:100%;height:220px;object-fit:cover;border-radius:12px;margin-top:10px;"
>

</div>

<div class="campo">

<label>Cambiar imagen (Opcional)</label>

<input
type="file"
name="imagen"
accept="image/*"
>

</div>

<div style="display:flex;gap:15px;margin-top:20px;">

<a
href="ver_manualidad.php?id=<?php echo $manualidad['id'];?>"
class="btn-volver"
style="
flex:1;
text-align:center;
background:#888;
color:white;
padding:13px;
border-radius:10px;
text-decoration:none;
"
>

Cancelar

</a>

<button
type="submit"
class="btn-guardar"
style="flex:1;"
>

<i class="fa-solid fa-floppy-disk"></i>

Actualizar

</button>

</div>

</form>

</div>

</div>

</body>

</html>