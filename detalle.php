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
 
/* 
   Procesar acciones de LIKE y FAVORITO (vienen por POST)
   */
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
 
    // Evita que al recargar la pagina se repita el POST
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
 
/* -------------------------------------------------------------
   Contar likes y revisar si el usuario actual ya dio like / favorito
   ------------------------------------------------------------- */
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
    <title>Detalles del Poema - SoyArte</title>
    <link rel="stylesheet" href="styles/poesia.css">
</head>
<body>
 
    <div class="barra-detalle">
        <a href="poesia.php" class="volver">⬅ Regresar</a>
        <span>Detalles del Poema</span>
 
        <?php if ($usuario_id): ?>
            <form method="POST" style="margin:0;">
                <input type="hidden" name="accion" value="favorito">
                <button type="submit" class="favorito <?php echo $esFavorito ? 'activo' : ''; ?>">
                    Favorito <?php echo $esFavorito ? '♥' : '♡'; ?>
                </button>
            </form>
        <?php else: ?>
            <span class="favorito">Favorito ♡</span>
        <?php endif; ?>
    </div>
 
    <div class="detalle-wrap">
        <div class="detalle-imagen">
            <?php if ($src): ?>
                <img src="<?php echo $src; ?>" alt="<?php echo htmlspecialchars($obra['titulo']); ?>">
            <?php else: ?>
                <div class="foto-placeholder" style="width:260px;height:340px;">Foto</div>
            <?php endif; ?>
        </div>
 
        <div class="detalle-campos">
            <div class="campo-grupo">
                <label>🖋 Autor:</label>
                <input type="text" value="<?php echo htmlspecialchars($obra['autor']); ?>" readonly>
            </div>
 
            <div class="campo-grupo">
                <label>📖 Nombre de la obra:</label>
                <input type="text" value="<?php echo htmlspecialchars($obra['titulo']); ?>" readonly>
            </div>
 
            <div class="campo-grupo">
                <label>📅 Fecha de Publicación:</label>
                <input type="text" value="<?php echo htmlspecialchars(date('d/m/Y', strtotime($obra['fecha_publicacion']))); ?>" readonly>
            </div>
 
            <p style="color:#6b5a5a;">Subido por: <strong><?php echo htmlspecialchars($obra['creador']); ?></strong></p>
        </div>
    </div>
 
    <div class="campo-grupo" style="padding: 0 30px;">
        <label>🔖 Descripción:</label>
        <textarea readonly><?php echo htmlspecialchars($obra['contenido']); ?></textarea>
    </div>
 
    <div class="acciones-detalle">
        <?php if ($usuario_id): ?>
            <form method="POST">
                <input type="hidden" name="accion" value="like">
                <button type="submit" class="btn-accion like <?php echo $yaLeDioLike ? 'activo' : ''; ?>">
                    👍 Like (<?php echo $totalLikes; ?>)
                </button>
            </form>
        <?php else: ?>
            <span class="btn-accion">👍 Like (<?php echo $totalLikes; ?>)</span>
        <?php endif; ?>
 
        <?php if ($esPropietario): ?>
            <a href="editar.php?id=<?php echo $obra['id']; ?>" class="btn-accion">Editar</a>
            <a href="eliminar.php?id=<?php echo $obra['id']; ?>" class="btn-accion peligro" onclick="return confirm('¿Seguro que quieres eliminar esta obra?');">Eliminar</a>
        <?php endif; ?>
    </div>
 
</body>
</html>
<?php $conn->close(); ?>