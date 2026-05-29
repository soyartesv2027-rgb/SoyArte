<?php
session_start();
include("php/conexion.php");


if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $usuario_actual = $_SESSION['usuario_id'];

    // Seguridad estricta: confirma propiedad antes de borrar
    $sqlVerificar = "SELECT usuario_id FROM obras WHERE id = ?";
    $stmtVerificar = $conn->prepare($sqlVerificar);
    $stmtVerificar->bind_param("i", $id);
    $stmtVerificar->execute();
    $res = $stmtVerificar->get_result()->fetch_assoc();

    if ($res && $res['usuario_id'] == $usuario_actual) {
        $sqlDelete = "DELETE FROM obras WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();
    }
}

header("Location: poesia.php");
exit();
?>