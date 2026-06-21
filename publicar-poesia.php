<?php
session_start();
include("php/conexion.php");
 
if (!isset($_SESSION['usuario_id'])) {
    header("Location: php/login.php");
    exit;
}
 
// Recuperar errores y datos previos si vienen de procesar-poesia.php
$errores = $_SESSION['errores_publicar'] ?? [];
$datos   = $_SESSION['datos_publicar']   ?? [];
unset($_SESSION['errores_publicar'], $_SESSION['datos_publicar']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Poema - Soy Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/poesia.css">
</head>
<body>
 
    <div class="topbar-detalle">
        <a href="poesia.php" class="btn-regresar">
            <i class="fa-solid fa-chevron-left"></i> Regresar
        </a>
        <h2>Publicar Poema</h2>
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
            <form method="POST" action="php/procesar-poesia.php" enctype="multipart/form-data">
 
                <!-- IMAGEN -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-image"></i> Imagen de portada</label>
                    <label class="upload-imagen-label" for="inputImagen">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Subir imagen (opcional)
                    </label>
                    <input type="file" id="inputImagen" name="imagen" accept="image/*">
                    <img id="previewImagen" src="" alt="Vista previa" style="display:none; max-width:100%; margin-top:10px; border-radius:8px;">
                </div>
 
                <!-- AUTOR (texto libre, el usuario escribe el que quiera) -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-feather"></i> Autor:</label>
                    <input type="text" name="autor"
                           placeholder="Nombre del autor del poema"
                           value="<?= htmlspecialchars($datos['autor'] ?? '') ?>" required>
                </div>
 
                <!-- TÍTULO -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-book-open"></i> Nombre de la obra:</label>
                    <input type="text" name="titulo"
                           placeholder="Título del poema"
                           value="<?= htmlspecialchars($datos['titulo'] ?? '') ?>" required>
                </div>
 
                <!-- FECHA (el usuario la elige con un selector) -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-calendar-days"></i> Fecha de Publicación:</label>
                    <input type="date" name="fecha_publicacion"
                           value="<?= htmlspecialchars($datos['fecha_publicacion'] ?? date('Y-m-d')) ?>" required>
                </div>
 
                <!-- CONTENIDO -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-align-left"></i> Descripción:</label>
                    <textarea name="contenido"
                              placeholder="Escribe tu poema aquí..."><?= htmlspecialchars($datos['contenido'] ?? '') ?></textarea>
                </div>
 
                <button type="submit" class="btn-guardar">
                    <i class="fa-solid fa-paper-plane me-2"></i> Publicar poema
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
</body>
</html>