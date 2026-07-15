<?php
session_start();
require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

$mensaje = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'categoria_enviada') $mensaje = ['tipo' => 'success', 'texto' => 'Categoría enviada para revisión. Un administrador la activará pronto.'];
    if ($_GET['msg'] === 'tema_creado') $mensaje = ['tipo' => 'success', 'texto' => 'Tema creado exitosamente.'];
}

$total_categorias = 0;
$total_temas = 0;

$res_cat = $conn->query("SELECT COUNT(*) FROM foro_categorias WHERE estado='activo'");
if ($res_cat) $total_categorias = $res_cat->fetch_row()[0];

$res_tema = $conn->query("SELECT COUNT(*) FROM foro_temas");
if ($res_tema) $total_temas = $res_tema->fetch_row()[0];

$categorias = $conn->query("
    SELECT c.*, u.nombre AS creador_nombre, u.foto_perfil AS creador_foto
    FROM foro_categorias c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.estado = 'activo'
    ORDER BY c.num_temas DESC, c.nombre ASC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunidad - SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/comunidad.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("../components/navbar.php"); ?>

    <div class="foro-header">
        <h1><i class="fa-solid fa-comments"></i> Comunidad SoyArte</h1>
        <p>Comparte, discute y aprende con otros artistas salvadoreños</p>
        <div class="foro-stats">
            <span><strong><?php echo $total_categorias; ?></strong> Categorías</span>
            <span><strong><?php echo $total_temas; ?></strong> Temas</span>
        </div>
        <?php if (esAdmin()): ?>
            <a href="admin/categorias.php" class="foro-btn foro-btn-admin">
                <i class="fa-solid fa-shield-halved"></i> Administrar categorías
            </a>
        <?php endif; ?>
    </div>

    <div class="foro-container">

        <?php if ($mensaje): ?>
            <div class="foro-alert foro-alert-<?php echo $mensaje['tipo']; ?>">
                <i class="fa-solid fa-check-circle"></i> <?php echo $mensaje['texto']; ?>
            </div>
        <?php endif; ?>

        <div class="foro-actions">
            <h2>Categorías de discusión</h2>
            <a href="nueva_categoria.php" class="foro-btn foro-btn-primary">
                <i class="fa-solid fa-plus"></i> Nueva categoría
            </a>
        </div>

        <?php if ($categorias && $categorias->num_rows > 0): ?>
            <div class="categoria-grid">
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <a href="categoria.php?slug=<?php echo urlencode($cat['slug']); ?>" class="categoria-card">
                        <div class="cat-icono" style="background:<?php echo $cat['color']; ?>">
                            <i class="fa-solid <?php echo $cat['icono']; ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($cat['nombre']); ?></h3>
                        <?php if ($cat['descripcion']): ?>
                            <p><?php echo htmlspecialchars($cat['descripcion']); ?></p>
                        <?php endif; ?>
                        <div class="cat-meta">
                            <span><i class="fa-regular fa-message"></i> <?php echo $cat['num_temas']; ?> temas</span>
                        </div>
                        <div class="cat-creador">
                            Creada por <?php echo htmlspecialchars($cat['creador_nombre']); ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="foro-empty">
                <i class="fa-solid fa-comments"></i>
                <h4>No hay categorías aún</h4>
                <p>Sé el primero en crear una categoría de discusión.</p>
                <a href="nueva_categoria.php" class="foro-btn foro-btn-primary">
                    <i class="fa-solid fa-plus"></i> Crear primera categoría
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php include("../components/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/script.js"></script>
    <script src="../JavaScript/comunidad.js?v=<?php echo time(); ?>"></script>
</body>
</html>
<?php $conn->close(); ?>
