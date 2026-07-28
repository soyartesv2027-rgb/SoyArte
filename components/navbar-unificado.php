<?php

$seccion = $seccion ?? 'general';

$colores = [
    'poesia'       => '#ec7b8b',
    'pinturas'     => '#64a0db',
    'musica'       => '#2c4e7e',
    'manualidades' => '#f8bbb8',

];

$colorNav = $colores[$seccion] ?? $colores['general'];


$acentos = [
    'poesia'       => ['bg' => '#fff0f6', 'color' => '#e91e8c'],
    'pinturas'     => ['bg' => '#fff5ee', 'color' => '#e8721a'],
    'musica'       => ['bg' => '#f0f4ff', 'color' => '#2c4e7e'],
    'manualidades' => ['bg' => '#fff5f5', 'color' => '#c0392b'],
    'general'      => ['bg' => '#f0f6ff', 'color' => '#7aaac8'],
];

$acento = $acentos[$seccion] ?? $acentos['general'];


$colorTexto = ($seccion === 'musica')
    ? '#ffffff'
    : '#333333';


$todosLosModulos = [

    'pinturas' => [
        'href'  => 'Pantalla-de-carga/PC-pintura.html',
        'icon'  => 'fa-paintbrush',
        'color' => '#c7e3ff',
        'title' => 'Pinturas'
    ],

    'musica' => [
        'href'  => 'Pantalla-de-carga/PC-musica.html',
        'icon'  => 'fa-music',
        'color' => '#2c4e7e',
        'title' => 'Música'
    ],

    'poesia' => [
        'href'  => 'Pantalla-de-carga/PC-poesia.html',
        'icon'  => 'fa-feather-pointed',
        'color' => '#fd8b8b',
        'title' => 'Poesía'
    ],

    'manualidades' => [
        'href'  => 'Pantalla-de-carga/PC-manualidades.html',
        'icon'  => 'fa-cube',
        'color' => '#f8bbb8',
        'title' => 'Manualidades'
    ],

    'realidad' => [
        'href'  => 'Pantalla-de-carga/PC-realidad.html',
        'icon'  => 'fa-vr-cardboard',
        'color' => '#b3d9ff',
        'title' => 'Realidad Virtual'
    ],

];


$iconosNav = array_filter(
    $todosLosModulos,
    fn($k) => $k !== $seccion,
    ARRAY_FILTER_USE_KEY
);

$iconosNav = array_slice($iconosNav, 0, 4);

?>


<link rel="stylesheet" href="styles/navbar-unificado.css">


<nav
    class="nb"
    style="background-color: <?= $colorNav ?>;">


    <!-- LADO IZQUIERDO -->

    <div class="nb-left">


        <!-- CASITA -->

        <a
            href="index.php"
            class="nb-home"
            style="color: <?= $colorTexto ?>;"
            title="Inicio">

            <i class="fa-solid fa-house"></i>

        </a>



        <!-- LOGO -->

        <a
            href="index.php"
            class="nb-logo"
            title="SoyArte">

            <img
                src="images/logonegro.png"
                alt="SoyArte">


        </a>


    </div>


    <!-- LADO DERECHO -->

    <div class="nb-right">


        <!-- ICONOS DE LAS SECCIONES -->

        <?php foreach ($iconosNav as $mod): ?>

            <a
                href="<?= $mod['href'] ?>"
                class="nb-icon"
                title="<?= $mod['title'] ?>"
                style="
                    background-color: <?= $mod['color'] ?>;
                    color: <?= $colorTexto ?>;
                ">

                <i class="fa-solid <?= $mod['icon'] ?>"></i>

            </a>

        <?php endforeach; ?>


        <!-- TRES PUNTITOS -->

        <div class="nb-more">


            <button
                type="button"
                id="nbMoreBtn"
                class="nb-more-btn"
                style="
                    color: <?= $colorTexto ?>;
                    background: rgba(0,0,0,0.15);
                "
                title="Explorar más">

                <i class="fa-solid fa-ellipsis-vertical"></i>

            </button>


            <!-- MENÚ EXPLORAR MÁS -->

            <div
                id="nbDropdown"
                class="nb-dropdown">


                <!-- TÍTULO -->

                <div
                    class="nb-dd-top"
                    style="
                        color: <?= $acento['color'] ?>;
                        background: <?= $acento['bg'] ?>;
                    ">

                    Explorar más

                </div>


                <?php if (isset($_SESSION['usuario_id'])): ?>


                    <!-- PERFIL -->

                    <a
                        class="nb-dd-item"
                        href="perfil.php">

                        <div
                            class="nb-dd-icon"
                            style="
                                background:#f3e5f5;
                                color:#7b1fa2;
                            ">

                            <i class="fa-solid fa-id-card"></i>

                        </div>


                        <div class="nb-dd-text">

                            <span>

                                <?= htmlspecialchars($_SESSION['nombre']) ?>

                            </span>


                            <span class="nb-dd-sub">

                                Ver mi perfil

                            </span>

                        </div>

                    </a>


                    <!-- MENSAJES -->

                    <a
                        class="nb-dd-item"
                        href="mensajes.php">

                        <div
                            class="nb-dd-icon"
                            style="
                                background:#e8f4ff;
                                color:#1565c0;
                            ">

                            <i class="fa-solid fa-message"></i>

                        </div>


                        <div class="nb-dd-text">

                            <span>

                                Mensajes

                            </span>


                            <span class="nb-dd-sub">

                                Ver conversaciones

                            </span>

                        </div>

                    </a>


                <?php endif; ?>


                <!-- TIENDA -->

                <a
                    class="nb-dd-item"
                    href="Pantalla-de-carga/PC-shop.html">

                    <div
                        class="nb-dd-icon"
                        style="
                            background:#fff8e1;
                            color:#e65100;
                        ">

                        <i class="fa-solid fa-bag-shopping"></i>

                    </div>


                    <div class="nb-dd-text">

                        <span>

                            Tienda

                        </span>


                        <span class="nb-dd-sub">

                            Arte y productos únicos

                        </span>

                    </div>

                </a>


                <!-- COMUNIDAD -->

                <a
                    class="nb-dd-item"
                    href="Pantalla-de-carga/PC-comunidad.html">

                    <div
                        class="nb-dd-icon"
                        style="
                            background:#e8f5e9;
                            color:#1b5e20;
                        ">

                        <i class="fa-solid fa-users"></i>

                    </div>


                    <div class="nb-dd-text">

                        <span>

                            Comunidad

                        </span>


                        <span class="nb-dd-sub">

                            Conecta con artistas

                        </span>

                    </div>

                </a>


                <?php if (isset($_SESSION['usuario_id'])): ?>


                    <!-- CERRAR SESIÓN -->

                    <a
                        class="nb-dd-item"
                        href="php/logout.php">

                        <div
                            class="nb-dd-icon"
                            style="
                                background:#fdecea;
                                color:#c62828;
                            ">

                            <i class="fa-solid fa-right-from-bracket"></i>

                        </div>


                        <div class="nb-dd-text">

                            <span style="color:#c62828;">

                                Cerrar sesión

                            </span>


                            <span class="nb-dd-sub">

                                Salir de tu cuenta

                            </span>

                        </div>

                    </a>


                <?php else: ?>


                    <!-- INICIAR SESIÓN -->

                    <a
                        class="nb-dd-item"
                        href="login.html">

                        <div
                            class="nb-dd-icon"
                            style="
                                background:#f3e5f5;
                                color:#7b1fa2;
                            ">

                            <i class="fa-solid fa-right-to-bracket"></i>

                        </div>


                        <div class="nb-dd-text">

                            <span>

                                Iniciar sesión

                            </span>


                            <span class="nb-dd-sub">

                                Accede a tu cuenta

                            </span>

                        </div>

                    </a>


                <?php endif; ?>


            </div>

        </div>

    </div>

</nav>


<script>

document.addEventListener('DOMContentLoaded', function () {


    const boton = document.getElementById('nbMoreBtn');

    const menu = document.getElementById('nbDropdown');


    if (!boton || !menu) {

        console.error('No se encontró el botón o el menú');

        return;

    }


    boton.addEventListener('click', function (event) {

        event.stopPropagation();


        menu.classList.toggle('nb-open');

    });


    document.addEventListener('click', function (event) {


        if (

            !menu.contains(event.target) &&

            !boton.contains(event.target)

        ) {

            menu.classList.remove('nb-open');

        }


    });


});

</script>