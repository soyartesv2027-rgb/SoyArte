<?php

session_start();
require_once 'php/conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$idProducto = $_GET['id'] ?? 0;
$usuarioActual = $_SESSION['usuario_id'];

$sql = "SELECT * FROM productos
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idProducto);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    die("Producto no encontrado");
}

$producto = $resultado->fetch_assoc();

if ($producto['usuario_id'] != $usuarioActual) {
    die("No tienes permiso para editar esta obra");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];

    $sql = "UPDATE productos
            SET nombre=?,
                descripcion=?,
                precio=?,
                categoria=?
            WHERE id=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssdsi",
        $nombre,
        $descripcion,
        $precio,
        $categoria,
        $idProducto
    );

    $stmt->execute();

    header("Location: producto.php?id=" . $idProducto);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Obra</title>
<link rel="stylesheet" href="styles/editar_productos.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="barra-superior">
    <a href="perfil.php" class="btn-volver">
        ← Volver
    </a>
</div>

<div class="contenedor">

    <h1>✏️ Editar Obra</h1>

    <form method="POST">

        <input
            type="text"
            name="nombre"
            value="<?php echo htmlspecialchars($producto['nombre']); ?>"
            required
        >

        <textarea
            name="descripcion"
            required
        ><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

        <input
            type="number"
            name="precio"
            step="0.01"
            value="<?php echo $producto['precio']; ?>"
            required
        >

        <input
            type="text"
            name="categoria"
            value="<?php echo htmlspecialchars($producto['categoria']); ?>"
            required
        >

        <button type="submit">
            Guardar Cambios
        </button>

    </form>

</div>

</body>
</html>