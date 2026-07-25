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
  <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="style.css?v=<?php echo time();?>">
</head>
<body>
  <?php include("components/navbar.php"); ?>

  <!-- ============================================================ -->
  <!-- HEADER MEJORADO                                              -->
  <!-- ============================================================ -->
  <div class="header text-center my-5">
    <div class="header-content">
      <img src="images/ChatGPT Image 1 jul 2026, 18_32_29.png" alt="Soy Arte Logo" class="header-logo">
      <div class="textos">
        <h1 class="titulo-principal">Soy <span>Arte</span></h1>
        <p class="subtitulo">El arte es la ventana a tu alma</p>
      </div>
    </div>
  </div>

  <!-- ============================================================ -->
  <!-- CARRUSEL PRINCIPAL MEJORADO                                  -->
  <!-- ============================================================ -->
  <div class="container mt-4">
    <div id="carouselExampleCaptions" class="carousel slide carousel-principal" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <a href="pinturas.php">
            <img src="images/pinturas.jpeg" class="d-block w-100" alt="Pinturas" loading="lazy">
          </a>
          <div class="carousel-caption">
            <h5>🎨 Pinturas</h5>
            <p>Explora obras llenas de color y expresión</p>
          </div>
        </div>
        <div class="carousel-item">
          <a href="musica.php">
            <img src="images/musica.jpeg" class="d-block w-100" alt="Música" loading="lazy">
          </a>
          <div class="carousel-caption">
            <h5>🎵 Música</h5>
            <p>Deja que el ritmo te inspire</p>
          </div>
        </div>
        <div class="carousel-item">
          <a href="poesia.php">
            <img src="images/Poemas.jpeg" class="d-block w-100" alt="Poesía" loading="lazy">
          </a>
          <div class="carousel-caption">
            <h5>📝 Poesía</h5>
            <p>Versos que tocan el alma</p>
          </div>
        </div>
        <div class="carousel-item">
          <a href="manualidad.php">
            <img src="images/manualidades.jpeg" class="d-block w-100" alt="Manualidades" loading="lazy">
          </a>
          <div class="carousel-caption">
            <h5>🧵 Manualidades</h5>
            <p>Crea con tus manos</p>
          </div>
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

  <!-- ============================================================ -->
  <!-- INFO SOY ARTE                                                -->
  <!-- ============================================================ -->
  <div class="info-soyarte">

    <div class="info-header">
      <span class="info-badge">Plataforma creativa</span>
      <h1>¿Qué es Soy Arte?</h1>
      <p class="info-subtitle">Un espacio donde la creatividad, la tecnología y la imaginación se conectan.</p>
    </div>

    <div class="contenido">

      <div class="texto-info">
        <div class="glass-card">
          <div class="icono-art">
            <i class="fa-solid fa-palette"></i>
          </div>
          <h2>Inspira. Crea. Comparte.</h2>
          <p>Soy Arte es una plataforma diseñada para artistas, creadores y soñadores que desean expresar sus ideas a través de la música, pintura, poesía y arte digital.</p>
          <p>Explora experiencias inmersivas, comparte tus obras y conecta con una comunidad apasionada por la creatividad.</p>
        </div>
      </div>

      <div class="imagen-box">
        <img class="fondo" src="images/Arty.RV.jpeg" alt="Arte">
        <div class="floating-card">
          <i class="fa-solid fa-vr-cardboard"></i>
          <span>Experiencias VR</span>
        </div>
      </div>

    </div>

    <!-- REALIDAD VIRTUAL -->
    <div class="realidad-container">

      <div class="video-box">
        <video src="videos/videoPaginaInicial.mp4" controls autoplay muted></video>
      </div>

      <div class="content-box">
        <span class="vr-badge">Experiencia inmersiva</span>
        <h2>Descubre nuestro museo virtual</h2>
        <p>Vive una experiencia artística interactiva y explora galerías digitales como si estuvieras dentro de un museo real.</p>
        <a href="#" class="btn-realidad">Entrar al museo</a>
      </div>

    </div>

  </div>

  <hr>

  <!-- ============================================================ -->
  <!-- GALERÍA DE ARTE                                              -->
  <!-- ============================================================ -->
  <section class="arte-section">

    <div class="arte-bg"></div>

    <div class="container">

      <div class="arte-header">
        <span class="arte-badge">Galería artística</span>
        <h1>Conoce más sobre el arte</h1>
        <p>Descubre expresiones visuales, creatividad y experiencias que inspiran emociones.</p>
      </div>

      <div id="carouselArte" class="carousel slide carousel-fade" data-bs-ride="carousel">

        <div class="carousel-inner">

          <div class="carousel-item active">
            <div class="arte-card">
              <img src="images/img-info.jpeg" class="carousel-img" alt="Arte">
              <div class="arte-overlay">
                <h2>Arte Visual</h2>
                <p>Explora obras llenas de color, creatividad y expresión.</p>
              </div>
            </div>
          </div>

          <div class="carousel-item">
            <div class="arte-card">
              <img src="images/img-info2.jpeg" class="carousel-img" alt="Arte">
              <div class="arte-overlay">
                <h2>Inspiración Creativa</h2>
                <p>Descubre ideas que conectan emociones y arte moderno.</p>
              </div>
            </div>
          </div>

          <div class="carousel-item">
            <div class="arte-card">
              <img src="images/img-info3.jpeg" class="carousel-img" alt="Arte">
              <div class="arte-overlay">
                <h2>Expresión Artística</h2>
                <p>Un espacio donde la imaginación cobra vida.</p>
              </div>
            </div>
          </div>

          <div class="carousel-item">
            <div class="arte-card">
              <img src="images/img-info4.jpeg" class="carousel-img" alt="Arte">
              <div class="arte-overlay">
                <h2>Creatividad Digital</h2>
                <p>Arte y tecnología unidos en una experiencia inmersiva.</p>
              </div>
            </div>
          </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselArte" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselArte" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>

      </div>

    </div>

  </section>

  <!-- ============================================================ -->
  <!-- PROMO TIENDA                                                 -->
  <!-- ============================================================ -->
  <section class="promo-cinematic">

    <div class="overlay-dark"></div>

    <div class="container">

      <div class="promo-wrapper">

        <div class="promo-left">

          <span class="promo-tag">TIENDA ARTÍSTICA</span>

          <h1>explora nuestra <span>tienda de arte</span></h1>

<<<<<<< HEAD
          <p>Descubre productos artísticos, herramientas creativas, obras exclusivas y artículos diseñados para inspirar a cada artista.</p>
=======
                <h1>
                    Explora
                    nuestra <span>tienda de arte</span>
                </h1>
>>>>>>> e4fb1438416079680dbae7a01f4d086f299599e3

          <a href="tienda.php" class="promo-button">
            Explorar tienda
            <i class="fa-solid fa-arrow-right"></i>
          </a>

        </div>

      </div>

    </div>

  </section>

  <!-- ============================================================ -->
  <!-- PROMO EDITOR                                                -->
  <!-- ============================================================ -->
  <section class="promo-editor">

    <div class="promo-editor-overlay"></div>

    <div class="container">

      <div class="promo-editor-wrapper">

        <div class="promo-editor-content">

          <span class="promo-editor-tag">🎨 CREATIVIDAD DIGITAL</span>

                <h1>
                    Crea tu propia
                    <span>obra de arte digital</span>
                </h1>

          <p>Dibuja, pinta y da vida a tus ideas con nuestro editor de arte profesional. Pinceles, formas, colores y efectos para que explores todo tu potencial creativo sin límites.</p>

          <a href="juego.php" class="promo-editor-btn">
            <i class="fa-solid fa-paintbrush me-2"></i>
            Probar Editor
            <i class="fa-solid fa-arrow-right ms-2"></i>
          </a>

        </div>

      </div>

    </div>

  </section>

  <!-- ============================================================ -->
  <!-- MISIÓN Y VISIÓN                                             -->
  <!-- ============================================================ -->
  <section class="py-5" id="misionVision">

    <div class="container">

      <div class="text-center mb-5">
        <h2 class="fw-bold display-5">Nuestra Misión y Visión</h2>
        <p class="text-muted">Impulsando el talento artístico juvenil de El Salvador</p>
      </div>

      <div class="row g-4 justify-content-center">

        <div class="col-md-6">
          <div class="card border-0 shadow-lg h-100 p-4 rounded-4 card-hover">
            <div class="icon-box mb-4">
              <i class="fa-solid fa-paintbrush"></i>
            </div>
            <h3 class="fw-bold mb-3">Misión</h3>
            <p class="text-muted">Impulsar, apoyar y visibilizar el talento artístico de jóvenes salvadoreños mediante una plataforma digital inclusiva donde puedan compartir y difundir sus obras, como pintura, música, poesía y otras expresiones creativas, fomentando el arte, la cultura y nuevas oportunidades de crecimiento.</p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card border-0 shadow-lg h-100 p-4 rounded-4 card-hover">
            <div class="icon-box mb-4">
              <i class="fa-regular fa-lightbulb"></i>
            </div>
            <h3 class="fw-bold mb-3">Visión</h3>
            <p class="text-muted">Ser la principal plataforma digital de arte juvenil en El Salvador, reconocida por proyectar el talento de jóvenes artistas a nivel nacional e internacional, creando una comunidad creativa que inspire, conecte y transforme la cultura a través del arte.</p>
          </div>
        </div>

      </div>

    </div>

  </section>

  <!-- ============================================================ -->
  <!-- TESTIMONIOS                                                 -->
  <!-- ============================================================ -->
  <section class="testimonios">
    <div class="container">
      <div class="testimonios-header">
        <span class="testimonios-badge">💬 TESTIMONIOS</span>
        <h2>Lo que dicen los <span>artistas</span></h2>
      </div>

      <div class="testimonios-grid">
        <div class="testimonio-card">
          <div class="testimonio-estrellas">★★★★★</div>
          <p>"Soy Arte me ha dado la oportunidad de mostrar mi trabajo a toda una comunidad. ¡Es increíble!"</p>
          <div class="testimonio-autor">
            <div class="testimonio-avatar">👤</div>
            <div>
              <strong>Pedro González</strong>
              <span>Artista visual</span>
            </div>
          </div>
        </div>

        <div class="testimonio-card">
          <div class="testimonio-estrellas">★★★★★</div>
          <p>"La plataforma es fácil de usar y me ha conectado con otros artistas talentosos. ¡Recomendado!"</p>
          <div class="testimonio-autor">
            <div class="testimonio-avatar">👤</div>
            <div>
              <strong>Ana Rivera</strong>
              <span>Músico</span>
            </div>
          </div>
        </div>

        <div class="testimonio-card">
          <div class="testimonio-estrellas">★★★★★</div>
          <p>"El editor de arte digital es mi herramienta favorita. Puedo crear desde cualquier lugar."</p>
          <div class="testimonio-autor">
            <div class="testimonio-avatar">👤</div>
            <div>
              <strong>Arturo Renderos</strong>
              <span>Artista</span>
            </div>
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
  <script src="JavaScript/script.js"></script>

</body>
</html>