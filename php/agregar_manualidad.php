<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 🔐 USUARIO LOGUEADO
    if (!isset($_SESSION['id'])) {
        die("Debes iniciar sesión para publicar una manualidad.");
    }

    $usuario_id = $_SESSION['id'];

    // 📌 DATOS
    $nombre = trim($_POST['nombre']);
    $autor = trim($_POST['autor']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($nombre) || empty($autor) || empty($descripcion)) {
        die("Todos los campos son obligatorios.");
    }

    // 📸 IMAGEN
    if (!isset($_FILES["imagen"]) || $_FILES["imagen"]["error"] != 0) {
        die("Debe seleccionar una imagen.");
    }

    $carpeta = "../uploads/manualidades/";

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $nombreImagen = time() . "." . $extension;

    $rutaImagenBD = "uploads/manualidades/" . $nombreImagen;
    $rutaFisica = "../" . $rutaImagenBD;

    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaFisica)) {
        die("Error al subir la imagen.");
    }

    // 💾 INSERTAR
    $sql = "INSERT INTO manualidades
            (nombre, autor, descripcion, imagen, usuario_id)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error SQL: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssi",
        $nombre,
        $autor,
        $descripcion,
        $rutaImagenBD,
        $usuario_id
    );

    if ($stmt->execute()) {
        header("Location: ../manualidad.php");
        exit();
    } else {
        die("Error al guardar: " . $stmt->error);
    }
}
?>