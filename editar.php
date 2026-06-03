<?php
session_start();
include("php/conexion.php"); //

// SEGURIDAD: Si no está logueado, afuera
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_actual = $_SESSION['usuario_id'];
$id_obra = $_GET['id'] ?? 0;

// Traer la obra actual para rellenar los cuadros
$sql = "SELECT * FROM obras WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_obra, $usuario_actual);
$stmt->execute();
$resultado = $stmt->get_result();
$obra = $resultado->fetch_assoc();

// Si la obra no existe o no le pertenece, lo saca al muro
if (!$obra) {
    header("Location: poesia.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Soy Arte - Editar Obra</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/style.css"> </head>
<body class="bg-light"> <?php include("components/navbar.php"); ?> <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow p-4 border-0" style="border-radius: 15px;">
            <h3 class="text-center mb-4 text-dark"><i class="fa-solid fa-pen-to-square"></i> Editar tu Obra</h3>
            
            <form action="procesar_actualizacion.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $obra['id']; ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">Título</label>
                    <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($obra['titulo']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Poema</label>
                    <textarea name="poema" class="form-control" rows="6" required><?php echo htmlspecialchars($obra['poema']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Cambiar ilustración (Opcional)</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*">
                </div>
                
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="poesia.php" class="btn btn-secondary px-4" style="border-radius: 20px;">Cancelar</a>
                    <button type="submit" class="btn text-white px-4" style="background-color: #e8b4b8; border-radius: 20px;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>