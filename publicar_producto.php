<?php

session_start();
require_once 'php/conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario_id = $_SESSION['usuario_id'] ?? 0;

    if($usuario_id == 0){
        die("Debes iniciar sesión para publicar una obra.");
    }

    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];

    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);

        $rutaDestino = "uploads/" . $nombreImagen;

        if(move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)){

            $sql = "INSERT INTO productos
            (
                usuario_id,
                nombre,
                descripcion,
                precio,
                imagen,
                categoria
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?
            )";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "issdss",
                $usuario_id,
                $nombre,
                $descripcion,
                $precio,
                $nombreImagen,
                $categoria
            );

            if($stmt->execute()){

                $mensaje = "✅ Obra publicada correctamente";

            }else{

                $mensaje = "❌ Error al guardar en la base de datos";

            }

        }else{

            $mensaje = "❌ Error al subir la imagen";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Publicar Obra</title>

    <link rel="stylesheet" href="styles/publicar.css">

</head>

<body>

<div class="contenedor">

    <h1>🎨 Publicar Obra</h1>

    <?php if(!empty($mensaje)): ?>

        <div class="mensaje">
            <?php echo $mensaje; ?>
        </div>

    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="text"
            name="nombre"
            placeholder="Nombre de la obra"
            required
        >

        <textarea
            name="descripcion"
            placeholder="Descripción de la obra"
            required
        ></textarea>

        <input
            type="number"
            step="0.01"
            name="precio"
            placeholder="Precio"
            required
        >

        <select name="categoria" required>

            <option value="">
                Seleccione una categoría
            </option>

            <option value="Pintura">
                Pintura
            </option>

            <option value="Crochet">
                Crochet
            </option>

            <option value="Escultura">
                Escultura
            </option>

            <option value="Dibujo">
                Dibujo
            </option>

            <option value="Fotografía">
                Fotografía
            </option>

        </select>

        <input
            type="file"
            name="imagen"
            accept="image/*"
            required
        >

        <button type="submit">
            Publicar Obra
        </button>

    </form>

</div>

</body>
</html>