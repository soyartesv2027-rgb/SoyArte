<?php
session_start();
include("php/conexion.php");

$usuario_actual = $_SESSION['usuario_id'] ?? 0;
$id = intval($_GET['id'] ?? 0);
if ($id === 0) { header("Location: poesia.php"); exit; }

// Cargar obra
$stmt = $conn->prepare(
    "SELECT o.*, u.nombre AS autor,
     (SELECT COUNT(*) FROM likes WHERE obra_id = o.id) AS total_likes,
     (SELECT COUNT(*) FROM likes WHERE obra_id = o.id AND usuario_id = ?) AS dio_like
     FROM obras o JOIN usuarios u ON o.usuario_id = u.id
     WHERE o.id = ?"
);
$stmt->bind_param("ii", $usuario_actual, $id);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();
if (!$obra) { header("Location: poesia.php"); exit; }

// Guardar comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario_actual > 0) {
    $texto = trim($_POST['comentario'] ?? '');
    if (!empty($texto)) {
        $ins = $conn->prepare("INSERT INTO comentarios (obra_id, usuario_id, texto) VALUES (?, ?, ?)");
        $ins->bind_param("iis", $id, $usuario_actual, $texto);
        $ins->execute();
        header("Location: detalle.php?id=$id");
        exit;
    }
}

// Cargar comentarios
$sc = $conn->prepare(
    "SELECT c.*, u.nombre AS autor_com
     FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id
     WHERE c.obra_id = ? ORDER BY c.creado_en ASC"
);
$sc->bind_param("i", $id);
$sc->execute();
$comentarios = $sc->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($obra['titulo']) ?> - Soy Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/poesia.css">
</head>
<body>

    <div class="topbar-detalle">
        <a href="poesia.php" class="btn-regresar">
            <i class="fa-solid fa-chevron-left"></i> Regresar
        </a>
        <h2>Detalles del Poema</h2>
        <?php if ($usuario_actual > 0): ?>
            <a href="like.php?id=<?= $id ?>&redirect=detalle" class="btn-favorito" title="Me gusta">
                <i class="fa-<?= $obra['dio_like'] > 0 ? 'solid' : 'regular' ?> fa-heart"></i>
            </a>
        <?php else: ?>
            <div style="width:40px"></div>
        <?php endif; ?>
    </div>

    <div class="detalle-container">
        <div class="card-detalle">

            <!-- IMAGEN -->
            <?php if (!empty($obra['imagen'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($obra['imagen']) ?>"
                     class="detalle-imagen" alt="<?= htmlspecialchars($obra['titulo']) ?>">
            <?php else: ?>
                <div class="detalle-placeholder-img">
                    <i class="fa-solid fa-image" style="font-size:2rem"></i>
                </div>
            <?php endif; ?>

            <!-- AUTOR -->
            <div class="campo-detalle">
                <label><i class="fa-solid fa-feather"></i> Autor:</label>
                <div class="valor-campo"><?= htmlspecialchars($obra['autor']) ?></div>
            </div>

            <!-- TÍTULO -->
            <div class="campo-detalle">
                <label><i class="fa-solid fa-book-open"></i> Nombre de la obra:</label>
                <div class="valor-campo"><?= htmlspecialchars($obra['titulo']) ?></div>
            </div>

            <!-- FECHA -->
            <div class="campo-detalle">
                <label><i class="fa-solid fa-calendar-days"></i> Fecha de Publicación:</label>
                <div class="valor-campo"><?= date('d/m/Y', strtotime($obra['fecha_publicacion'])) ?></div>
            </div>

            <!-- CONTENIDO -->
            <div class="campo-detalle">
                <label><i class="fa-solid fa-align-left"></i> Descripción:</label>
                <div class="valor-campo" style="white-space:pre-line;min-height:100px;align-items:flex-start;padding-top:10px;">
                    <?= htmlspecialchars($obra['contenido']) ?>
                </div>
            </div>

            <!-- LIKES -->
            <div style="margin-top:16px;">
                <?php if ($usuario_actual > 0): ?>
                    <a href="like.php?id=<?= $id ?>&redirect=detalle"
                       class="btn-like-detalle <?= $obra['dio_like'] > 0 ? 'activo' : '' ?>">
                        <i class="fa-<?= $obra['dio_like'] > 0 ? 'solid' : 'regular' ?> fa-heart"></i>
                        <?= $obra['total_likes'] ?> <?= $obra['total_likes'] == 1 ? 'like' : 'likes' ?>
                    </a>
                <?php else: ?>
                    <span class="btn-like-detalle">
                        <i class="fa-regular fa-heart"></i> <?= $obra['total_likes'] ?> likes
                    </span>
                <?php endif; ?>
            </div>

            <!-- COMENTARIOS -->
            <div class="seccion-comentarios">
                <h5><i class="fa-solid fa-comments"></i> Comentarios (<?= $comentarios->num_rows ?>)</h5>

                <?php while ($com = $comentarios->fetch_assoc()): ?>
                    <div class="comentario-item">
                        <div class="comentario-autor"><?= htmlspecialchars($com['autor_com']) ?></div>
                        <div class="comentario-texto"><?= htmlspecialchars($com['texto']) ?></div>
                        <div class="comentario-fecha"><?= date('d/m/Y H:i', strtotime($com['creado_en'])) ?></div>
                    </div>
                <?php endwhile; ?>

                <?php if ($usuario_actual > 0): ?>
                    <form method="POST" class="form-comentario">
                        <input type="text" name="comentario" placeholder="Escribe un comentario..." required>
                        <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
                    </form>
                <?php else: ?>
                    <p style="color:#888;font-size:0.85rem;margin-top:10px;">
                        <a href="php/login.php" style="color:#f9c9d4">Inicia sesión</a> para comentar.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
