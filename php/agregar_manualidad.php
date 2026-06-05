<?php
session_start();
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre      = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $autor       = $_SESSION['nombre'];
    $usuario_id  = $_SESSION['id'];
    $fecha       = date("Y-m-d H:i:s");

    $imagen = $_FILES['imagen']['name'];
    $ruta   = "uploads/" . $imagen;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);

    $sql = "INSERT INTO Manualidades 
            (imagen, nombre, autor, descripcion, usuario_id, fecha_creacion)
            VALUES ('$ruta', '$nombre', '$autor', '$descripcion', '$usuario_id', '$fecha')";

    if (mysqli_query($conn, $sql)) {
        header("Location: manualidad.php");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>