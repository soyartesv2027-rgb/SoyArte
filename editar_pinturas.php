<?php
include("php/conexion.php");

if (!isset($_GET['id'])) {
    die("No se recibió el ID de la pintura.");
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM pinturas WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Pintura no encontrada.");
}

$pintura = $resultado->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar pintura</title>

<link rel="stylesheet" href="styles/editar_pinturas.css?v=<?php echo time(); ?>">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

<a href="ver_pintura.php?id=<?php echo $pintura['ID']; ?>" class="flecha">

<i class="fa-solid fa-arrow-left"></i>

</a>

<div class="contenedor">

<form action="actualizar_pintura.php"
method="POST"
enctype="multipart/form-data">

<h2>

<i class="fa-solid fa-pen-to-square"></i>

Editar pintura

</h2>

<input
type="hidden"
name="id"
value="<?php echo $pintura['ID']; ?>">

<div class="imagen-actual">

<h3>Imagen actual</h3>

<img
src="<?php echo $pintura['imagen']; ?>"
alt="Pintura">

</div>

<label>

Cambiar imagen (opcional)

</label>

<input
type="file"
name="imagen">

<label>

Nombre de la pintura

</label>

<input
type="text"
name="nombre_pintura"
value="<?php echo htmlspecialchars($pintura['nombre_pintura']); ?>"
required>

<label>

Autor

</label>

<input
type="text"
name="autor"
value="<?php echo htmlspecialchars($pintura['autor']); ?>"
required>

<label>

Descripción

</label>

<textarea
name="descripcion"
rows="6"
required><?php echo htmlspecialchars($pintura['descripcion']); ?></textarea>

<div class="botones">

<button type="submit">

<i class="fa-solid fa-floppy-disk"></i>

Guardar cambios

</button>

<a
href="ver_pintura.php?id=<?php echo $pintura['ID']; ?>"
class="cancelar">

Cancelar

</a>

</div>

</form>

</div>

</body>
</html>