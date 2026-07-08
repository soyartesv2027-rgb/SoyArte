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
    <link rel="stylesheet" href="styles/poesia.css?v=2">
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
                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                            style="height:280px;">
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
                        <div class="valor-campo">
                            <?= htmlspecialchars(date('d/m/Y', strtotime($obra['fecha_publicacion']))) ?></div>
                    </div>

                    <p class="text-muted small">Subido por: <strong><?= htmlspecialchars($obra['creador']) ?></strong>
                    </p>
                </div>
            </div>

            <div class="campo-detalle mt-3">
                <label><i class="fa-solid fa-align-left"></i> Descripción:</label>
                <div class="valor-campo" style="white-space: pre-wrap;"><?= htmlspecialchars($obra['contenido']) ?>
                </div>
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
                    <span class="btn btn-outline-secondary disabled"><i class="fa-solid fa-thumbs-up"></i> Like
                        (<?= $totalLikes ?>)</span>
                <?php endif; ?>

                <?php if ($esPropietario): ?>
                    <a href="editar-poesia.php?id=<?= $obra['id'] ?>" class="btn btn-outline-secondary"><i
                            class="fa-solid fa-pen"></i> Editar</a>
                    <a href="php/eliminar-poesia.php?id=<?= $obra['id'] ?>" class="btn btn-outline-danger"
                        onclick="return confirm('¿Seguro que quieres eliminar esta obra?');"><i
                            class="fa-solid fa-trash"></i> Eliminar</a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="JavaScript/script.js"></script>
</body>

</html>
<?php $conn->close(); ?>