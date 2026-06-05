<?php
session_start();
include("php/conexion.php");

$usuario_actual = $_SESSION['usuario_id'] ?? 0;

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
    <link rel="stylesheet" href="styles/poesia.css">
<<<<<<< HEAD
=======
    <link rel="stylesheet" href="style.css">
>>>>>>> 0c5faa6b462c3546a2263bd803e57347cafee744
</head>
<body>

    <?php include("components/navbar.php"); ?>

    <!-- HERO FUERA DEL CONTENEDOR (ancho completo) -->
    <div class="hero-banner">
        <h1><i class="fa-solid fa-book-open"></i> Poesía</h1>
        <p>"Todo lo que se puede imaginar es real, si tienes el valor de perseguirlo con la mirada del alma."</p>
        <div class="autor-cita">-Dante Alighieri</div>
    </div>

    <!-- CONTENEDOR: buscador y cards -->
    <div class="muro-poesia-container">

        <!-- BUSCADOR -->
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="inputBuscar" placeholder="Buscar">
        </div>

        <!-- GRID -->
        <div class="grid-poesias" id="gridPoesias">

            <?php if ($resultado->num_rows === 0): ?>
                <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#888;">
                    <i class="fa-solid fa-book-open" style="font-size:3rem;display:block;margin-bottom:12px;"></i>
                    Aún no hay poemas. ¡Sé el primero en publicar!
                </div>
            <?php endif; ?>

            <?php while ($obra = $resultado->fetch_assoc()): ?>
                <div class="card-poesia">

                    <?php if (!empty($obra['imagen'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($obra['imagen']) ?>"
                             alt="<?= htmlspecialchars($obra['titulo']) ?>">
                    <?php else: ?>
                        <div class="placeholder-foto">Foto</div>
                    <?php endif; ?>

                    <div class="card-body-custom">
                        <h6 class="card-title-custom"><?= htmlspecialchars($obra['titulo']) ?></h6>
                        <p class="card-autor-custom"><?= htmlspecialchars($obra['autor']) ?></p>

                        <a href="detalle.php?id=<?= $obra['id'] ?>" class="btn-info-custom">
                            Más información
                        </a>

                        <div class="card-footer-custom">

                            <!-- LIKES -->
                            <?php if ($usuario_actual > 0): ?>
                                <a href="like.php?id=<?= $obra['id'] ?>"
                                   class="<?= $obra['dio_like'] > 0 ? 'text-danger-custom' : 'text-muted-custom' ?>">
                                    <i class="fa-<?= $obra['dio_like'] > 0 ? 'solid' : 'regular' ?> fa-heart"></i>
                                    <?= $obra['total_likes'] ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted-custom">
                                    <i class="fa-regular fa-heart"></i> <?= $obra['total_likes'] ?>
                                </span>
                            <?php endif; ?>

                            <!-- EDITAR Y ELIMINAR: solo el dueño -->
                            <?php if ($usuario_actual > 0 && $obra['usuario_id'] == $usuario_actual): ?>
                                <div>
                                    <a href="editar.php?id=<?= $obra['id'] ?>" class="text-warning-custom" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="eliminar-poesia.php?id=<?= $obra['id'] ?>"
                                       onclick="return confirm('¿Eliminar este poema?');"
                                       class="text-danger-custom ms-2" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Botón publicar: solo usuarios con sesión -->
    <?php if ($usuario_actual > 0): ?>
        <a href="publicar.php" class="btn-flotante-pildora" title="Publicar poema">
            <i class="fa-solid fa-plus"></i>
        </a>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const menuBtn = document.getElementById('menuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        if (menuBtn) menuBtn.addEventListener('click', () => { sidebar?.classList.toggle('open'); overlay?.classList.toggle('show'); });
        if (overlay) overlay.addEventListener('click', () => { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); });

        document.getElementById('inputBuscar')?.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.card-poesia').forEach(card => {
                const titulo = card.querySelector('.card-title-custom')?.textContent.toLowerCase() ?? '';
                const autor  = card.querySelector('.card-autor-custom')?.textContent.toLowerCase() ?? '';
                card.style.display = (titulo.includes(q) || autor.includes(q)) ? '' : 'none';
            });
        });
    </script>
</body>
</html>