<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['usuario_id'])){
    echo json_encode([
        "estado"=>"login"
    ]);
    exit();
}

$idUsuario=$_SESSION['usuario_id'];
$idPintura=intval($_POST['id_pintura']);

$sql="SELECT * FROM likes_pinturas
WHERE id_usuario=? AND id_pintura=?";

$stmt=$conn->prepare($sql);
$stmt->bind_param("ii",$idUsuario,$idPintura);
$stmt->execute();

$resultado=$stmt->get_result();

if($resultado->num_rows>0){

    $sql="DELETE FROM likes_pinturas
    WHERE id_usuario=? AND id_pintura=?";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ii",$idUsuario,$idPintura);
    $stmt->execute();

    $conn->query("UPDATE pinturas
    SET likes=likes-1
    WHERE ID=$idPintura");

    $nuevoEstado=false;

}else{

    $sql="INSERT INTO likes_pinturas(id_usuario,id_pintura)
    VALUES(?,?)";

    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ii",$idUsuario,$idPintura);
    $stmt->execute();

    $conn->query("UPDATE pinturas
    SET likes=likes+1
    WHERE ID=$idPintura");

    $nuevoEstado=true;

}

$sql="SELECT likes FROM pinturas
WHERE ID=$idPintura";

$total=$conn->query($sql)->fetch_assoc();

echo json_encode([
    "estado"=>"ok",
    "likes"=>$total['likes'],
    "like"=>$nuevoEstado
]);