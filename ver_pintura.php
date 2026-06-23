<?php
$conexion = new mysqli("localhost", "root", "", "soyarte");

$id = $_GET['id'] ?? 0;
$id = intval($id);

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
    <title><?php echo $pintura['nombre_pintura']; ?></title>
    <link rel="stylesheet" href="styles/ver_pintura.css">
</head>
<body>
    <div class="detalle-pintura">

    <img
        src="<?php echo $pintura['imagen']; ?>"
        class="imagen-detalle"
        alt="Pintura">

    <h1 class="titulo-detalle">
        <?php echo htmlspecialchars($pintura['nombre_pintura']); ?>
    </h1>

    <h3>Descripción:</h3>

    <p class="descripcion-detalle">
        <?php echo htmlspecialchars($pintura['descripcion']); ?>
    </p>

    <div class="perfil-artista">

        <i class="fa-regular fa-user"></i>

        <span>
            <?php echo htmlspecialchars($pintura['autor']); ?>
        </span>

    </div>

    <a href="pinturas.php" class="btn-volver">
        ← Volver
    </a>

</div>
</body>
</html>