<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

// ===== Estadísticas =====

// Denuncias pendientes
$sql = "SELECT COUNT(*) AS total FROM denuncias WHERE estado = 'pendiente'";
$denunciasPendientes = (int)$conn->query($sql)->fetch_assoc()['total'];

// Contenidos ocultos (suma de las 4 categorías)
$ocultos = 0;
foreach (['pinturas', 'musica', 'obras', 'manualidades'] as $tabla) {
    $ocultos += (int)$conn->query("SELECT COUNT(*) AS total FROM `$tabla` WHERE estado = 'oculta'")->fetch_assoc()['total'];
}

// Usuarios sancionados
$sql = "SELECT COUNT(*) AS total FROM usuarios WHERE estado IN ('suspendido', 'eliminado')";
$usuariosSancionados = (int)$conn->query($sql)->fetch_assoc()['total'];

// Publicaciones totales
$publicacionesTotales = 0;
foreach (['pinturas', 'musica', 'obras', 'manualidades'] as $tabla) {
    $publicacionesTotales += (int)$conn->query("SELECT COUNT(*) AS total FROM `$tabla`")->fetch_assoc()['total'];
}

// Denuncias por categoría
$denunciasPorTipo = ['pintura' => 0, 'musica' => 0, 'poesia' => 0, 'manualidad' => 0];
$resTipos = $conn->query("SELECT tipo_contenido, COUNT(*) AS total FROM denuncias GROUP BY tipo_contenido");
while ($f = $resTipos->fetch_assoc()) {
    $denunciasPorTipo[$f['tipo_contenido']] = (int)$f['total'];
}

// Actividad reciente
$sql = "SELECT h.*, ua.nombre AS admin_nombre, uu.nombre AS usuario_nombre
        FROM moderacion_historial h
        LEFT JOIN usuarios ua ON ua.id = h.admin_id
        LEFT JOIN usuarios uu ON uu.id = h.usuario_id
        ORDER BY h.fecha DESC
        LIMIT 8";
$actividadReciente = $conn->query($sql);

$tipos = mod_tipos();
$nombresCategoria = [
    'pintura'    => ['Pinturas', '#64a0db'],
    'musica'     => ['Música', '#2c4e7e'],
    'poesia'     => ['Poesía', '#ec7b8b'],
    'manualidad' => ['Manualidades', '#f8bbb8'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderación - SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/moderacion.css">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

<?php $seccion = 'general'; include("components/navbar-unificado.php"); ?>

<main class="mod-contenido">

    <h1 class="mod-titulo-seccion">
        <i class="fa-solid fa-shield-halved"></i> Moderación
    </h1>
    <p class="mod-subtitulo">Panel administrativo de SoyArte</p>

    <div class="mod-encabezado-acciones">
        <a href="moderacion_denuncias.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-flag"></i> Todas las denuncias
        </a>
        <a href="moderacion_usuarios.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-users-slash"></i> Usuarios sancionados
        </a>
        <a href="moderacion_historial.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-clock-rotate-left"></i> Historial
        </a>
    </div>

    <!-- ESTADÍSTICAS GENERALES -->
    <div class="mod-stats">

        <div class="mod-stat">
            <div class="mod-stat-icono" style="background:#fdecea; color:#c62828;">
                <i class="fa-solid fa-flag"></i>
            </div>
            <div>
                <div class="mod-stat-numero"><?= $denunciasPendientes ?></div>
                <div class="mod-stat-texto">Denuncias pendientes</div>
            </div>
        </div>

        <div class="mod-stat">
            <div class="mod-stat-icono" style="background:#fff8e1; color:#e65100;">
                <i class="fa-solid fa-eye-slash"></i>
            </div>
            <div>
                <div class="mod-stat-numero"><?= $ocultos ?></div>
                <div class="mod-stat-texto">Contenidos ocultos</div>
            </div>
        </div>

        <div class="mod-stat">
            <div class="mod-stat-icono" style="background:#f3e5f5; color:#7b1fa2;">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <div>
                <div class="mod-stat-numero"><?= $usuariosSancionados ?></div>
                <div class="mod-stat-texto">Usuarios sancionados</div>
            </div>
        </div>

        <div class="mod-stat">
            <div class="mod-stat-icono" style="background:#e8f5e9; color:#2e7d32;">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <div>
                <div class="mod-stat-numero"><?= $publicacionesTotales ?></div>
                <div class="mod-stat-texto">Publicaciones totales</div>
            </div>
        </div>

    </div>

    <!-- CATEGORÍAS -->
    <div class="mod-categorias">

        <?php foreach ($nombresCategoria as $tipo => $info): ?>
        <a class="mod-categoria" style="background:linear-gradient(135deg, <?= $info[1] ?>, <?= $info[1] ?>cc);" href="moderacion_categoria.php?tipo=<?= $tipo ?>">
            <span class="mod-cat-icono"><i class="fa-solid <?= $tipos[$tipo]['icono'] ?>"></i></span>
            <div class="mod-cat-titulo"><?= $info[0] ?></div>
            <div class="mod-cat-denuncias">
                🚨 <?= $denunciasPorTipo[$tipo] ?> denuncias
            </div>
            <div class="mod-cat-acciones">
                <span>
                    <i class="fa-solid fa-flag"></i> <?= $tipos[$tipo]['plural'] ?> denunciadas
                </span>
                <span>
                    <i class="fa-solid fa-table-list"></i> Todas las <?= strtolower($tipos[$tipo]['plural']) ?>
                </span>
            </div>
        </a>
        <?php endforeach; ?>

    </div>

    <!-- ACTIVIDAD RECIENTE -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo">
            <i class="fa-solid fa-bell"></i> Actividad reciente
        </div>

        <?php if ($actividadReciente->num_rows > 0): ?>

            <?php while ($a = $actividadReciente->fetch_assoc()): ?>
            <div class="mod-actividad">
                <div class="mod-actividad-icono"><?= mod_icono_accion($a['accion']) ?></div>
                <div>
                    <div class="mod-actividad-texto">
                        <strong><?= htmlspecialchars($a['admin_nombre'] ?? 'Admin') ?></strong>
                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $a['accion']))) ?>
                        <?php if (!empty($a['tipo_contenido']) && !empty($a['id_contenido'])): ?>
                            en <?= strtolower($tipos[$a['tipo_contenido']]['etiqueta']) ?> #<?= (int)$a['id_contenido'] ?>
                        <?php endif; ?>
                        <?php if (!empty($a['usuario_nombre'])): ?>
                            — @<?= htmlspecialchars($a['usuario_nombre']) ?>
                        <?php endif; ?>
                        <?php if (!empty($a['motivo'])): ?>
                            <span class="mod-etiqueta"><?= htmlspecialchars($a['motivo']) ?></span>
                        <?php endif; ?>
                        <?php if ($a['correo_enviado']): ?>
                            <span class="mod-etiqueta">📧 Correo enviado</span>
                        <?php endif; ?>
                    </div>
                    <div class="mod-actividad-fecha">
                        <?= date('d/m/Y H:i', strtotime($a['fecha'])) ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

        <?php else: ?>

            <p class="text-muted mb-0">Aún no hay actividad de moderación.</p>

        <?php endif; ?>

    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>