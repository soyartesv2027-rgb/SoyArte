<?php
include("php/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre_pintura']);
    $autor = trim($_POST['autor']);
    $descripcion = trim($_POST['descripcion']);

    // Obtener la imagen actual
    $consulta = $conn->prepare("SELECT imagen FROM pinturas WHERE ID = ?");
    $consulta->bind_param("i", $id);
    $consulta->execute();
    $resultado = $consulta->get_result();
    $pintura = $resultado->fetch_assoc();

    $rutaImagen = $pintura['imagen'];

    // Si el usuario seleccionó una nueva imagen
    if (isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] == 0 &&
        !empty($_FILES['imagen']['name'])) {

        $carpeta = "uploads/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);

        $rutaImagen = $carpeta . $nombreImagen;

        move_uploaded_file(
            $_FILES["imagen"]["tmp_name"],
            $rutaImagen
        );
    }

    $sql = "UPDATE pinturas
            SET nombre_pintura=?,
                autor=?,
                descripcion=?,
                imagen=?
            WHERE ID=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssi",
        $nombre,
        $autor,
        $descripcion,
        $rutaImagen,
        $id
    );

    if ($stmt->execute()) {

        header("Location: ver_pintura.php?id=" . $id);
        exit();

    } else {

        echo "Error al actualizar la pintura.";

    }

}
?>