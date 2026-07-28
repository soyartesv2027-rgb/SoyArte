<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../funciones_foro.php';

if (!isset($_SESSION['usuario_id']) || !esAdmin()) {
    header("Location: ../../login.html");
    exit();
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion   = $_POST['accion'] ?? '';
    $cat_id   = (int)($_POST['categoria_id'] ?? 0);

    if ($accion === 'aprobar' && $cat_id > 0) {
        $stmt = $conn->prepare("UPDATE foro_categorias SET estado='activo' WHERE id=?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $mensaje = 'Categoría aprobada';
    } elseif ($accion === 'desactivar' && $cat_id > 0) {
        $stmt = $conn->prepare("UPDATE foro_categorias SET estado='inactivo' WHERE id=?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $mensaje = 'Categoría desactivada';
    } elseif ($accion === 'reactivar' && $cat_id > 0) {
        $stmt = $conn->prepare("UPDATE foro_categorias SET estado='activo' WHERE id=?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $mensaje = 'Categoría reactivada';
    } elseif ($accion === 'rechazar' && $cat_id > 0) {
        $stmt = $conn->prepare("DELETE FROM foro_categorias WHERE id=?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $mensaje = 'Categoría rechazada y eliminada';
    }
}

$pendientes = $conn->query("
    SELECT c.*, u.nombre AS creador
    FROM foro_categorias c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.estado = 'pendiente'
    ORDER BY c.created_at DESC
");

$activas = $conn->query("
    SELECT c.*, u.nombre AS creador
    FROM foro_categorias c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.estado = 'activo'
    ORDER BY c.num_temas DESC
");

$inactivas = $conn->query("
    SELECT c.*, u.nombre AS creador
    FROM foro_categorias c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.estado = 'inactivo'
    ORDER BY c.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Categorías - Comunidad SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../styles/comunidad.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="foro-header">
        <h1><i class="fa-solid fa-shield-halved"></i> Admin - Categorías</h1>
        <p>Gestiona las categorías de la comunidad</p>
    </div>

    <div class="foro-container">
        <div class="foro-actions">
            <h2>Panel de moderación</h2>
            <a href="../../foro.php" class="foro-btn foro-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Volver a Comunidad
            </a>
        </div>

        <?php if ($mensaje): ?>
            <div class="foro-alert foro-alert-success">
                <i class="fa-solid fa-check-circle"></i> <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="admin-categorias">

            <div class="admin-section">
                <h3><i class="fa-solid fa-clock"></i> Pendientes <span class="count">(<?php echo $pendientes->num_rows; ?>)</span></h3>
                <?php if ($pendientes->num_rows === 0): ?>
                    <div class="foro-empty"><p>No hay categorías pendientes</p></div>
                <?php else: ?>
                    <?php while ($cat = $pendientes->fetch_assoc()): ?>
                        <div class="admin-card">
                            <div class="admin-info">
                                <strong>
                                    <i class="fa-solid <?php echo $cat['icono']; ?>" style="color:<?php echo $cat['color']; ?>"></i>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </strong>
                                <span>Creada por <?php echo htmlspecialchars($cat['creador']); ?> — <?php echo htmlspecialchars($cat['descripcion']); ?></span>
                            </div>
                            <div class="admin-actions">
                                <form method="POST">
                                    <input type="hidden" name="categoria_id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" name="accion" value="aprobar" class="foro-btn foro-btn-sm foro-btn-primary">
                                        <i class="fa-solid fa-check"></i> Aprobar
                                    </button>
                                    <button type="submit" name="accion" value="rechazar" class="foro-btn foro-btn-sm foro-btn-danger" onclick="return confirm('¿Eliminar esta categoría?')">
                                        <i class="fa-solid fa-xmark"></i> Rechazar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="admin-section">
                <h3><i class="fa-solid fa-check-circle"></i> Activas <span class="count">(<?php echo $activas->num_rows; ?>)</span></h3>
                <?php if ($activas->num_rows === 0): ?>
                    <div class="foro-empty"><p>No hay categorías activas</p></div>
                <?php else: ?>
                    <?php while ($cat = $activas->fetch_assoc()): ?>
                        <div class="admin-card">
                            <div class="admin-info">
                                <strong>
                                    <i class="fa-solid <?php echo $cat['icono']; ?>" style="color:<?php echo $cat['color']; ?>"></i>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </strong>
                                <span><?php echo $cat['num_temas']; ?> temas — Creada por <?php echo htmlspecialchars($cat['creador']); ?></span>
                            </div>
                            <div class="admin-actions">
                                <form method="POST">
                                    <input type="hidden" name="categoria_id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" name="accion" value="desactivar" class="foro-btn foro-btn-sm foro-btn-danger">
                                        <i class="fa-solid fa-pause"></i> Desactivar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

            <div class="admin-section">
                <h3><i class="fa-solid fa-pause"></i> Inactivas <span class="count">(<?php echo $inactivas->num_rows; ?>)</span></h3>
                <?php if ($inactivas->num_rows === 0): ?>
                    <div class="foro-empty"><p>No hay categorías inactivas</p></div>
                <?php else: ?>
                    <?php while ($cat = $inactivas->fetch_assoc()): ?>
                        <div class="admin-card">
                            <div class="admin-info">
                                <strong>
                                    <i class="fa-solid <?php echo $cat['icono']; ?>" style="color:<?php echo $cat['color']; ?>"></i>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </strong>
                                <span>Creada por <?php echo htmlspecialchars($cat['creador']); ?></span>
                            </div>
                            <div class="admin-actions">
                                <form method="POST">
                                    <input type="hidden" name="categoria_id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" name="accion" value="reactivar" class="foro-btn foro-btn-sm foro-btn-primary">
                                        <i class="fa-solid fa-play"></i> Reactivar
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../JavaScript/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>
