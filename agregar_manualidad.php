<?php
session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Nueva Manualidad</title>
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link rel="stylesheet"
href="styles/agregar_manualidad.css?v=<?php echo time();?>">

</head>

<body>

<div class="contenedor-form">

<div class="form-card">

<div class="form-header">

<i class="fa-solid fa-scissors"></i>

<h2>Nueva Manualidad</h2>

</div>

<form
action="php/agregar_manualidad.php"
method="POST"
enctype="multipart/form-data"
>

<div class="campo">

<label>Nombre de la manualidad</label>

<input
type="text"
name="nombre"
placeholder="Ej. Flor de papel"
required>

</div>

<div class="campo">

<label>Autor</label>

<input
type="text"
name="autor"
value="<?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?>"
required>

</div>

<div class="campo">

<label>Descripción</label>

<textarea
name="descripcion"
rows="5"
placeholder="Describe tu manualidad..."
required></textarea>

</div>

<div class="campo">

<label>Imagen</label>

<input
type="file"
name="imagen"
accept="image/*"
required>

</div>

<div style="display:flex;gap:15px;margin-top:20px;">

<a
href="manualidad.php"
class="btn-volver"
style="
flex:1;
text-align:center;
background:#888;
color:white;
padding:12px;
border-radius:10px;
text-decoration:none;
">

Cancelar

</a>

<button
type="submit"
class="btn-guardar"
style="flex:1;">

<i class="fa-solid fa-plus"></i>

Publicar

</button>

</div>

</form>

</div>

</div>

</body>
</html>