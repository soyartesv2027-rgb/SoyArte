 poesia · PHP
<?php
session_start();
include("conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
 
if (!isset($_GET['id'])) {
    header("Location: ../poesia.php");
    exit;
}
 
$obra_id = (int) $_GET['id'];
$usuario_id = (int) $_SESSION['usuario_id'];
 
// Comprobar que la obra le pertenece al usuario logueado antes de borrar
$stmt = $conn->prepare("SELECT usuario_id FROM obras WHERE id = ?");
$stmt->bind_param("i", $obra_id);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();
 
if (!$obra || (int) $obra['usuario_id'] !== $usuario_id) {
    echo "No tienes permiso para eliminar esta obra.";
    exit;
}
 
// Gracias a ON DELETE CASCADE en tu base de datos, los likes, favoritos
// y comentarios asociados a esta obra se borran automaticamente.
$del = $conn->prepare("DELETE FROM obras WHERE id = ?");
$del->bind_param("i", $obra_id);
$del->execute();
 
header("Location: ../poesia.php");
exit;
