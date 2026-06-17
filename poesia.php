<?php
session_start();
include("php/conexion.php");
include("php/funciones-poesia.php");
 
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';
 
if ($busqueda !== '') {
    $like = "%" . $busqueda . "%";
    $sql = "SELECT obras.id, obras.titulo, obras.imagen, usuarios.nombre AS creador
            FROM obras
            JOIN usuarios ON obras.usuario_id = usuarios.id
            WHERE obras.titulo LIKE ? OR obras.autor LIKE ?
            ORDER BY obras.id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $like, $like);
} else {
    $sql = "SELECT obras.id, obras.titulo, obras.imagen, usuarios.nombre AS creador
            FROM obras
            JOIN usuarios ON obras.usuario_id = usuarios.id
            ORDER BY obras.id DESC";
    $stmt = $conn->prepare($sql);
}
 
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poesía - Soy Arte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/poesia.css">
</head>
<body class="bg-light">
 
   
    <?php include("components/navbar.php"); ?> 
 
    <div class="text-center mt-4">
        <h2><i class="fa-solid fa-feather-pointed"></i> Poesía</h2>
        <p class="text-muted fst-italic mb-0">"Todo lo que se puede imaginar es real, si tienes el valor de perseguirlo con la mirada del alma."</p>
        <p class="text-muted small">- Dante Alighieri</p>
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
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <?php if ($src): ?>
                            <img src="<?= $src ?>" class="card-img-top" style="height:160px; object-fit:cover;" alt="Foto de la obra">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height:160px;">
                                <span class="text-muted">Foto</span>
                            </div>
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($obra['titulo']) ?></h6>
                            <p class="card-text text-muted small mb-3"><?= htmlspecialchars($obra['creador']) ?></p>
                            <a href="detalle.php?id=<?= $obra['id'] ?>" class="btn btn-outline-secondary btn-sm w-100">Más información</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
 
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <a href="publicar-poesia.php" class="btn btn-danger rounded-circle position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center" style="width:55px; height:55px;">
            <i class="fa-solid fa-plus"></i>
        </a>
    <?php else: ?>
        <a href="php/login.php" class="btn btn-danger rounded-circle position-fixed bottom-0 end-0 m-4 d-flex align-items-center justify-content-center" style="width:55px; height:55px;">
            <i class="fa-solid fa-plus"></i>
        </a>
    <?php endif; ?>
 
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>