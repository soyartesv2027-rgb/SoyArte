<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$tipo = isset($_GET['tipo']) ? mod_tipo_valido($_GET['tipo']) : null;

if (!$tipo) {
    header('Location: moderacion.php');
    exit;
}

$tipos = mod_tipos();
$cfg = $tipos[$tipo];

$coloresTipo = [
    'pintura'    => '#64a0db',
    'musica'     => '#2c4e7e',
    'poesia'     => '#ec7b8b',
    'manualidad' => '#f8bbb8',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $cfg['plural'] ?> - Moderación SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/moderacion.css">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

<?php $seccion = 'general'; include("components/navbar-unificado.php"); ?>

<main class="mod-contenido">

    <div class="mod-encabezado-acciones">
        <a href="moderacion.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-arrow-left"></i> Volver a Moderación
        </a>
    </div>

    <div class="mod-categoria" style="background:linear-gradient(135deg, <?= $coloresTipo[$tipo] ?>, <?= $coloresTipo[$tipo] ?>cc); margin-bottom:24px;">
        <span class="mod-cat-icono"><i class="fa-solid <?= $cfg['icono'] ?>"></i></span>
        <div class="mod-cat-titulo" style="font-size:1.6rem;"><?= $cfg['plural'] ?></div>
    </div>

    <div class="mod-categorias">
        <a class="mod-cat-acciones mod-bloque" href="moderacion_denuncias.php?tipo=<?= $tipo ?>" style="text-decoration:none;">
            <div class="mod-bloque-titulo" style="font-size:1.15rem;">
                <i class="fa-solid fa-flag"></i> <?= $cfg['plural'] ?> denunciadas
            </div>
            <p style="color:var(--sa-muted); margin:0; font-size:0.9rem;">
                Ver solo contenido que haya recibido denuncias.
            </p>
        </a>
        <a class="mod-cat-acciones mod-bloque" href="moderacion_todas.php?tipo=<?= $tipo ?>" style="text-decoration:none;">
            <div class="mod-bloque-titulo" style="font-size:1.15rem;">
                <i class="fa-solid fa-table-list"></i> Todas las <?= strtolower($cfg['plural']) ?>
            </div>
            <p style="color:var(--sa-muted); margin:0; font-size:0.9rem;">
                Ver todo el contenido publicado de esta categoría.
            </p>
        </a>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>