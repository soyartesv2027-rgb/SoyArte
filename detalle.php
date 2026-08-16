<?php
session_start();
include("php/conexion.php");
include("php/funciones-poesia.php");
 
if (!isset($_GET['id'])) {
    header("Location: poesia.php");
    exit;
}
 
$obra_id = (int) $_GET['id'];
$usuario_id = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
 
/* -------------------------------------------------------------
   Procesar accion de FAVORITO (viene por POST). El Like ahora
   lo maneja php/like.php directamente.
   ------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
 
    if (!$usuario_id) {
        header("Location: php/login.php");
        exit;
    }
 
    if ($_POST['accion'] === 'like') {
        $check = $conn->prepare("SELECT id FROM likes WHERE obra_id = ? AND usuario_id = ?");
        $check->bind_param("ii", $obra_id, $usuario_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $del = $conn->prepare("DELETE FROM likes WHERE obra_id = ? AND usuario_id = ?");
            $del->bind_param("ii", $obra_id, $usuario_id);
            $del->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO likes (obra_id, usuario_id) VALUES (?, ?)");
            $ins->bind_param("ii", $obra_id, $usuario_id);
            $ins->execute();
        }
    }
 
    if ($_POST['accion'] === 'favorito') {
        $check = $conn->prepare("SELECT id FROM favoritos WHERE obra_id = ? AND usuario_id = ?");
        $check->bind_param("ii", $obra_id, $usuario_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $del = $conn->prepare("DELETE FROM favoritos WHERE obra_id = ? AND usuario_id = ?");
            $del->bind_param("ii", $obra_id, $usuario_id);
            $del->execute();
        } else {
            $ins = $conn->prepare("INSERT INTO favoritos (obra_id, usuario_id) VALUES (?, ?)");
            $ins->bind_param("ii", $obra_id, $usuario_id);
            $ins->execute();
        }
    }
 
    header("Location: detalle.php?id=" . $obra_id);
    exit;
}
 
/* -------------------------------------------------------------
   Traer la obra junto con el nombre de quien la subio
   ------------------------------------------------------------- */
$sql = "SELECT obras.*, usuarios.nombre AS creador
        FROM obras
        JOIN usuarios ON obras.usuario_id = usuarios.id
        WHERE obras.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $obra_id);
$stmt->execute();
$obra = $stmt->get_result()->fetch_assoc();
 
if (!$obra) {
    echo "La obra no existe.";
    exit;
}

$esAdminDetalle = ($_SESSION['rol'] ?? '') === 'admin';
if (($obra['estado'] ?? 'publicada') !== 'publicada' && !$esAdminDetalle) {
    include("components/flash.php");
    echo "Esta publicación no está disponible.";
    exit;
}

include("components/flash.php");
 
$src = imagenSrc($obra['imagen']);
$totalLikes = $conn->query("SELECT COUNT(*) AS total FROM likes WHERE obra_id = $obra_id")->fetch_assoc()['total'];
 
$yaLeDioLike = false;
$esFavorito = false;
 
if ($usuario_id) {
    $r1 = $conn->prepare("SELECT id FROM likes WHERE obra_id = ? AND usuario_id = ?");
    $r1->bind_param("ii", $obra_id, $usuario_id);
    $r1->execute();
    $yaLeDioLike = $r1->get_result()->num_rows > 0;
 
    $r2 = $conn->prepare("SELECT id FROM favoritos WHERE obra_id = ? AND usuario_id = ?");
    $r2->bind_param("ii", $obra_id, $usuario_id);
    $r2->execute();
    $esFavorito = $r2->get_result()->num_rows > 0;
}
 
$esPropietario = $usuario_id && $usuario_id === (int) $obra['usuario_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Poema - Soy Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/poesia.css?v=3">
</head>
<body>
 
    <div class="topbar-detalle">
        <a href="poesia.php" class="btn-regresar">
            <i class="fa-solid fa-chevron-left"></i> Regresar
        </a>
        <h2>Detalles del Poema</h2>
        <?php if ($usuario_id): ?>
            <form method="POST" class="m-0">
                <input type="hidden" name="accion" value="favorito">
                <button type="submit" class="btn-favorito" style="background:none;border:none;color:inherit;">
                    Favorito <i class="fa-<?= $esFavorito ? 'solid' : 'regular' ?> fa-heart"></i>
                </button>
            </form>
        <?php else: ?>
            <span>Favorito <i class="fa-regular fa-heart"></i></span>
        <?php endif; ?>
    </div>
 
    <div class="form-obra-container">
        <div class="card-form-obra">
 
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <?php if ($src): ?>
                        <img src="<?= $src ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($obra['titulo']) ?>">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height:280px;">
                            <span class="text-muted">Foto</span>
                        </div>
                    <?php endif; ?>
                </div>
 
                <div class="col-md-8">
                    <div class="campo-detalle">
                        <label><i class="fa-solid fa-feather"></i> Autor:</label>
                        <div class="valor-campo"><?= htmlspecialchars($obra['autor']) ?></div>
                    </div>
 
                    <div class="campo-detalle">
                        <label><i class="fa-solid fa-book-open"></i> Nombre de la obra:</label>
                        <div class="valor-campo"><?= htmlspecialchars($obra['titulo']) ?></div>
                    </div>
 
                    <div class="campo-detalle">
                        <label><i class="fa-solid fa-calendar-days"></i> Fecha de Publicación:</label>
                        <div class="valor-campo"><?= htmlspecialchars(date('d/m/Y', strtotime($obra['fecha_publicacion']))) ?></div>
                    </div>
 
                    <p class="text-muted small">Subido por: <strong><?= htmlspecialchars($obra['creador']) ?></strong></p>
                </div>
            </div>
 
            <div class="campo-detalle mt-3">
                <label><i class="fa-solid fa-align-left"></i> Descripción:</label>
                <div class="valor-campo" style="white-space: pre-wrap;"><?= htmlspecialchars($obra['contenido']) ?></div>
            </div>
 
            <div class="d-flex gap-2 mt-4 flex-wrap">
                <?php if ($usuario_id): ?>
                    <form method="POST" class="m-0">
                        <input type="hidden" name="accion" value="like">
                        <button type="submit" class="btn <?= $yaLeDioLike ? 'btn-danger' : 'btn-outline-danger' ?>">
                            <i class="fa-solid fa-thumbs-up"></i> Like (<?= $totalLikes ?>)
                        </button>
                    </form>
                <?php else: ?>
                    <span class="btn btn-outline-secondary disabled"><i class="fa-solid fa-thumbs-up"></i> Like (<?= $totalLikes ?>)</span>
                <?php endif; ?>
 
                <?php if ($esPropietario): ?>
                    <a href="editar.php?id=<?= $obra['id'] ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-pen"></i> Editar</a>
                    <a href="php/eliminar-poesia.php?id=<?= $obra['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('¿Seguro que quieres eliminar esta obra?');"><i class="fa-solid fa-trash"></i> Eliminar</a>
                <?php endif; ?>
            </div>
 
        </div>
    </div>

    <!-- ==================== DENUNCIAR ==================== -->
    <?php
    $mod_tipo = 'poesia';
    $mod_id = $obra_id;
    include("components/denunciar.php");
    ?>
    <!-- ==================== FIN DENUNCIAR ==================== -->

    <!-- ==================== COMENTARIOS ==================== -->
    <?php
    // Traer comentarios de esta obra
    $sqlComentarios = "SELECT comentarios_poesia.id, comentarios_poesia.texto, comentarios_poesia.creado_en, comentarios_poesia.usuario_id, usuarios.nombre
                       FROM comentarios_poesia
                       JOIN usuarios ON comentarios_poesia.usuario_id = usuarios.id
                       WHERE comentarios_poesia.obra_id = ?
                       ORDER BY comentarios_poesia.creado_en DESC";    $stmtC = $conn->prepare($sqlComentarios);
    $stmtC->bind_param("i", $obra_id);
    $stmtC->execute();
    $comentarios = $stmtC->get_result();
    ?>
 
    <div class="form-obra-container mt-4">
        <div class="card-form-obra">
 
            <h5 style="color:var(--coral); margin-bottom:20px;">
                <i class="fa-solid fa-comments"></i> Comentarios (<?= $comentarios->num_rows ?>)
            </h5>
 
            <!-- Formulario para comentar (solo logueados) -->
            <?php if ($usuario_id): ?>
                <form method="POST" action="php/guardar-comentario-poesia.php" class="mb-4">
                    <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                    <div class="campo-detalle">
                        <label><i class="fa-solid fa-pen"></i> Escribe un comentario:</label>
                        <textarea name="texto" placeholder="¿Qué te pareció este poema?" required></textarea>
                    </div>
                    <button type="submit" class="btn-guardar" style="width:auto; padding:10px 24px;">
                        <i class="fa-solid fa-paper-plane me-2"></i> Publicar comentario
                    </button>
                </form>
            <?php else: ?>
                <div class="valor-campo mb-4" style="text-align:center; color:#888;">
                    <a href="php/login.php" style="color:var(--coral); font-weight:bold;">Inicia sesión</a> para dejar un comentario.
                </div>
            <?php endif; ?>
 
            <!-- Lista de comentarios -->
            <?php if ($comentarios->num_rows === 0): ?>
                <p class="text-muted text-center">Sé el primero en comentar este poema.</p>
            <?php endif; ?>
 
            <?php while ($com = $comentarios->fetch_assoc()): ?>
                <div style="border-bottom:1px solid var(--rosa-claro); padding:14px 0;">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:var(--rosa-medio); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:bold; font-size:0.9rem;">
                                <?= strtoupper(substr($com['nombre'], 0, 1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:0.9rem;"><?= htmlspecialchars($com['nombre']) ?></strong>
                                <span style="font-size:0.75rem; color:#aaa; margin-left:8px;">
                                    <?= date('d/m/Y H:i', strtotime($com['creado_en'])) ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($usuario_id && $com['usuario_id'] == $usuario_id): ?>
                            <form method="POST" action="php/eliminar-comentario-poesia.php" onsubmit="return confirm('¿Eliminar este comentario?');">
                                <input type="hidden" name="comentario_id" value="<?= $com['id'] ?>">
                                <input type="hidden" name="obra_id" value="<?= $obra_id ?>">
                                <button type="submit" style="background:none; border:none; color:#e57373; cursor:pointer; font-size:0.85rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <p style="margin:0; padding-left:46px; font-size:0.92rem; color:var(--texto);">
                        <?= nl2br(htmlspecialchars($com['texto'])) ?>
                    </p>
                </div>
            <?php endwhile; ?>
 
        </div>
    </div>
    <!-- ==================== FIN COMENTARIOS ==================== -->
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="JavaScript/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>
 