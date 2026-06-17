<?php
session_start();
include("php/conexion.php");
include("php/funciones-poesia.php");
 
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
$error = '';
 
// Traer la obra y comprobar que pertenece al usuario logueado
$stmt = $conn->prepare("SELECT * FROM obras WHERE id = ?");
$stmt->bind_param("i", $obra_id);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();
 
if (!$obra || (int) $obra['usuario_id'] !== $usuario_id) {
    echo "No tienes permiso para editar esta obra.";
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $autor             = trim($_POST['autor'] ?? '');
    $titulo            = trim($_POST['titulo'] ?? '');
    $fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');
    $contenido         = trim($_POST['contenido'] ?? '');
    $nuevaImagen       = null;
    $hayImagenNueva    = false;
 
    if ($autor === '' || $titulo === '' || $fecha_publicacion === '') {
        $error = "Autor, nombre de la obra y fecha de publicación son obligatorios.";
    } else {
 
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
 
            if (in_array($extension, $extensionesPermitidas)) {
                $nuevaImagen = file_get_contents($_FILES['foto']['tmp_name']);
                $hayImagenNueva = true;
            } else {
                $error = "Formato de imagen no permitido. Usa jpg, png, gif o webp.";
            }
        }
 
        if ($error === '') {
            if ($hayImagenNueva) {
                $sql = "UPDATE obras SET autor = ?, titulo = ?, contenido = ?, fecha_publicacion = ?, imagen = ?
                        WHERE id = ?";
                $upd = $conn->prepare($sql);
                $upd->bind_param("sssssi", $autor, $titulo, $contenido, $fecha_publicacion, $nuevaImagen, $obra_id);
            } else {
                // No se subio una foto nueva, se conserva la que ya estaba
                $sql = "UPDATE obras SET autor = ?, titulo = ?, contenido = ?, fecha_publicacion = ?
                        WHERE id = ?";
                $upd = $conn->prepare($sql);
                $upd->bind_param("ssssi", $autor, $titulo, $contenido, $fecha_publicacion, $obra_id);
            }
 
            if ($upd->execute()) {
                header("Location: detalle.php?id=" . $obra_id);
                exit;
            } else {
                $error = "Ocurrió un error al actualizar la obra.";
            }
        }
    }
}
 
// Para mostrar la fecha en el input type="date" (formato YYYY-MM-DD)
$fechaParaInput = date('Y-m-d', strtotime($obra['fecha_publicacion']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Obra - SoyArte</title>
    <link rel="stylesheet" href="styles/poesia.css">
</head>
<body>
 
    <div class="barra-detalle">
        <a href="detalle.php?id=<?php echo $obra_id; ?>" class="volver">⬅ Regresar</a>
        <span>Editar Obra</span>
        <span></span>
    </div>
 
    <form class="form-agregar" method="POST" enctype="multipart/form-data">
 
        <?php if ($error !== ''): ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
 
        <div class="campo-grupo">
            <label>🖋 Autor:</label>
            <input type="text" name="autor" value="<?php echo htmlspecialchars($obra['autor']); ?>" required>
        </div>
 
        <div class="campo-grupo">
            <label>📖 Nombre de la obra:</label>
            <input type="text" name="titulo" value="<?php echo htmlspecialchars($obra['titulo']); ?>" required>
        </div>
 
        <div class="campo-grupo">
            <label>📅 Fecha de Publicación:</label>
            <input type="date" name="fecha_publicacion" value="<?php echo htmlspecialchars($fechaParaInput); ?>" required>
        </div>
 
        <div class="campo-grupo">
            <label>🔖 Descripción:</label>
            <textarea name="contenido"><?php echo htmlspecialchars($obra['contenido']); ?></textarea>
        </div>
 
        <div class="campo-grupo">
            <label>🖼 Cambiar foto (opcional):</label>
            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif,.webp">
        </div>
 
        <button type="submit" class="btn-accion">Guardar Cambios</button>
    </form>
 
</body>
</html>
<?php $conn->close(); ?>