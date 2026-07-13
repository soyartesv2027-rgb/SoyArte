<?php
session_start();
require_once __DIR__ . '/funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'vacio') $error = 'El nombre de la categoría es obligatorio.';
    if ($_GET['error'] === 'error') $error = 'Ocurrió un error al crear la categoría.';
}

$colores = ['#6c63ff', '#c084fc', '#f472b6', '#4facfe', '#34d399', '#fbbf24', '#f97316', '#ef4444', '#14b8a6', '#8b5cf6'];
$iconos_disponibles = [
    'fa-paintbrush', 'fa-palette', 'fa-image', 'fa-camera', 'fa-music',
    'fa-book', 'fa-feather-pointed', 'fa-cube', 'fa-wand-magic-sparkles',
    'fa-star', 'fa-heart', 'fa-fire', 'fa-lightbulb', 'fa-rocket',
    'fa-handshake', 'fa-circle-nodes', 'fa-comments', 'fa-question',
    'fa-bullhorn', 'fa-tag', 'fa-calendar', 'fa-users', 'fa-user-group',
    'fa-gem', 'fa-crown', 'fa-brain', 'fa-eye', 'fa-flag'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Categoría - Comunidad SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/comunidad.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("../components/navbar.php"); ?>

    <div class="foro-header">
        <h1><i class="fa-solid fa-plus"></i> Nueva Categoría</h1>
        <p>Crea un espacio de discusión para la comunidad</p>
    </div>

    <div class="foro-container">
        <div class="categoria-crear">

            <?php if ($error): ?>
                <div class="foro-alert foro-alert-error"><i class="fa-solid fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="foro-alert foro-alert-info">
                <i class="fa-solid fa-info-circle"></i>
                Las categorías pasan por revisión antes de publicarse.
            </div>

            <form class="foro-form" method="POST" action="procesos/nueva_categoria.php">
                <div class="form-group">
                    <label for="nombre">Nombre de la categoría *</label>
                    <input type="text" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Técnicas de acuarela">
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" placeholder="¿De qué trata esta categoría?"></textarea>
                </div>

                <div class="form-group">
                    <label>Ícono</label>
                    <div class="icono-selector">
                        <?php foreach ($iconos_disponibles as $ico): ?>
                            <input type="radio" name="icono" value="<?php echo $ico; ?>" id="ico_<?php echo $ico; ?>" <?php echo $ico === 'fa-comments' ? 'checked' : ''; ?>>
                            <label for="ico_<?php echo $ico; ?>"><i class="fa-solid <?php echo $ico; ?>"></i></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Color</label>
                    <div class="color-picker">
                        <?php foreach ($colores as $c): ?>
                            <input type="radio" name="color" value="<?php echo $c; ?>" id="color_<?php echo str_replace('#', '', $c); ?>" <?php echo $c === '#6c63ff' ? 'checked' : ''; ?>>
                            <label for="color_<?php echo str_replace('#', '', $c); ?>" style="background:<?php echo $c; ?>"></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="foro-btn foro-btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Enviar categoría
                </button>
            </form>
        </div>
    </div>

    <?php include("../components/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/script.js"></script>
</body>
</html>
