<?php
session_start();
require_once 'php/conexion.php';

// Obtener los likes totales para cada canción
$sql = "SELECT m.*, 
        (SELECT COUNT(*) FROM likes_musica WHERE musica_id = m.musica_id) as total_likes 
        FROM musica m 
        WHERE m.estado = 'publicada'
        ORDER BY m.musica_id DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoyArte - Música</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="styles/musica.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php $seccion = 'musica'; include("components/navbar-unificado.php"); ?>

<section class="banner">
    <img src="images/banner.jpeg" alt="Banner Música">
    <div class="overlay">
        <h2><i class="fa-solid fa-music"></i> Música</h2>
        <p>"La música expresa lo que no puede ser dicho y aquello sobre lo que es imposible permanecer en silencio."</p>
    </div>
</section>

<div class="search-container">
    <input type="text" id="buscador" placeholder="Buscar canción o cantante...">
</div>

<main class="cards" id="lista-musica">
<?php if($resultado->num_rows > 0): ?>
    <?php while($musica = $resultado->fetch_assoc()): ?>
        <div class="card-wrapper">
            <a href="ver_musica.php?id=<?php echo $musica['musica_id']; ?>" class="card-link tarjeta-musica">
                <div class="card">
                    <div class="card-image">
                        <img src="uploads/musica/<?php echo htmlspecialchars($musica['portada']); ?>" alt="<?php echo htmlspecialchars($musica['nombre_cancion']); ?>">
                        <div class="play-overlay">
                            <i class="fa-solid fa-play"></i>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="title-row">
                            <div>
                                <h3 class="nombre-cancion"><?php echo htmlspecialchars($musica['nombre_cancion']); ?></h3>
                                <p class="nombre-cantante"><?php echo htmlspecialchars($musica['nombre_cantante']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Botón de like FUERA del enlace -->
            <div class="like-container">
                <button class="btn-like" 
                        data-id="<?php echo $musica['musica_id']; ?>" 
                        data-usuario="<?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0; ?>"
                        type="button">
                    <i class="fa-regular fa-heart"></i>
                    <span class="contador-like">
                        <?php echo $musica['total_likes'] ?? 0; ?>
                    </span>
                </button>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="sin-publicaciones">
        <h3>No hay publicaciones musicales todavía 🎵</h3>
        <p>Sé el primero en compartir una canción.</p>
    </div>
<?php endif; ?>
</main>

<?php if(isset($_SESSION['usuario_id'])): ?>
<<<<<<< HEAD
    <a href="publicar_musica.php" class="floating-btn">
        <i class="fa-solid fa-plus"></i>
    </a>
=======

   
>>>>>>> 2f25a941abc1d61216de7aefb74ed11a6998d36d
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="JavaScript/script.js"></script>
<script src="JavaScript/musica.js"></script>
</body>
</html>