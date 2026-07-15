<?php
session_start();
require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

$cat_slug = $_GET['categoria'] ?? '';
$categoria = null;

if ($cat_slug) {
    $stmt = $conn->prepare("SELECT id, nombre, slug FROM foro_categorias WHERE slug=? AND estado='activo'");
    $stmt->bind_param("s", $cat_slug);
    $stmt->execute();
    $categoria = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$categoria) {
    header("Location: foro.php");
    exit();
}

$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'vacio') $error = 'El título y el contenido son obligatorios.';
    if ($_GET['error'] === 'error') $error = 'Ocurrió un error al crear el tema.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Tema - Comunidad SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/comunidad.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("../components/navbar.php"); ?>

    <div class="foro-header">
        <h1><i class="fa-solid fa-feather-pointed"></i> Nuevo Tema</h1>
        <p>En <?php echo htmlspecialchars($categoria['nombre']); ?></p>
    </div>

    <div class="foro-container">
        <div class="foro-actions">
            <h2>Crear tema de discusión</h2>
            <a href="categoria.php?slug=<?php echo urlencode($categoria['slug']); ?>" class="foro-btn foro-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if ($error): ?>
            <div class="foro-alert foro-alert-error"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form class="foro-form" method="POST" action="procesos/crear_tema.php">
            <input type="hidden" name="categoria_id" value="<?php echo $categoria['id']; ?>">
            <input type="hidden" name="categoria_slug" value="<?php echo htmlspecialchars($categoria['slug']); ?>">

            <div class="form-group">
                <label for="titulo">Título del tema *</label>
                <input type="text" id="titulo" name="titulo" required maxlength="200" placeholder="Ej: ¿Qué técnicas usan para retratos?">
            </div>

            <div class="form-group">
                <label for="contenido">Contenido *</label>
                <textarea id="contenido" name="contenido" required rows="8" placeholder="Escribe tu mensaje aquí... Consejos: sé específico, respeta a los demás, usa párrafos."></textarea>
            </div>

            <button type="submit" class="foro-btn foro-btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Publicar tema
            </button>
        </form>
    </div>

    <?php include("../components/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>
