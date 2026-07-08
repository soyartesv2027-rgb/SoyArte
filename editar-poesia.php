<?php
session_start();
include("php/conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: php/login.php");
    exit;
}
 
if (!isset($_GET['id'])) {
    header("Location: poesia.php");
    exit;
}
 
$obra_id = (int) $_GET['id'];
$usuario_id = (int) $_SESSION['usuario_id'];
 
$stmt = $conn->prepare("SELECT * FROM obras WHERE id = ?");
$stmt->bind_param("i", $obra_id);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();
 
if (!$obra || (int) $obra['usuario_id'] !== $usuario_id) {
    echo "No tienes permiso para editar esta obra.";
    exit;
}
 
// Recuperar errores y datos previos si vienen de actualizar-poesia.php
$errores = $_SESSION['errores_editar'] ?? [];
$datos   = $_SESSION['datos_editar']   ?? [];
unset($_SESSION['errores_editar'], $_SESSION['datos_editar']);
 
$autorActual     = $datos['autor']             ?? $obra['autor'];
$tituloActual    = $datos['titulo']            ?? $obra['titulo'];
$fechaActual     = $datos['fecha_publicacion'] ?? date('Y-m-d', strtotime($obra['fecha_publicacion']));
$contenidoActual = $datos['contenido']         ?? $obra['contenido'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Poema - Soy Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles/poesia.css?v=<?php echo time(); ?>">
</head>
<body>
 
    <div class="topbar-detalle">
        <a href="detalle.php?id=<?= $obra_id ?>" class="btn-regresar">
            <i class="fa-solid fa-chevron-left"></i> Regresar
        </a>
        <h2>Editar Poema</h2>
        <div style="width:80px"></div>
    </div>
 
    <div class="form-obra-container">
 
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger small mb-3">
                <ul class="mb-0">
                    <?php foreach ($errores as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
 
        <div class="card-form-obra">
            <form method="POST" action="php/actualizar-poesia.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $obra_id ?>">
 
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-image"></i> Imagen de portada</label>
                    <label class="upload-imagen-label" for="inputImagen">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Cambiar imagen (opcional)
                    </label>
                    <input type="file" id="inputImagen" name="imagen" accept="image/*">
                    <img id="previewImagen" src="" alt="Vista previa" style="display:none; max-width:100%; margin-top:10px; border-radius:8px;">
                </div>
 
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-feather"></i> Autor:</label>
                    <input type="text" name="autor" value="<?= htmlspecialchars($autorActual) ?>" required>
                </div>
 
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-book-open"></i> Nombre de la obra:</label>
                    <input type="text" name="titulo" value="<?= htmlspecialchars($tituloActual) ?>" required>
                </div>
 
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-calendar-days"></i> Fecha de Publicación:</label>
                    <input type="date" name="fecha_publicacion" value="<?= htmlspecialchars($fechaActual) ?>" required>
                </div>
 
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-align-left"></i> Descripción:</label>
                    <textarea name="contenido"><?= htmlspecialchars($contenidoActual) ?></textarea>
                </div>
 
                <button type="submit" class="btn-guardar">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>
 
    <script>
        document.getElementById('inputImagen').addEventListener('change', function () {
            const preview = document.getElementById('previewImagen');
            const file    = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="JavaScript/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>