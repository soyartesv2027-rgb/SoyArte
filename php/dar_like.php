<?php
session_start();
include("conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        "estado" => "login"
    ]);
    exit();
}

if (!isset($_POST["id_pintura"])) {
    echo json_encode([
        "estado" => "error"
    ]);
    exit();
}

$idUsuario = $_SESSION['usuario_id'];
$idPintura = intval($_POST['id_pintura']);


// Verificar si ya dio like
$sql = "SELECT id
        FROM likes_pinturas
        WHERE id_usuario = ?
        AND id_pintura = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $idUsuario, $idPintura);
$stmt->execute();
$resultado = $stmt->get_result();

if($resultado->num_rows > 0){

    // Quitar like
    $sql = "DELETE FROM likes_pinturas
            WHERE id_usuario = ?
            AND id_pintura = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idUsuario, $idPintura);
    $stmt->execute();

    $like = false;

}else{

    // Agregar like
    $sql = "INSERT INTO likes_pinturas(id_usuario,id_pintura)
            VALUES(?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$idUsuario,$idPintura);
    $stmt->execute();

    $like = true;
}


// Contar likes actuales
$sql = "SELECT COUNT(*) AS total
        FROM likes_pinturas
        WHERE id_pintura=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$idPintura);
$stmt->execute();

$total = $stmt->get_result()->fetch_assoc();

echo json_encode([
    "estado"=>"ok",
    "like"=>$like,
    "likes"=>$total["total"]
]);