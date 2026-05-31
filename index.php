<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soy Arte 🖌️🎨</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include("components/navbar.php"); ?>
  
  <!-- El resto de tu index queda exactamente igual -->
  <div class="header text-center my-4">
    <img src="images/Arty.png" class="arty" alt="Arty Grande">
    <div class="textos">
      <h1>Soy Arte</h1>
      <h3 class="subtitulo">El arte es la ventana a tu alma</h3>
    </div>
  </div>

  <div class="container mt-4">
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="images/pinturas.jpeg" class="d-block w-100" alt="Pinturas">
          <div class="carousel-caption"><h5>Pinturas</h5></div>
        </div>
        <div class="carousel-item">
          <img src="images/musica.jpeg" class="d-block w-100" alt="Música">
          <div src="musica.php" class="carousel-caption"><h5>Música</h5></div>
        </div>
        <div class="carousel-item">
          <img src="images/Poemas.jpeg" class="d-block w-100" alt="Poemas">
          <div class="carousel-caption"><h5>Poemas</h5></div>
        </div>
        <div class="carousel-item">
          <img src="images/manualidades.jpeg" class="d-block w-100" alt="Manualidades">
          <div class="carousel-caption"><h5>Manualidades</h5></div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
      </button>
    </div>
  </div>

    <div class="info-soyarte">

    <div class="info-header">

        <span class="info-badge">
            Plataforma creativa
        </span>

        <h1>
            ¿Qué es Soy Arte?
        </h1>

        <p class="info-subtitle">
            Un espacio donde la creatividad,
            la tecnología y la imaginación se conectan.
        </p>

    </div>

    <div class="contenido">

        <!-- TEXTO -->

        <div class="texto-info">

            <div class="glass-card">

                <div class="icono-art">
                    <i class="fa-solid fa-palette"></i>
                </div>

                <h2>
                    Inspira. Crea. Comparte.
                </h2>

                <p>
                    Soy Arte es una plataforma diseñada para artistas,
                    creadores y soñadores que desean expresar sus ideas
                    a través de la música, pintura, poesía y arte digital.
                </p>

                <p>
                    Explora experiencias inmersivas, comparte tus obras
                    y conecta con una comunidad apasionada por la creatividad.
                </p>

            </div>

        </div>

        <!-- IMAGEN -->

        <div class="imagen-box">

            <img class="fondo" src="images/fondo.jpg" alt="Arte">

            <div class="floating-card">

                <i class="fa-solid fa-vr-cardboard"></i>

                <span>
                    Experiencias VR
                </span>

            </div>

        </div>

    </div>

    <!-- REALIDAD VIRTUAL -->

    <div class="realidad-container">

        <div class="video-box">

            <video
                src="videos/realidad-virtual.mp4"
                controls
                autoplay
                muted>
            </video>

        </div>

        <div class="content-box">

            <span class="vr-badge">
                Experiencia inmersiva
            </span>

            <h2>
                Descubre nuestro museo virtual
            </h2>

            <p>
                Vive una experiencia artística interactiva
                y explora galerías digitales como si estuvieras
                dentro de un museo real.
            </p>

            <a href="#" class="btn-realidad">
                Entrar al museo
            </a>

        </div>

    </div>

</div>

  </div>
  </div>
  </div>

  <hr>
  
  <section class="arte-section">

    <div class="arte-bg"></div>

    <div class="container">

        <!-- TITULO -->

        <div class="arte-header">

            <span class="arte-badge">
                Galería artística
            </span>

            <h1>
                Conoce más sobre el arte
            </h1>

            <p>
                Descubre expresiones visuales, creatividad
                y experiencias que inspiran emociones.
            </p>

        </div>

        <!-- GALERIA -->

        <div id="carouselArte"
            class="carousel slide carousel-fade"
            data-bs-ride="carousel">

            <div class="carousel-inner">

                <div class="carousel-item active">

                    <div class="arte-card">

                        <img
                            src="images/img-info.jpeg"
                            class="carousel-img"
                            alt="Arte">

                        <div class="arte-overlay">

                            <h2>
                                Arte Visual
                            </h2>

                            <p>
                                Explora obras llenas de color,
                                creatividad y expresión.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="carousel-item">

                    <div class="arte-card">

                        <img
                            src="images/img-info2.jpeg"
                            class="carousel-img"
                            alt="Arte">

                        <div class="arte-overlay">

                            <h2>
                                Inspiración Creativa
                            </h2>

                            <p>
                                Descubre ideas que conectan
                                emociones y arte moderno.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="carousel-item">

                    <div class="arte-card">

                        <img
                            src="images/img-info3.jpeg"
                            class="carousel-img"
                            alt="Arte">

                        <div class="arte-overlay">

                            <h2>
                                Expresión Artística
                            </h2>

                            <p>
                                Un espacio donde la imaginación
                                cobra vida.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="carousel-item">

                    <div class="arte-card">

                        <img
                            src="images/img-info4.jpeg"
                            class="carousel-img"
                            alt="Arte">

                        <div class="arte-overlay">

                            <h2>
                                Creatividad Digital
                            </h2>

                            <p>
                                Arte y tecnología unidos
                                en una experiencia inmersiva.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

            <!-- BOTONES -->

            <button class="carousel-control-prev"
                type="button"
                data-bs-target="#carouselArte"
                data-bs-slide="prev">

                <span class="carousel-control-prev-icon"></span>

            </button>

            <button class="carousel-control-next"
                type="button"
                data-bs-target="#carouselArte"
                data-bs-slide="next">

                <span class="carousel-control-next-icon"></span>

            </button>

        </div>

    </div>

</section>

<!-- ========================================= -->
<!-- PROMO TIENDA CINEMATICA -->
<!-- ========================================= -->

<section class="promo-cinematic">

    <div class="overlay-dark"></div>

    <div class="container">

        <div class="promo-wrapper">

            <!-- TEXTO -->

            <div class="promo-left">

                <span class="promo-tag">
                    TIENDA ARTÍSTICA
                </span>

                <h1>
                     explora
                    nuestra <span>tienda de arte</span>
                </h1>

                <p>
                    Descubre productos artísticos, herramientas creativas,
                    obras exclusivas y artículos diseñados
                    para inspirar a cada artista.
                </p>

                <a href="tienda.php" class="promo-button">

                    Explorar tienda

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>

        </div>

    </div>

</section>

<section class="py-5" id="misionVision">

    <div class="container">

        <!-- TITULO -->
        <div class="text-center mb-5">
            <h2 class="fw-bold display-5">
                Nuestra Misión y Visión
            </h2>

            <p class="text-muted">
                Impulsando el talento artístico juvenil de El Salvador
            </p>
        </div>

        <!-- CONTENIDO -->
        <div class="row g-4 justify-content-center">

            <!-- MISION -->
            <div class="col-md-6">

                <div class="card border-0 shadow-lg h-100 p-4 rounded-4 card-hover">

                    <div class="icon-box mb-4">
                        <i class="fa-solid fa-paintbrush"></i>
                    </div>

                    <h3 class="fw-bold mb-3">
                        Misión
                    </h3>

                    <p class="text-muted">
                        Impulsar, apoyar y visibilizar el talento artístico
                        de jóvenes salvadoreños mediante una plataforma
                        digital inclusiva donde puedan compartir y difundir
                        sus obras, como pintura, música, poesía y otras
                        expresiones creativas, fomentando el arte,
                        la cultura y nuevas oportunidades de crecimiento.
                    </p>

                </div>
            </div>

            <!-- VISION -->
            <div class="col-md-6">

                <div class="card border-0 shadow-lg h-100 p-4 rounded-4 card-hover">

                    <div class="icon-box mb-4">
                        <i class="fa-regular fa-lightbulb"></i>
                    </div>

                    <h3 class="fw-bold mb-3">
                        Visión
                    </h3>

                    <p class="text-muted">
                        Ser la principal plataforma digital de arte juvenil
                        en El Salvador, reconocida por proyectar el talento
                        de jóvenes artistas a nivel nacional e internacional,
                        creando una comunidad creativa que inspire,
                        conecte y transforme la cultura a través del arte.
                    </p>

                </div>
            </div>

        </div>
    </div>

</section>

     
  <?php include("components/footer.php"); ?>
  

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener("scroll", () => {
      const section = document.querySelector(".info-soyarte");
      if (section) {
        const position = section.getBoundingClientRect().top;
        const screen = window.innerHeight;
        if (position < screen - 100) {
          section.classList.add("visible");
        }
      }
    });
  </script>
  <script src="JavaScrip/script.js"></script>
</body>
</html>