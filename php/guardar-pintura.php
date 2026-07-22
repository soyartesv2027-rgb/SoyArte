<?php
include("php/conexion.php");

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

        $permitidos = ["jpg", "jpeg", "png", "gif", "webp"];
        $extension = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));

        if (!in_array($extension, $permitidos)) {
            die("Solo se permiten imágenes.");
        }

        $nombreImagen = time() . "_" . basename($_FILES["imagen"]["name"]);
        $rutaImagen = $carpeta . $nombreImagen;

        if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaImagen)) {
            die("Error al subir la imagen.");
        }
    }

    $sql = "INSERT INTO pinturas
            (nombre_pintura, descripcion, autor, imagen, likes, comentarios)
            VALUES (?, ?, ?, ?, 0, 0)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }

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
        echo "Error al guardar la pintura: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>