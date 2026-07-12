<?php
session_start();
header('Content-Type: application/json');

include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "estado" => "login"
    ]);
    exit();
}

$idUsuario = $_SESSION['usuario_id'];
$idPintura = intval($_POST['id_pintura']);

$buscar = $conn->prepare("SELECT id FROM likes_pinturas WHERE id_usuario=? AND id_pintura=?");
$buscar->bind_param("ii", $idUsuario, $idPintura);
$buscar->execute();
$resultado = $buscar->get_result();

if ($resultado->num_rows > 0) {

    $eliminar = $conn->prepare("DELETE FROM likes_pinturas WHERE id_usuario=? AND id_pintura=?");
    $eliminar->bind_param("ii", $idUsuario, $idPintura);
    $eliminar->execute();

    $like = false;

} else {

    $insertar = $conn->prepare("INSERT INTO likes_pinturas(id_usuario,id_pintura) VALUES(?,?)");
    $insertar->bind_param("ii", $idUsuario, $idPintura);
    $insertar->execute();

    $like = true;
}

$total = $conn->prepare("SELECT COUNT(*) AS total FROM likes_pinturas WHERE id_pintura=?");
$total->bind_param("i", $idPintura);
$total->execute();

$likes = $total->get_result()->fetch_assoc();

echo json_encode([
    "estado" => "ok",
    "likes" => $likes['total'],
    "like" => $like
]);