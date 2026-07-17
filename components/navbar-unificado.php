<?php
$seccion = $seccion ?? 'general';
 
$colores = [
    'poesia'       => '#ec7b8b',
    'pinturas'     => '#c7e3ff',
    'musica'       => '#2c4e7e',
    'manualidades' => '#f8bbb8',
    'general'      => '#b8cfe8',
];
$colorNav = $colores[$seccion] ?? $colores['general'];
 
$logos = [
    'poesia'       => 'images/Logo-Blanco.png',
    'pinturas'     => 'images/Logo-Blanco.png',
    'musica'       => 'images/Logo-Blanco.png',
    'manualidades' => 'images/Logo-Blanco.png',
    'general'      => 'images/Logo-Blanco.png',
];
$logoNav = $logos[$seccion] ?? $logos['general'];
 
$todosLosModulos = [
    'pinturas'     => ['href' => 'Pantalla-de-carga/PC-pintura.html',      'icon' => 'fa-paintbrush',      'color' => '#c7e3ff', 'title' => 'Pinturas',     'clase' => 'icono-naranja'],
    'musica'       => ['href' => 'Pantalla-de-carga/PC-musica.html',       'icon' => 'fa-music',           'color' => '#2c4e7e', 'title' => 'Música',       'clase' => 'icono-azul'],
    'poesia'       => ['href' => 'Pantalla-de-carga/PC-poesia.html',       'icon' => 'fa-feather-pointed', 'color' => '#fd8b8b', 'title' => 'Poesía',       'clase' => 'icono-rosa'],
    'manualidades' => ['href' => 'Pantalla-de-carga/PC-manualidades.html', 'icon' => 'fa-cube',            'color' => '#f8bbb8', 'title' => 'Manualidades', 'clase' => 'icono-verde'],
];
 
$iconosNav = array_filter($todosLosModulos, fn($k) => $k !== $seccion, ARRAY_FILTER_USE_KEY);
$iconosNav = array_slice($iconosNav, 0, 3);
 
// Color del boton de home segun seccion (un poco mas oscuro que el nav)
$coloresBoton = [
    'poesia'       => 'rgba(0,0,0,0.15)',
    'pinturas'     => 'rgba(0,0,0,0.12)',
    'musica'       => 'rgba(255,255,255,0.15)',
    'manualidades' => 'rgba(0,0,0,0.12)',
    'general'      => 'rgba(0,0,0,0.12)',
];
$colorBoton = $coloresBoton[$seccion] ?? $coloresBoton['general'];
 
// Color del texto/icono segun si el nav es claro u oscuro
$colorTexto = in_array($seccion, ['musica']) ? '#ffffff' : '#333333';
?>
<link rel="stylesheet" href="styles/navbar-unificado.css">
 
<nav class="navbar-soyarte" style="min-height: 80px; background-color: <?= $colorNav ?>;">
 
    <div style="display:flex; align-items:stretch;">
 
        <!-- Boton Home -->
        <a href="index.php"
           title="Ir al inicio"
           style="display:flex; align-items:center; justify-content:center;
                  width:60px; background:<?= $colorBoton ?>;
                  color:<?= $colorTexto ?>; font-size:1.2rem;
                  text-decoration:none; transition:background 0.2s;
                  border-right: 1px solid rgba(0,0,0,0.08);">
            <i class="fa-solid fa-house"></i>
        </a>
 
        <!-- Logo -->
        <a href="index.php" class="navbar-soyarte-logo">
            <img src="<?= $logoNav ?>" alt="Logo" width="75" height="75" style="object-fit:contain;">
            Soy<span>Arte</span>
        </a>
 
    </div>
 
    <!-- Iconos de otras secciones -->
    <div class="navbar-soyarte-iconos">
        <?php foreach ($iconosNav as $mod): ?>
            <a href="<?= $mod['href'] ?>"
               class="icono-nav-global <?= $mod['clase'] ?>"
               title="<?= $mod['title'] ?>"
               style="background-color: <?= $mod['color'] ?>; color:<?= $colorTexto ?>;">
                <i class="fa-solid <?= $mod['icon'] ?>"></i>
            </a>
        <?php endforeach; ?>
    </div>
 
</nav>