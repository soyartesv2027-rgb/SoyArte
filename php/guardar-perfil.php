<?php
session_start();               
header('Content-Type: application/json');

// Si no hay sesión activa, rechazamos la petición
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

// Recogemos los datos que mandó el formulario
$id               = $_SESSION['id'];
$nombre           = trim($_POST['nombre']          ?? '');
$correo           = trim($_POST['correo']           ?? '');
$biografia        = trim($_POST['biografia']        ?? '');
$fecha_nacimiento = $_POST['fecha_nacimiento']      ?? null;
$pais             = trim($_POST['pais']             ?? '');
$password         = $_POST['password']              ?? '';

// Validaciones básicas
if (empty($nombre) || empty($correo)) {
    echo json_encode(['error' => 'El nombre y el correo son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'El correo no tiene un formato válido.']);
    exit;
}

// Verificamos que el correo no lo tenga otro usuario
$check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
$check->bind_param('si', $correo, $id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['error' => 'Ese correo ya está en uso por otra cuenta.']);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Si el usuario escribió una nueva contraseña, la ciframos
if (!empty($password)) {
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "UPDATE usuarios
            SET nombre = ?, correo = ?, biografia = ?, fecha_nacimiento = ?, pais = ?, password = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    // s=texto, s=texto, s=texto, s=fecha, s=texto, s=hash, i=id
    $stmt->bind_param('ssssssi', $nombre, $correo, $biografia, $fecha_nacimiento, $pais, $hash, $id);

} else {
    // Sin contraseña nueva, actualizamos solo los demás campos
    $sql = "UPDATE usuarios
            SET nombre = ?, correo = ?, biografia = ?, fecha_nacimiento = ?, pais = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    // s=texto, s=texto, s=texto, s=fecha, s=texto, i=id
    $stmt->bind_param('sssssi', $nombre, $correo, $biografia, $fecha_nacimiento, $pais, $id);
}

// Ejecutamos y respondemos
if ($stmt->execute()) {
    echo json_encode(['exito' => true]);
} else {
    echo json_encode(['error' => 'No se pudieron guardar los cambios.']);
}

$stmt->close();
$conn->close();
?>