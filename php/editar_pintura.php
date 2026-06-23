<?php

$conexion = new mysqli("localhost", "root", "", "soyarte");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conexion->prepare("SELECT * FROM pinturas WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$pintura = $resultado->fetch_assoc();

if(!$pintura){
    die("Pintura no encontrada");
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar pintura</title>
</head>
<body>

<form action="actualizar_pintura.php" method="POST">

    <input type="hidden"
           name="id"
           value="<?php echo $pintura['ID']; ?>">

    <label>Nombre</label>
    <input type="text"
           name="nombre_pintura"
           value="<?php echo htmlspecialchars($pintura['nombre_pintura']); ?>"
           required>

    <br><br>

    <label>Autor</label>
    <input type="text"
           name="autor"
           value="<?php echo htmlspecialchars($pintura['autor']); ?>"
           required>

    <br><br>

    <label>Descripción</label>
    <textarea name="descripcion"
              required><?php echo htmlspecialchars($pintura['descripcion']); ?></textarea>

    <br><br>

    <button type="submit">
        Guardar cambios
    </button>

</form>

</body>
</html>