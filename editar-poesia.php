<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: php/login.php");
    exit;
}

$usuario_actual = $_SESSION['usuario_id'];
$id = intval($_GET['id'] ?? 0);

// Cargar obra y verificar que pertenece al usuario logueado
$stmt = $conn->prepare("SELECT * FROM obras WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_actual);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();

// Si no existe o no es suya, redirigir
if (!$obra) {
    header("Location: poesia.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if (empty($titulo) || empty($contenido)) {
        $error = "El título y el contenido son obligatorios.";
    } else {
        if (!empty($_FILES['imagen']['tmp_name'])) {
            $tipo    = $_FILES['imagen']['type'];
            $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
            if (!in_array($tipo, $allowed)) {
                $error = "Solo se permiten imágenes JPG, PNG, GIF o WEBP.";
            } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                $error = "La imagen no puede superar 5MB.";
            } else {
                $imagen = file_get_contents($_FILES['imagen']['tmp_name']);
                $s = $conn->prepare("UPDATE obras SET titulo=?, contenido=?, imagen=? WHERE id=? AND usuario_id=?");
                $s->bind_param("ssbii", $titulo, $contenido, $imagen, $id, $usuario_actual);
                if ($s->execute()) { header("Location: poesia.php"); exit; }
                else { $error = "Error al guardar."; }
            }
        } else {
            $s = $conn->prepare("UPDATE obras SET titulo=?, contenido=? WHERE id=? AND usuario_id=?");
            $s->bind_param("ssii", $titulo, $contenido, $id, $usuario_actual);
            if ($s->execute()) { header("Location: poesia.php"); exit; }
            else { $error = "Error al guardar."; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Poema - Soy Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/poesia.css">
</head>
<body>

    <div class="topbar-detalle">
        <a href="poesia.php" class="btn-regresar">
            <i class="fa-solid fa-chevron-left"></i> Regresar
        </a>
        <h2>Editar Poema</h2>
        <div style="width:80px"></div>
    </div>

    <div class="form-obra-container">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger small mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card-form-obra">
            <form method="POST" enctype="multipart/form-data">

                <!-- IMAGEN ACTUAL -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-image"></i> Imagen de portada</label>
                    <?php if (!empty($obra['imagen'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($obra['imagen']) ?>"
                             style="width:100%;max-height:180px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
                    <?php endif; ?>
                    <label class="upload-imagen-label" for="inputImagen">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <?= !empty($obra['imagen']) ? 'Cambiar imagen' : 'Subir imagen (opcional)' ?>
                    </label>
                    <input type="file" id="inputImagen" name="imagen" accept="image/*">
                    <img id="previewImagen" src="" alt="Vista previa">
                </div>

                <!-- AUTOR -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-feather"></i> Autor:</label>
                    <div class="valor-campo"><?= htmlspecialchars($_SESSION['nombre']) ?></div>
                </div>

                <!-- TÍTULO -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-book-open"></i> Nombre de la obra:</label>
                    <input type="text" name="titulo"
                           value="<?= htmlspecialchars($_POST['titulo'] ?? $obra['titulo']) ?>" required>
                </div>

                <!-- FECHA -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-calendar-days"></i> Fecha de Publicación:</label>
                    <div class="valor-campo"><?= date('d/m/Y', strtotime($obra['fecha_publicacion'])) ?></div>
                </div>

                <!-- CONTENIDO -->
                <div class="campo-detalle">
                    <label><i class="fa-solid fa-align-left"></i> Descripción:</label>
                    <textarea name="contenido"><?= htmlspecialchars($_POST['contenido'] ?? $obra['contenido']) ?></textarea>
                </div>

                <button type="submit" class="btn-guardar">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar cambios
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
            }
        });
    </script>
</body>
</html>