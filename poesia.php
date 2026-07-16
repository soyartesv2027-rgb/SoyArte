<?php
session_start();
include("php/conexion.php");
include("php/funciones-poesia.php");
 
$usuario_id = isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
 
/*  (boton "Like" en cada poema) */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'like') {
 
    if (!$usuario_id) {
        header("Location: php/login.php");
        exit;
    }
 
    $obra_id_like = (int) ($_POST['obra_id'] ?? 0);
 
    $check = $conn->prepare("SELECT id FROM likes WHERE obra_id = ? AND usuario_id = ?");
    $check->bind_param("ii", $obra_id_like, $usuario_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM likes WHERE obra_id = ? AND usuario_id = ?");
        $del->bind_param("ii", $obra_id_like, $usuario_id);
        $del->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO likes (obra_id, usuario_id) VALUES (?, ?)");
        $ins->bind_param("ii", $obra_id_like, $usuario_id);
        $ins->execute();
    }
 
    $queryString = isset($_POST['q']) && $_POST['q'] !== '' ? '?q=' . urlencode($_POST['q']) : '';
    header("Location: poesia.php" . $queryString);
    exit;
}
 
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
$usuarioParaLikes = $usuario_id ?? 0; // 0 nunca coincide con un usuario real
 
if ($busqueda !== '') {
    $like = "%" . $busqueda . "%";
    $sql = "SELECT obras.id, obras.titulo, obras.imagen, usuarios.nombre AS creador,
                   (SELECT COUNT(*) FROM likes WHERE likes.obra_id = obras.id) AS total_likes,
                   EXISTS(SELECT 1 FROM likes WHERE likes.obra_id = obras.id AND likes.usuario_id = ?) AS ya_le_dio_like
            FROM obras
            JOIN usuarios ON obras.usuario_id = usuarios.id
            WHERE obras.titulo LIKE ? OR obras.autor LIKE ?
            ORDER BY obras.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $usuarioParaLikes, $like, $like);
} else {
    $sql = "SELECT obras.id, obras.titulo, obras.imagen, usuarios.nombre AS creador,
                   (SELECT COUNT(*) FROM likes WHERE likes.obra_id = obras.id) AS total_likes,
                   EXISTS(SELECT 1 FROM likes WHERE likes.obra_id = obras.id AND likes.usuario_id = ?) AS ya_le_dio_like
            FROM obras
            JOIN usuarios ON obras.usuario_id = usuarios.id
            ORDER BY obras.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioParaLikes);
}
 
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poesía</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/poesia.css?v=3">
    <link rel="stylesheet" href="styles/poesia.css?v=2">
    <link rel="stylesheet" href="styles/poesia.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <?php $seccion = 'poesia'; include("components/navbar-unificado.php"); ?>
    <div class="hero-poesia">
        <div class="hero-poesia-img"></div>
        <div class="text-center hero-poesia-texto">
            <h2><i class="fa-solid fa-feather-pointed"></i> Poesía</h2>
            <p class="text-muted fst-italic mb-0">"Todo lo que se puede imaginar es real, si tienes el valor de perseguirlo con la mirada del alma."</p>
            <p class="text-muted small">- Dante Alighieri</p>
        </div>
    </div>
 
    <div class="d-flex justify-content-center my-4 px-3">
        <form method="GET" action="poesia.php" class="w-100" style="max-width:600px;">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="q" class="form-control" placeholder="Buscar" value="<?= htmlspecialchars($busqueda) ?>">
            </div>
        </form>
    </div>
 
    <div class="container pb-5">
        <div class="row g-4">
            <?php if ($resultado->num_rows === 0): ?>
                <p class="text-center text-muted">No se encontraron obras todavía.</p>
            <?php endif; ?>
 
           <?php while ($obra = $resultado->fetch_assoc()): ?>
                <?php $src = imagenSrc($obra['imagen']); ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm tarjeta-obra">
                        
                        <div class="tarjeta-img-wrap">
                            <?php if ($src): ?>
                                <img src="<?= $src ?>" class="card-img-top" alt="Foto de la obra">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;">
                                    <span class="text-muted">Foto</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body text-center">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($obra['titulo']) ?></h6>
                            <p class="card-text text-muted small mb-2"><?= htmlspecialchars($obra['creador']) ?></p>

                            <?php if ($usuario_id): ?>
                                <form method="POST" class="mb-2">
                                    <input type="hidden" name="accion" value="like">
                                    <input type="hidden" name="obra_id" value="<?= $obra['id'] ?>">
                                    <input type="hidden" name="q" value="<?= htmlspecialchars($busqueda) ?>">
                                    <button type="submit" class="btn-like-tarjeta <?= $obra['ya_le_dio_like'] ? 'activo' : '' ?>">
                                        <i class="fa-solid fa-thumbs-up"></i> <?= $obra['total_likes'] ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="btn-like-tarjeta d-inline-block mb-2">
                                    <i class="fa-solid fa-thumbs-up"></i> <?= $obra['total_likes'] ?>
                                </span>
                            <?php endif; ?>

                            <a href="detalle.php?id=<?= $obra['id'] ?>" class="btn btn-outline-secondary btn-sm w-100">Más información</a>
                        </div>
                        
                        </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
 
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <a href="publicar-poesia.php" class="floating-btn">
        <i class="fa-solid fa-plus"></i>
    </a>
<?php else: ?>
    <a href="php/login.php" class="floating-btn">
        <i class="fa-solid fa-plus"></i>
    </a>
<?php endif; ?>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    window.addEventListener("scroll", () => {
      const section = document.querySelector(".info-soyarte");
      if (section) {
        const position = section.getBoundingClientRect().top;
        const screen = window.innerHeight;
        if (position < screen - 100) {
          section.classList.add("visible");
        }
      }
    });
  </script>
  <script src="JavaScript/script.js"></script>
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>