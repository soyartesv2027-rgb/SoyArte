<?php
session_start();
include("php/conexion.php"); 

// Si no hay sesión, usuario_actual será 0
$usuario_actual = $_SESSION['usuario_id'] ?? 0;

// Carga las obras conectando con la tabla usuarios
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soy Arte - Poesías</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
    <link rel="stylesheet" href="style.css">
=======
>>>>>>> bf03b9a (actualice)
>>>>>>> c7012318110b505b1ac1f8c52f2ef10e4334d4ef
    <link rel="stylesheet" href="poesia.css"> 
=======
    <link rel="stylesheet" href="styles/poesia.css"> </head>
=======
    <link rel="stylesheet" href="styles/poesia.css"> 
>>>>>>> 3fed72177666439cd0117aca13d22444839e2648
</head>
>>>>>>> 1048bb5d3a69d91b2a97c4156bd17884f4d9c141
<body class="bg-light">

    <?php include("components/navbar.php"); ?> 
<<<<<<< HEAD
=======
<<<<<<< HEAD
    <div class="container mt-4">
        <div class="text-center p-4 bg-white shadow-sm rounded mb-4">
=======
>>>>>>> c7012318110b505b1ac1f8c52f2ef10e4334d4ef

    <div class="container mt-4">
        <div class="text-center p-5 rounded mb-4 shadow-sm text-white" 
             style="background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('img/download.jpg'); background-size: cover; background-position: center; min-height: 200px;">
            
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> bf03b9a (actualice)
>>>>>>> c7012318110b505b1ac1f8c52f2ef10e4334d4ef
            <h1 class="display-6 fw-bold text-dark"><i class="fa-solid fa-book-open"></i> Poesía</h1>
            <p class="fst-italic text-muted">"Todo lo que se puede imaginar es real, si tienes el valor de perseguirlo con la mirada del alma."</p>
            <span class="small text-secondary d-block text-end">- Dante Alighieri</span>
=======
            <h1 class="display-5 fw-bold mb-2"><i class="fa-solid fa-book-open"></i> Poesía</h1>
            <p class="fst-italic fs-5 mb-1">"Todo lo que se puede imaginar es real, si tienes el valor de perseguirlo con la mirada del alma."</p>
            <span class="small d-block text-end opacity-75">- Dante Alighieri</span>
>>>>>>> 3fed72177666439cd0117aca13d22444839e2648
        </div>
        
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 py-2 rounded-end" placeholder="Buscar">
                </div>
            </div>
        </div>

        <div class="row">
            <?php while ($obra = $resultado->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm border-1 text-center p-2" style="border-radius: 12px;">
                        
                        <?php if (!empty($obra['imagen'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($obra['imagen']); ?>" class="card-img-top border rounded" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center border rounded" style="height: 200px; background-color: #f3eade; color: #555; font-weight: bold;">Foto</div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column p-2">
                            <h6 class="card-title fw-normal mb-1 text-truncate"><?php echo htmlspecialchars($obra['titulo']); ?></h6>
                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($obra['autor']); ?></p>
                            
                            <div class="mt-auto">
                                <a href="detalle.php?id=<?php echo $obra['id']; ?>" class="btn w-100 fw-bold py-1" style="background-color: #e8b4b8; color: #4a3b32; border-radius: 20px; font-size: 0.9rem;">
                                    Más información
                                </a>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <?php if ($usuario_actual > 0): ?>
                                    <a href="like.php?id=<?php echo $obra['id']; ?>" class="text-decoration-none <?php echo $obra['dio_like'] > 0 ? 'text-danger' : 'text-muted'; ?>" style="font-size: 0.85rem;">
                                        <i class="fa-<?php echo $obra['dio_like'] > 0 ? 'solid' : 'regular'; ?> fa-heart"></i> <?php echo $obra['total_likes']; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.85rem;">
                                        <i class="fa-regular fa-heart"></i> <?php echo $obra['total_likes']; ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ($obra['usuario_id'] == $usuario_actual && $usuario_actual > 0): ?>
                                    <div>
                                        <a href="editar.php?id=<?php echo $obra['id']; ?>" class="text-warning me-2"><i class="fa-solid fa-pen"></i></a>
                                        <a href="eliminar-poesia.php?id=<?php echo $obra['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar tu poema?');" class="text-danger"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php if ($usuario_actual > 0): ?>
        <a href="publicar.php" class="d-flex align-items-center justify-content-center shadow-sm text-dark btn-flotante-pildora" 
           style="position: fixed; 
                  bottom: 25px; 
                  right: 20px; 
                  width: 110px; 
                  height: 42px; 
                  background-color: #fca1f3; 
                  border-radius: 50px; 
                  text-decoration: none; 
                  z-index: 1000;">
            <i class="fa-regular fa-circle-plus" style="font-size: 1.8rem;"></i>
        </a>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>