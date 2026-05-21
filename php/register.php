<?php 
include("conexion.php"); // ← mismo nivel

if ($_SERVER["REQUEST_METHOD"] == "POST") { 

    $nombre = $_POST['nombre']; 
    $correo = $_POST['correo']; 
    $password = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, correo, password, rol) VALUES (?, ?, ?, 'usuario')";

    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("sss", $nombre, $correo, $password);

    if ($stmt->execute()) { 
        header("Location: ../login.html");
        exit();
    } else { 
        echo "Error al registrar: " . $conn->error;
    } 
} 
?>