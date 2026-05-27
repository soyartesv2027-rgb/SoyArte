<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$usuario_actual = $_SESSION['usuario_id'];

// Carga las obras, sus autores, cuenta los likes y mira si el usuario actual le dio like
$sql = "SELECT o.*, u.nombre AS autor,
        (SELECT COUNT(*) FROM likes WHERE obra_id = o.id) AS total_likes,
        (SELECT COUNT(*) FROM likes WHERE obra_id = o.id AND usuario_id = ?) AS dio_like
        FROM obras o 
        JOIN usuarios u ON o.usuario_id = u.id 
        ORDER BY o.fecha_publicacion DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_actual);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Soy Arte - Poesías</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="poesia.css">
</head>
<body class="bg-light">
<?php include("componentes/navbar.php"); ?>

    <div class="container">
        <h2 class="text-center mb-4">- Galería de Poesías -</h2>
        
        <div class="row">
            <?php while ($obra = $resultado->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        
                        <?php if (!empty($obra['imagen'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($obra['imagen']); ?>" class="card-img-top" style="height: 250px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary text-white text-center py-5" style="height: 250px;">Sin ilustración</div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($obra['titulo']); ?></h5>
                            <p class="text-muted small">Por: <?php echo htmlspecialchars($obra['autor']); ?></p>
                            <p class="card-text flex-grow-1" style="white-space: pre-line;"><?php echo htmlspecialchars($obra['poema']); ?></p>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <a href="like.php?id=<?php echo $obra['id']; ?>" class="btn <?php echo $obra['dio_like'] > 0 ? 'btn-pink' : 'btn-outline-pink'; ?> btn-sm">
                                    <i class="fa-<?php echo $obra['dio_like'] > 0 ? 'solid' : 'regular'; ?> fa-heart"></i> 
                                    <?php echo $obra['total_likes']; ?> Me gusta
                                </a>

                                <?php if ($obra['usuario_id'] == $usuario_actual): ?>
                                    <div>
                                        <a href="editar.php?id=<?php echo $obra['id']; ?>" class="btn btn-warning btn-sm text-white">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="eliminar-poesia.php?id=<?php echo $obra['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar tu poema?');" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>