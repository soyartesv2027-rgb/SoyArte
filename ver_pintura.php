<?php
$conexion = new mysqli("localhost", "root", "", "soyarte");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conexion->prepare("SELECT * FROM pinturas WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$pintura = $resultado->fetch_assoc();

if (!$pintura) {
    die("Pintura no encontrada");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pintura['nombre_pintura']; ?></title>
    <link rel="stylesheet" href="styles/ver_pintura.css?v=<?php echo time(); ?>">
</head>
<body>

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


<div class="acciones">

    <a href="editar_pinturas.php?id=<?php echo $pintura['ID']; ?>" class="btn-editar">
    Editar
</a>
    
<a href="php/eliminar_pintura.php?id=<?php echo $pintura['ID']; ?>"
   class="btn-eliminar"
   onclick="return confirm('¿Estás seguro de eliminar esta pintura?')">
    Eliminar
</a>

</div>

    <a href="pinturas.php" class="btn-volver">
            Regresar
     </a>

    </div>

</div>

</body>
</html>