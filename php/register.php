<?php

session_start();

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    $password = password_hash(
        $_POST['contrasena'],
        PASSWORD_DEFAULT
    );

    $sql = "INSERT INTO usuarios
    (nombre, correo, password, rol)
    VALUES (?, ?, ?, 'usuario')";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sss",
        $nombre,
        $correo,
        $password
    );

    if ($stmt->execute()) {

        $_SESSION['usuario_id'] = $stmt->insert_id;

        header("Location: ../formulario.php");
        exit();

    } else {

        echo "Error al registrar";
    }
}
?>