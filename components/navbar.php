<?php
// =============================================
// NAVBAR - SOY ARTE
// IMPORTANTE:
// session_start() debe ir antes del include
// =============================================
?>

<!-- OVERLAY -->
<div id="overlay"></div>

<!-- SIDEBAR -->
<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3">

    <!-- TITULO -->
    <a href="index.php" class="d-flex align-items-center mb-3 text-decoration-none">
        <span class="fs-4 text-dark fw-bold">
            Soy Arte
        </span>
    </a>

    <hr>

    <!-- MENU -->
    <ul class="nav nav-pills flex-column mb-auto">

        <li class="nav-item mb-2">
            <a href="index.php" class="nav-link active">
                <i class="fa-solid fa-house me-2"></i>
                Inicio
            </a>
        </li>

        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-pintura.html" class="nav-link link-dark">
                <i class="fa-solid fa-image me-2"></i>
                Pinturas
        </li>

        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-musica.html" class="nav-link link-dark">
                <i class="fa-solid fa-music" style="color: rgb(0, 0, 0);"></i>
                Musica
            </a>
        </li>

        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-poesia.html" class="nav-link link-dark">
                <i class="fa-solid fa-feather-pointed" style="color: rgb(0, 0, 0);"></i>
                Poesia
            </a>
        </li>

        <li class="mb-2">
            <a href="manualidad.php" class="nav-link link-dark">
                <i class="fa-solid fa-cube" style="color: rgb(0, 0, 0);"></i>
                Manualidades
            </a>
        </li>
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-realidad.html" class="nav-link link-dark">
                <i class="fa-solid fa-vr-cardboard" style="color: rgb(0, 0, 0);"></i>
                Realidad Virtual
            </a>
        </li>
            <li class="mb-2">
            <a href="Pantalla-de-carga/PC-shop.html" class="nav-link link-dark">
            <i class="fa-solid fa-cart-shopping" style="color: rgb(0, 0, 0);"></i>
                Tienda
            </a>
        </li>

    </ul>

    <hr>

    <!-- USUARIO -->
    <div class="dropdown">

        <a href="#"
            class="d-flex align-items-center text-decoration-none dropdown-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false">

            <img 
                src=""
                alt="usuario"
                width="32"
                height="32"
                class="rounded-circle me-2">

            <strong class="text-dark">
                Usuario
            </strong>

        </a>

        <ul class="dropdown-menu text-small shadow">

            <li>
                <a class="dropdown-item" href="perfil.php">
                    Perfil
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="configuracion.php">
                    Configuración
                </a>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item text-danger" href="logout,php">
                    Cerrar sesión
                </a>
            </li>

        </ul>

    </div>

</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg shadow-sm" style="background-color: #b8cfe8;">

    <div class="container-fluid">

        <!-- BOTON MENU -->
        <svg
             id="menuBtn"
                class="menu-btn"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 640"
                width="30"
                height="30"
                style="cursor:pointer; flex-shrink:0;">

            <path
                fill="black"
                d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/>
        </svg>

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold"
            href="index.php">

            Soy Arte

            <img
                src="images/Arty.png"
                alt="Arty"
                width="40"
                height="40"
                style="object-fit: contain;">

        </a>

        <!-- BOTON RESPONSIVE -->
        <button class="navbar-toggler border-0"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navSoyArte"
                aria-controls="navSoyArte"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- CONTENIDO NAVBAR -->
        <div class="collapse navbar-collapse" id="navSoyArte">

 
   

            <!-- SESION -->
            <div class="ms-auto d-flex align-items-center">

                <?php if (isset($_SESSION['usuario_id'])): ?>

                    <!-- USUARIO LOGUEADO -->
                    <div class="dropdown">

                        <a class="btn btn-secondary dropdown-toggle d-flex align-items-center gap-2"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            <i class="fa-solid fa-circle-user"></i>

                            <?php echo htmlspecialchars($_SESSION['nombre']); ?>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                            <li>
                                <a class="dropdown-item" href="perfil.php">
                                    <i class="fa-solid fa-id-card me-2"></i>
                                    Perfil
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item text-danger"
                                    href="php/logout.php">

                                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                                    Cerrar sesión

                                </a>
                            </li>

                        </ul>

                    </div>

                <?php else: ?>

                    <!-- SIN SESION -->
                    <a class="btn btn-outline-primary me-2"
                        href="login.html">

                        Login

                    </a>

                    <a class="btn btn-primary"
                        href="register.html">

                        Registrarse

                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</nav>