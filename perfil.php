<?php session_start(); 
include("php/conexion.php"); 
 
// Proteger la página if (!isset($_SESSION['usuario_id'])) {     header("Location: login.html");     exit();  
 
// Obtener datos del usuario 
$id = $_SESSION['usuario_id']; 
 
$sql = "SELECT nombre, correo, rol, biografia FROM usuarios WHERE id = ?"; 
$stmt = $conn->prepare($sql); 
$stmt->bind_param("i", $id); 
$stmt->execute(); 
$resultado = $stmt->get_result(); 
$usuario = $resultado->fetch_assoc(); 
?> 
 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <title>Perfil</title> 
    
<body class="bg-light"> 
 
<div class="container mt-5"> 
     
    <div class="card shadow p-4"> 
        <h3 class="mb-4">Perfil del Usuario</h3> 
 
        <p><strong>Nombre:</strong> <?php echo $usuario['nombre']; ?></p> 
        <p><strong>Correo:</strong> <?php echo $usuario['correo']; ?></p>         
        <p><strong>Rol:</strong> <?php echo $usuario['rol']; ?></p> 
        <textarea name="biografia" id="biografia"><?php echo $usuario['biografia']; ?></textarea>
        <a href="index.php" class="btn btn-secondary mt-3">Volver</a> 
    </div> 
 
</div> 
</body> 
</html>  	 
