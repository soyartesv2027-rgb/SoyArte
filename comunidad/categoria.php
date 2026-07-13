<?php
session_start();
require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: foro.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM foro_categorias WHERE slug=? AND estado='activo'");
$stmt->bind_param("s", $slug);
$stmt->execute();
$categoria = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$categoria) {
    header("Location: foro.php");
    exit();
}

$temas = $conn->prepare("
    SELECT t.*, u.nombre AS autor_nombre, u.foto_perfil AS autor_foto,
           u2.nombre AS ultimo_nombre
    FROM foro_temas t
    JOIN usuarios u ON t.usuario_id = u.id
    LEFT JOIN usuarios u2 ON t.ultimo_usuario_id = u2.id
    WHERE t.categoria_id = ?
    ORDER BY t.es_fijado DESC, t.ultima_actividad DESC
");
$temas->bind_param("i", $categoria['id']);
$temas->execute();
$resultado_temas = $temas->get_result();
$temas->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoria['nombre']); ?> - Comunidad SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/comunidad.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("../components/navbar.php"); ?>

    <div class="foro-header">
        <h1><i class="fa-solid <?php echo $categoria['icono']; ?>" style="color:<?php echo $categoria['color']; ?>"></i> <?php echo htmlspecialchars($categoria['nombre']); ?></h1>
        <?php if ($categoria['descripcion']): ?>
            <p><?php echo htmlspecialchars($categoria['descripcion']); ?></p>
        <?php endif; ?>
    </div>

    <div class="foro-container">
        <div class="foro-actions">
            <div class="foro-actions-left">
                <a href="foro.php" class="foro-btn foro-btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Comunidad
                </a>
                <h2><?php echo $resultado_temas->num_rows; ?> temas</h2>
            </div>
            <a href="crear_tema.php?categoria=<?php echo urlencode($categoria['slug']); ?>" class="foro-btn foro-btn-primary">
                <i class="fa-solid fa-plus"></i> Nuevo tema
            </a>
        </div>

        <?php if ($resultado_temas->num_rows > 0): ?>
            <?php while ($tema = $resultado_temas->fetch_assoc()): ?>
                <div class="foro-tema-item">
                    <img src="<?php echo fotoPerfil($tema['autor_foto']); ?>" alt="" class="tema-avatar">
                    <div class="tema-body">
                        <a href="tema.php?slug=<?php echo urlencode($tema['slug']); ?>" class="tema-titulo">
                            <?php echo htmlspecialchars($tema['titulo']); ?>
                            <?php if ($tema['es_fijado']): ?>
                                <span class="badge-fijado"><i class="fa-solid fa-thumbtack"></i> Fijado</span>
                            <?php endif; ?>
                            <?php if ($tema['es_cerrado']): ?>
                                <span class="badge-cerrado"><i class="fa-solid fa-lock"></i> Cerrado</span>
                            <?php endif; ?>
                        </a>
                        <div class="tema-meta">
                            Por <a href="../perfil.php?id=<?php echo $tema['usuario_id']; ?>"><?php echo htmlspecialchars($tema['autor_nombre']); ?></a>
                            &middot; <?php echo tiempoRelativo($tema['created_at']); ?>
                            <?php if ($tema['ultimo_usuario_id']): ?>
                                &middot; Última respuesta por <a href="../perfil.php?id=<?php echo $tema['ultimo_usuario_id']; ?>"><?php echo htmlspecialchars($tema['ultimo_nombre']); ?></a>
                                &middot; <?php echo tiempoRelativo($tema['ultima_actividad']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="tema-stats">
                        <span><strong><?php echo $tema['num_respuestas']; ?></strong> <i class="fa-regular fa-message"></i></span>
                        <span><strong><?php echo $tema['vistas']; ?></strong> <i class="fa-regular fa-eye"></i></span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="foro-empty">
                <i class="fa-solid fa-message"></i>
                <h4>No hay temas en esta categoría</h4>
                <p>Sé el primero en iniciar una discusión.</p>
                <a href="crear_tema.php?categoria=<?php echo urlencode($categoria['slug']); ?>" class="foro-btn foro-btn-primary">
                    <i class="fa-solid fa-plus"></i> Crear primer tema
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
