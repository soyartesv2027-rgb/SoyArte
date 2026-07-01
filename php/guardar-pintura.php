<?php
include("php/conexion.php")

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['nombre_pintura']);
    $autor = trim($_POST['autor']);
    $descripcion = trim($_POST['descripcion']);

    $rutaImagen = "";

    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == 0) {

        $carpeta = "uploads/";

        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaImagen = $carpeta . $nombreImagen;

        move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaImagen);
    }

    $sql = "INSERT INTO pinturas
    (nombre_pintura, descripcion, autor, imagen, likes, comentarios)
    VALUES (?, ?, ?, ?, 0, 0)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $nombre,
        $descripcion,
        $autor,
        $rutaImagen
    );

    if ($stmt->execute()) {

        header("Location: pinturas.php");
        exit();

    } else {

        echo "Error al guardar la pintura.";

    }

}
?>