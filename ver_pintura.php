<?php
$conexion = new mysqli("localhost", "root", "", "soyarte");

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM pinturas WHERE ID = $id";
$resultado = $conexion->query($sql);

$pintura = $resultado->fetch_assoc();

if(!$pintura){
    die("Pintura no encontrada");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?php echo $pintura['nombre_pintura']; ?></title>

<link rel="stylesheet" href="styles/pinturas.css">
</head>
<body>

<div class="contenedor-pintura">

    <img
        src="<?php echo $pintura['imagen']; ?>"
        class="imagen-grande"
    >

    <h1>
        <?php echo $pintura['nombre_pintura']; ?>
    </h1>

    <h3>
        <?php echo $pintura['autor']; ?>
    </h3>

    <p>
        <?php echo $pintura['descripcion']; ?>
    </p>

    <a href="pinturas.php">
        Volver
    </a>

</div>

</body>
</html>