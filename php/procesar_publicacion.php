<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $poema = $_POST['poema'];
    $usuario_id = $_SESSION['usuario_id'];
    $contenidoImagen = null;

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $contenidoImagen = file_get_contents($_FILES['imagen']['tmp_name']);
    }

    $sql = "INSERT INTO obras (titulo, poema, imagen, usuario_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    $null = null; 
    $stmt->bind_param("ssbi", $titulo, $poema, $null, $usuario_id);
    
    if ($contenidoImagen !== null) {
        $stmt->send_long_data(2, $contenidoImagen);
    }

    if ($stmt->execute()) {
        header("Location: poesia.php");
        exit();
    } else {
        echo "Error al guardar en la base de datos: " . $stmt->error;
    }
}
?>