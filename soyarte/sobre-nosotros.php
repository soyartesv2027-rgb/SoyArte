<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros - SoyArte</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">

    <!-- Estilos globales -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

    <!-- Estilos específicos -->
    <link rel="stylesheet" href="styles/sobre-nosotros.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("components/navbar.php"); ?>
    
    <section id="heroCarrusel" class="carousel slide carousel-fade hero-carrusel" data-bs-ride="carousel" data-bs-interval="6000">
        <div class="carousel-indicators hero-indicadores">
            <button type="button" data-bs-target="#heroCarrusel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarrusel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarrusel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide-cine slide-creatividad">
                    <div class="overlay-dark"></div>
                    <div class="container">
                        <div class="hero-slide-cine-content">
                            <div class="hero-texto">
                                <span class="promo-tag">Bienvenido a SoyArte</span>
                                <h1>Descubre el <span>talento salvadoreño</span></h1>
                                <p>
                                    SoyArte nace para dar visibilidad a miles de jóvenes artistas
                                    que desean compartir su creatividad con el mundo. Aquí cada obra
                                    cuenta una historia y cada artista tiene una oportunidad para crecer.
                                </p>
                            </div>
                            <div class="img-carousel">
                                <img src="images/sobrenosotros1.png" alt="Sobre nosotros">
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide-cine slide-comunidad">
                    <div class="overlay-dark"></div>
                    <div class="container">
                        <div class="hero-slide-cine-content">
                            <div class="hero-texto">
                                <span class="promo-tag">Nuestra filosofía</span>
                                <h1>Más que una plataforma,<br>una <span>comunidad</span></h1>
                                <p>
                                    Creemos que el arte une personas, inspira ideas y transforma sociedades.
                                    Por eso creamos un espacio donde artistas y amantes del arte pueden
                                    comunicarse de forma sencilla.
                                </p>
                            </div>    

                            <div class="img-carousel">
                                <img src="images/sobrenosotros2.png" alt="Nuestra filosofía">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide-cine slide-futuro">
                    <div class="overlay-dark"></div>
                    <div class="container">
                        <div class="hero-slide-cine-content">
                            <div class="hero-texto">
                                <span class="promo-tag">Únete al movimiento</span>
                                <h1>El futuro del arte<br>comienza <span>aquí</span></h1>
                                <p>
                                    Nuestro compromiso es impulsar el talento salvadoreño mediante
                                    herramientas digitales que permitan exhibir, promocionar y
                                    comercializar obras de manera profesional.
                                </p>
                                <a href="register.html" class="promo-button">
                                    Únete a SoyArte <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>    

                            <div class="img-carousel">
                                <img src="images/sobrenosotros3.png" alt="Únete al movimiento">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Controles -->
        <button class="carousel-control-prev hero-control" type="button" data-bs-target="#heroCarrusel" data-bs-slide="prev">
            <i class="fa-solid fa-chevron-left"></i>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next hero-control" type="button" data-bs-target="#heroCarrusel" data-bs-slide="next">
            <i class="fa-solid fa-chevron-right"></i>
            <span class="visually-hidden">Siguiente</span>
        </button>

    </section>

    <!-- ============================================= -->
    <!-- SECCIÓN: NUESTROS VALORES                     -->
    <!-- ============================================= -->

    <section class="valores-section sn-section">
        <div class="container">

            <div class="valores-header" data-aos="fade-up">
                <h2>Nuestros Valores</h2>
                <p class="valores-sub">
                    En SoyArte creemos que el arte transforma vidas y conecta personas.
                    Nuestros valores guían cada decisión que tomamos para apoyar a los
                    jóvenes artistas salvadoreños.
                </p>
            </div>

            <div class="row g-4">

                <!-- Valor 1 - Creatividad -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="valor-card">
                        <div class="valor-icono">
                            <i class="fa-solid fa-palette"></i>
                        </div>
                        <h3>Creatividad</h3>
                        <p>Impulsamos la imaginación y celebramos la originalidad de cada artista.</p>
                    </div>
                </div>

                <!-- Valor 2 - Comunidad -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="valor-card">
                        <div class="valor-icono">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h3>Comunidad</h3>
                        <p>Construimos un espacio donde artistas y visitantes pueden crecer juntos.</p>
                    </div>
                </div>

                <!-- Valor 3 - Compromiso -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="valor-card">
                        <div class="valor-icono">
                            <i class="fa-solid fa-handshake"></i>
                        </div>
                        <h3>Compromiso</h3>
                        <p>Trabajamos para brindar oportunidades reales a los jóvenes talentos.</p>
                    </div>
                </div>

                <!-- Valor 4 - Impacto -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="valor-card">
                        <div class="valor-icono">
                            <i class="fa-solid fa-earth-americas"></i>
                        </div>
                        <h3>Impacto</h3>
                        <p>Queremos que el arte salvadoreño sea conocido dentro y fuera del país.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================= -->
    <!-- SECCIÓN: ¿CÓMO AYUDA SOYARTE?                 -->
    <!-- ============================================= -->

    <section class="ayuda-section sn-section">
        <div class="container">

            <div class="ayuda-header" data-aos="fade-up">
                <h2>¿Cómo ayuda SoyArte a los jóvenes artistas?</h2>
                <hr class="ayuda-divider">
            </div>

            <div class="row g-5 ayuda-camino justify-content-center position-relative">

                <!-- Tarjeta izquierda - Para los artistas -->
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="ayuda-card text-center text-lg-start">
                        <div class="ayuda-card-icono mx-auto mx-lg-0">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <h3>Para los artistas</h3>
                        <p>
                            En SoyArte los jóvenes artistas pueden crear un perfil profesional,
                            publicar sus pinturas, poemas, música y manualidades, conectar con
                            compradores, mostrar su talento y formar parte de una comunidad que
                            valora el arte salvadoreño.
                        </p>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <a href="perfil.php" class="ayuda-btn ayuda-btn-primary">
                                Comenzar ahora <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        <?php else: ?>
                            <a href="register.html" class="ayuda-btn ayuda-btn-primary">
                                Comenzar ahora <i class="fa-solid fa-arrow-right ms-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Conector decorativo -->
                <div class="conector-arte" data-aos="zoom-in" data-aos-delay="300">
                    <div class="conector-linea"></div>
                    <div class="conector-circulo">
                        <i class="fa-solid fa-paintbrush"></i>
                    </div>
                </div>

                <!-- Tarjeta derecha - Para los visitantes -->
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="ayuda-card text-center text-lg-start">
                        <div class="ayuda-card-icono mx-auto mx-lg-0">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <h3>Para los visitantes</h3>
                        <p>
                            Los visitantes pueden descubrir nuevos talentos, explorar obras
                            originales, contactar directamente con los artistas, apoyar el arte
                            nacional y adquirir piezas únicas creadas en El Salvador.
                        </p>
                        <a href="tienda.php" class="ayuda-btn ayuda-btn-outline">
                            Explorar obras <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include("components/footer.php"); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Script global -->
    <script src="JavaScript/script.js"></script>

    <!-- Script específico -->
    <script src="JavaScript/sobre-nosotros.js?v=<?php echo time(); ?>"></script>

</body>
</html>
