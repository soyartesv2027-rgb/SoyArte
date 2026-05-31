<?php
// =============================================
// obtener_perfil.php  —  Soy Arte
// Devuelve los datos del usuario en sesión
// =============================================

session_start();                    // Siempre lo primero
header('Content-Type: application/json');

// Si no hay sesión, devolvemos error
if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'No has iniciado sesión.']);
    exit;
}

// Conexión a la base de datos
$conn = new mysqli('localhost', 'root', '', 'soyarte');

if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión.']);
    exit;
}

// Buscamos al usuario por su ID de sesión
// Nunca mostramos la contraseña
$id  = $_SESSION['id'];
$sql = "SELECT nombre, correo, biografia, fecha_nacimiento, pais
        FROM usuarios
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);        // 'i' = entero
$stmt->execute();
$resultado = $stmt->get_result();
$usuario   = $resultado->fetch_assoc();

if ($usuario) {
    echo json_encode($usuario);     // Enviamos los datos como JSON
} else {
    echo json_encode(['error' => 'Usuario no encontrado.']);
}

$stmt->close();
$conn->close();
?>