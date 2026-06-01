<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: poesia.php");
    exit();
}

$id = $_GET['id'];
$usuario_actual = $_SESSION['usuario_id'];

// Busca la obra validando que pertenezca al usuario logueado
$sql = "SELECT * FROM obras WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_actual);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();

if (!$obra) {
    header("Location: poesia.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $poema = $_POST['poema'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $contenidoImagen = file_get_contents($_FILES['imagen']['tmp_name']);
        $sqlUp = "UPDATE obras SET titulo = ?, poema = ?, imagen = ? WHERE id = ?";
        $stmtUp = $conn->prepare($sqlUp);
        $null = null;
        $stmtUp->bind_param("ssbi", $titulo, $poema, $null, $id);
        $stmtUp->send_long_data(2, $contenidoImagen);
    } else {
        $sqlUp = "UPDATE obras SET titulo = ?, poema = ? WHERE id = ?";
        $stmtUp = $conn->prepare($sqlUp);
        $stmtUp->bind_param("ssi", $titulo, $poema, $id);
    }

    $stmtUp->execute();
    header("Location: poesia.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Obra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="poesia.css">
</head>
<body class="bg-light p-5">
    <?php include("components/navbar.php"); ?>
    
    <div class="container" style="max-width: 600px;">
        <div class="card shadow p-4">
            <h3>Editar tu Obra</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($obra['titulo']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Poema</label>
                    <textarea name="poema" class="form-control" rows="6" required><?php echo htmlspecialchars($obra['poema']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cambiar ilustración (Opcional)</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-pink text-white">Guardar Cambios</button>
                <a href="poesia.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>