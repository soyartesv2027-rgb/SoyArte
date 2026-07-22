<?php
session_start();
require_once '../php/conexion.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'admin') {
    die("Acceso denegado");
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare(
    "SELECT portada, qr_imagen
     FROM realidad_virtual
     WHERE id=?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows > 0){

    $vr = $resultado->fetch_assoc();

    $conn->begin_transaction();

    try {
        $deleteComentarios = $conn->prepare(
            "DELETE FROM comentarios_vr WHERE vr_id=?"
        );

        $deleteComentarios->bind_param("i", $id);
        $deleteComentarios->execute();

        $delete = $conn->prepare(
            "DELETE FROM realidad_virtual WHERE id=?"
        );

        $delete->bind_param("i", $id);
        $delete->execute();

        $conn->commit();

        if(!empty($vr['portada']) && file_exists("../uploads/vr/portadas/" . $vr['portada'])){
            unlink("../uploads/vr/portadas/" . $vr['portada']);
        }

        if(!empty($vr['qr_imagen']) && file_exists("../uploads/vr/qr/" . $vr['qr_imagen'])){
            unlink("../uploads/vr/qr/" . $vr['qr_imagen']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        die("Error al eliminar la experiencia: " . $e->getMessage());
    }
}

header("Location: dashboard.php");
exit();
