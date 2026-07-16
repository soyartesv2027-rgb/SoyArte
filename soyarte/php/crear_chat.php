<?php

session_start();
require_once "conexion.php";

// Verificar que el usuario haya iniciado sesión //
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}
$comprador = $_SESSION['usuario_id'];

$producto_id = isset($_GET['producto']) ? (int)$_GET['producto'] : 0;
if ($producto_id <= 0) {
    die("Producto inválido.");
}

// Buscar el dueño del producto //
$sql = "SELECT usuario_id
        FROM productos
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();
if ($resultado->num_rows == 0) {
    die("Producto no encontrado.");
}
$producto = $resultado->fetch_assoc();
$artista = $producto['usuario_id'];

// Evitar que el artista se escriba a sí mismo //
if ($comprador == $artista) {
    header("Location: ../producto.php?id=" . $producto_id);
    exit();

}

// Buscar si ya existe una conversación// 
$sql = "SELECT id
        FROM conversaciones
        WHERE producto_id = ?
        AND (
            (usuario1_id = ? AND usuario2_id = ?)
            OR
            (usuario1_id = ? AND usuario2_id = ?)
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iiiii",
    $producto_id,
    $comprador,
    $artista,
    $artista,
    $comprador
);
$stmt->execute();
$resultado = $stmt->get_result();

// SI YA EXISTE //
if ($resultado->num_rows > 0) {
    $conversacion = $resultado->fetch_assoc();
    $idConversacion = $conversacion['id'];

    $sql = "UPDATE conversaciones
            SET
            oculto_usuario1 =
            CASE
                WHEN usuario1_id = ?
                THEN 0
                ELSE oculto_usuario1
            END,
            oculto_usuario2 =
            CASE
                WHEN usuario2_id = ?
                THEN 0
                ELSE oculto_usuario2
            END
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iii",
        $comprador,
        $comprador,
        $idConversacion
    );
    $stmt->execute();
}

else {
    $sql = "INSERT INTO conversaciones
            (
                producto_id,
                usuario1_id,
                usuario2_id
            )
            VALUES
            (
                ?,
                ?,
                ?
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iii",
        $producto_id,
        $comprador,
        $artista
    );
    $stmt->execute();
    $idConversacion = $conn->insert_id;
}

header("Location: ../chat.php?id=" . $idConversacion);
exit();

?>