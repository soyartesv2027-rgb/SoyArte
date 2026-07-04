<?php
// =============================================

// =============================================
 
$seccion = $seccion ?? 'general';
 
// --- Color del navbar segun seccion ---//
$colores = [
    'poesia'       => '#f06292',
    'pinturas'     => '#e8721a',
    'musica'       => '#5b8ecf',
    'manualidades' => '#66b366',
    'general'      => '#b8cfe8',
];
$colorNav = $colores[$seccion] ?? $colores['general'];
 
// --- Logo/mascota segun seccion ---
//  Cambia cada ruta por la imagen que quieras para cada seccion
$logos = [
    'poesia'       => 'images/Arty.png',
    'pinturas'     => 'images/Arty.png',
    'musica'       => 'images/Arty.png',
    'manualidades' => 'images/Arty.png',
    'general'      => 'images/Arty.png',
];
$logoNav = $logos[$seccion] ?? $logos['general'];
 
// --- Iconos de la derecha: los otras  3 secciones ---
// Cada icono tiene su color propio y va a la pantalla de carga
$todosLosModulos = [
    'pinturas'     => [
        'href'  => 'Pantalla-de-carga/PC-pintura.html',
        'icon'  => 'fa-paintbrush',
        'color' => '#e8721a',
        'title' => 'Pinturas',
        'clase' => 'icono-naranja',
    ],
    'musica'       => [
        'href'  => 'Pantalla-de-carga/PC-musica.html',
        'icon'  => 'fa-music',
        'color' => '#5b8ecf',
        'title' => 'Música',
        'clase' => 'icono-azul',
    ],
    'poesia'       => [
        'href'  => 'Pantalla-de-carga/PC-poesia.html',
        'icon'  => 'fa-feather-pointed',
        'color' => '#f06292',
        'title' => 'Poesía',
        'clase' => 'icono-rosa',
    ],
    'manualidades' => [
        'href'  => 'Pantalla-de-carga/PC-manualidades.html',
        'icon'  => 'fa-cube',
        'color' => '#66b366',
        'title' => 'Manualidades',
        'clase' => 'icono-verde',
    ],
];
 
// Quitar la seccion actual y tomar los primeros 3
$iconosNav = array_filter($todosLosModulos, fn($k) => $k !== $seccion, ARRAY_FILTER_USE_KEY);
$iconosNav = array_slice($iconosNav, 0, 3);
?>
<nav class="navbar-soyarte" style="background-color: <?= $colorNav ?>;">
 
    <a href="index.php" class="navbar-soyarte-logo">
        <img src="<?= $logoNav ?>" alt="Logo" width="38" height="38" style="object-fit:contain;">
        Soy<span>Arte</span>
    </a>
 
    <div class="navbar-soyarte-iconos">
        <?php foreach ($iconosNav as $mod): ?>
            <a href="<?= $mod['href'] ?>"
               class="icono-nav-global <?= $mod['clase'] ?>"
               title="<?= $mod['title'] ?>"
               style="background-color: <?= $mod['color'] ?>;">
                <i class="fa-solid <?= $mod['icon'] ?>"></i>
            </a>
        <?php endforeach; ?>
    </div>
 
</nav>