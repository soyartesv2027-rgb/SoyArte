<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>SoyArte | Cultura y talento salvadoreño</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">

  <!-- Rediseño editorial SoyArte — navbar y footer originales conservados -->
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>

<body>

<?php include("components/navbar.php"); ?>

<main>

  <!-- =========================================================
       HERO
       ========================================================= -->
  <section class="sa-hero">
    <div class="sa-hero-noise"></div>

    <div class="container sa-hero-grid">

      <div class="sa-hero-copy sa-reveal">
        <span class="sa-kicker">
          <span></span> Plataforma cultural salvadoreña
        </span>

        <h1>
          El arte tiene
          <em>un lugar.</em>
        </h1>

        <p>
          Descubre, crea y comparte el talento artístico de jóvenes
          salvadoreños en un espacio creado para la creatividad.
        </p>

        <div class="sa-hero-actions">
          <a href="#explorar" class="sa-btn sa-btn-primary">
            Explorar obras
            <i class="fa-solid fa-arrow-down"></i>
          </a>

          <a href="registrarse.php" class="sa-text-link">
            Comparte tu talento
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

        <div class="sa-hero-meta">
          <span><i class="fa-solid fa-palette"></i> Arte</span>
          <span><i class="fa-solid fa-music"></i> Música</span>
          <span><i class="fa-solid fa-pen-nib"></i> Poesía</span>
          <span><i class="fa-solid fa-scissors"></i> Manualidades</span>
        </div>
      </div>

      <div class="sa-hero-visual sa-reveal">
        <div class="sa-hero-image-wrap">
          <img
            src="images/img-info.jpeg"
            alt="Obra artística de SoyArte"
            class="sa-hero-image"
          >

          <div class="sa-hero-label">
            <span class="sa-label-number">01</span>
            <span>
              <small>Descubre</small>
              Expresiones que inspiran
            </span>
          </div>
        </div>

        <div class="sa-hero-accent sa-hero-accent-one"></div>
        <div class="sa-hero-accent sa-hero-accent-two"></div>
      </div>

    </div>

    <div class="sa-scroll-indicator">
      <span>Desliza para explorar</span>
      <i class="fa-solid fa-arrow-down"></i>
    </div>
  </section>


  <!-- =========================================================
       CATEGORÍAS
       ========================================================= -->
  <section class="sa-section sa-explore" id="explorar">
    <div class="container">

      <div class="sa-section-heading sa-reveal">
        <div>
          <span class="sa-kicker purple">Explora</span>
          <h2>Un universo de<br><em>creatividad.</em></h2>
        </div>

        <p>
          Desde una pincelada hasta una melodía. Encuentra diferentes
          formas de expresión y descubre nuevos talentos.
        </p>
      </div>

      <div class="sa-gallery-carousel" id="saGalleryCarousel" aria-label="Categorías de SoyArte">
        <div class="sa-gallery-track">

          <a href="pinturas.php" class="sa-gallery-slide is-active" data-index="0">
            <img src="images/pinturas.jpeg" alt="Pinturas" loading="eager">
            <div class="sa-gallery-overlay">
              <span>01 / 05</span>
              <div>
                <h3>Pintura</h3>
                <p>Color, técnica y expresión visual.</p>
              </div>
            </div>
          </a>

          <a href="musica.php" class="sa-gallery-slide" data-index="1">
            <img src="images/musica.jpeg" alt="Música" loading="lazy">
            <div class="sa-gallery-overlay">
              <span>02 / 05</span>
              <div>
                <h3>Música</h3>
                <p>Historias que cobran vida a través del sonido.</p>
              </div>
            </div>
          </a>

          <a href="poesia.php" class="sa-gallery-slide" data-index="2">
            <img src="images/manualidades.jpeg" alt="Poesía" loading="lazy">
            <div class="sa-gallery-overlay">
              <span>03 / 05</span>
              <div>
                <h3>Poesía</h3>
                <p>Palabras convertidas en emociones.</p>
              </div>
            </div>
          </a>

          <a href="manualidad.php" class="sa-gallery-slide" data-index="3">
            <img src="images/Poemas.jpeg" alt="Manualidades" loading="lazy">
            <div class="sa-gallery-overlay">
              <span>04 / 05</span>
              <div>
                <h3>Manualidades</h3>
                <p>Creaciones hechas con imaginación y dedicación.</p>
              </div>
            </div>
          </a>

          <a href="realidad_virtual.php" class="sa-gallery-slide" data-index="4">
            <img src="images/Arty.RV.jpeg" alt="Museo Virtual" loading="lazy">
            <div class="sa-gallery-overlay">
              <span>05 / 05</span>
              <div>
                <h3>Museo Virtual</h3>
                <p>Una experiencia para descubrir el arte de otra manera.</p>
              </div>
            </div>
          </a>

        </div>

        <button class="sa-gallery-arrow sa-gallery-prev" type="button" aria-label="Categoría anterior">
          <i class="fa-solid fa-chevron-left"></i>
        </button>
        <button class="sa-gallery-arrow sa-gallery-next" type="button" aria-label="Categoría siguiente">
          <i class="fa-solid fa-chevron-right"></i>
        </button>
      </div>

      <div class="sa-gallery-controls">
        <span id="saGalleryCounter">01 / 05</span>
        <div class="sa-gallery-progress"><span id="saGalleryProgress"></span></div>
        <span class="sa-gallery-hint">Haz clic para explorar</span>
      </div>
    </div>
  </section>


  <!-- =========================================================
       FRASE / IDENTIDAD
       ========================================================= -->
  <section class="sa-statement">
    <div class="container">
      <div class="sa-statement-inner sa-reveal">
        <span class="sa-kicker">SoyArte</span>

        <h2>
          La creatividad no necesita permiso.
          <em>Necesita un espacio.</em>
        </h2>

        <div class="sa-statement-line"></div>

        <p>
          Creamos SoyArte para que jóvenes artistas puedan mostrar lo que
          hacen, conectar con otras personas y encontrar nuevas formas
          de compartir su talento.
        </p>
      </div>
    </div>
  </section>


  <!-- =========================================================
       QUÉ ES SOYARTE
       ========================================================= -->
  <section class="sa-section sa-about">
    <div class="container">

      <div class="sa-about-grid">

        <div class="sa-about-image sa-reveal">
          <img src="images/Arty.RV.jpeg" alt="Experiencia artística de SoyArte" loading="lazy">

          <div class="sa-image-caption">
            <span>SOYARTE</span>
            <strong>Arte + tecnología</strong>
          </div>
        </div>

        <div class="sa-about-copy sa-reveal">
          <span class="sa-kicker purple">Nuestra plataforma</span>

          <h2>
            Inspira.<br>
            Crea.<br>
            <em>Comparte.</em>
          </h2>

          <p>
            SoyArte es una plataforma diseñada para artistas, creadores
            y soñadores que desean expresar sus ideas a través de la
            pintura, música, poesía, manualidades y arte digital.
          </p>

          <p>
            Explora obras, conoce nuevas propuestas y forma parte de una
            comunidad donde el talento puede ser visto.
          </p>

          <div class="sa-about-features">
            <div>
              <span>01</span>
              <strong>Descubre</strong>
              <p>Encuentra nuevas expresiones artísticas.</p>
            </div>

            <div>
              <span>02</span>
              <strong>Comparte</strong>
              <p>Da a conocer tus propias creaciones.</p>
            </div>

            <div>
              <span>03</span>
              <strong>Conecta</strong>
              <p>Forma parte de una comunidad creativa.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>


  <!-- =========================================================
       MUSEO VIRTUAL
       ========================================================= -->
  <section class="sa-museum">
    <div class="container">

      <div class="sa-museum-grid">

        <div class="sa-museum-copy sa-reveal">
          <span class="sa-kicker light">Experiencia inmersiva</span>

          <h2>
            Entra al<br>
            <em>museo virtual.</em>
          </h2>

          <p>
            Explora galerías digitales como si estuvieras dentro de un
            museo real y descubre el arte desde una nueva perspectiva.
          </p>

          <a href="realidad_virtual.php" class="sa-btn sa-btn-light">
            Entrar al museo
            <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>

        <div class="sa-museum-media sa-reveal">
          <video
            src="videos/videoPaginaInicial.mp4"
            autoplay
            muted
            loop
            playsinline
            controls
            aria-label="Vista previa del museo virtual">
          </video>

          <div class="sa-museum-tag">
            <i class="fa-solid fa-vr-cardboard"></i>
            <span>Realidad virtual</span>
          </div>
        </div>

      </div>

    </div>
  </section>


  <!-- =========================================================
       GALERÍA EDITORIAL
       ========================================================= -->
  <section class="sa-section sa-gallery">
    <div class="container">

      <div class="sa-section-heading centered sa-reveal">
        <div>
          <span class="sa-kicker purple">Galería</span>
          <h2>El arte puede<br><em>decirlo todo.</em></h2>
        </div>

        <p>
          Una mirada a diferentes formas de crear, imaginar y transformar
          una idea en una obra.
        </p>
      </div>

      <div class="sa-editorial-grid">

        <article class="sa-editorial-card sa-editorial-large sa-reveal">
          <img src="images/img-info.jpeg" alt="Arte visual" loading="lazy">
          <div class="sa-editorial-overlay">
            <span>01 / Arte visual</span>
            <h3>Donde una idea<br>se convierte en imagen.</h3>
          </div>
        </article>

        <article class="sa-editorial-card sa-reveal">
          <img src="images/img-info2.jpeg" alt="Inspiración creativa" loading="lazy">
          <div class="sa-editorial-overlay">
            <span>02 / Inspiración</span>
            <h3>Ideas que conectan.</h3>
          </div>
        </article>

        <article class="sa-editorial-card sa-reveal">
          <img src="images/img-info3.jpeg" alt="Expresión artística" loading="lazy">
          <div class="sa-editorial-overlay">
            <span>03 / Expresión</span>
            <h3>Imaginación sin límites.</h3>
          </div>
        </article>

        <article class="sa-editorial-card sa-reveal">
          <img src="images/img-info4.jpeg" alt="Creatividad digital" loading="lazy">
          <div class="sa-editorial-overlay">
            <span>04 / Arte digital</span>
            <h3>Arte y tecnología.</h3>
          </div>
        </article>

      </div>

    </div>
  </section>


  <!-- =========================================================
       EDITOR DE ARTE
       ========================================================= -->
  <section class="sa-feature sa-editor">
    <div class="sa-feature-image">
      <img src="images/hover-juego.png" alt="Editor de arte digital" loading="lazy">
    </div>

    <div class="container sa-feature-container">
      <div class="sa-feature-copy sa-reveal">
        <span class="sa-kicker light">Creatividad digital</span>

        <h2>
          Crea tu propia<br>
          <em>obra de arte.</em>
        </h2>

        <p>
          Dibuja, pinta y da vida a tus ideas con nuestro editor de arte
          digital. Experimenta con pinceles, formas, colores y efectos.
        </p>

        <a href="juego.php" class="sa-btn sa-btn-light">
          Probar editor
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>


  <!-- =========================================================
       TIENDA
       ========================================================= -->
  <section class="sa-feature sa-store">
    <div class="sa-feature-image">
      <img src="images/fondodeTienda.png" alt="Tienda de SoyArte" loading="lazy">
    </div>

    <div class="container sa-feature-container sa-feature-right">
      <div class="sa-feature-copy sa-reveal">
        <span class="sa-kicker light">Tienda artística</span>

        <h2>
          El arte también<br>
          <em>se puede llevar contigo.</em>
        </h2>

        <p>
          Descubre productos artísticos, herramientas creativas, obras
          exclusivas y artículos diseñados para inspirar.
        </p>

        <a href="tienda.php" class="sa-btn sa-btn-light">
          Explorar tienda
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </div>
  </section>


  <!-- =========================================================
       MISIÓN Y VISIÓN
       ========================================================= -->
  <section class="sa-section sa-mission">
    <div class="container">

      <div class="sa-section-heading centered sa-reveal">
        <div>
          <span class="sa-kicker purple">Nuestra razón de ser</span>
          <h2>Impulsar el talento<br><em>que nos representa.</em></h2>
        </div>

        <p>
          Una plataforma para visibilizar el talento artístico juvenil
          y acercar la creatividad salvadoreña a nuevas personas.
        </p>
      </div>

      <div class="sa-mission-grid">

        <article class="sa-mission-card sa-reveal">
          <span class="sa-card-number">01</span>
          <i class="fa-solid fa-paintbrush"></i>
          <h3>Misión</h3>
          <p>
            Impulsar, apoyar y visibilizar el talento artístico de jóvenes
            salvadoreños mediante una plataforma digital inclusiva donde
            puedan compartir y difundir sus obras, fomentando el arte,
            la cultura y nuevas oportunidades de crecimiento.
          </p>
        </article>

        <article class="sa-mission-card sa-reveal">
          <span class="sa-card-number">02</span>
          <i class="fa-regular fa-lightbulb"></i>
          <h3>Visión</h3>
          <p>
            Ser una plataforma digital de arte juvenil reconocida por
            proyectar el talento de jóvenes artistas a nivel nacional e
            internacional, creando una comunidad creativa que inspire,
            conecte y transforme la cultura a través del arte.
          </p>
        </article>

      </div>
    </div>
  </section>


  <!-- =========================================================
       CTA FINAL
       ========================================================= -->
  <section class="sa-final-cta">
    <div class="container">
      <div class="sa-final-inner sa-reveal">

        <span class="sa-kicker purple">Tu turno</span>

        <h2>
          Tu talento merece<br>
          <em>ser visto.</em>
        </h2>

        <p>
          Únete a SoyArte y encuentra un espacio para compartir
          aquello que te hace crear.
        </p>

        <a href="registrarse.php" class="sa-btn sa-btn-primary">
          Únete a SoyArte
          <i class="fa-solid fa-arrow-right"></i>
        </a>

      </div>
    </div>
  </section>

</main>

<?php include("components/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // ============================================================
  // CARRUSEL PRINCIPAL DE SOYARTE
  // Tarjeta central + tarjetas laterales + autoplay + touch
  // ============================================================
  (() => {
    const carousel = document.getElementById("saGalleryCarousel");
    if (!carousel) return;

    const slides = Array.from(carousel.querySelectorAll(".sa-gallery-slide"));
    const prevButton = carousel.querySelector(".sa-gallery-prev");
    const nextButton = carousel.querySelector(".sa-gallery-next");
    const counter = document.getElementById("saGalleryCounter");
    const progress = document.getElementById("saGalleryProgress");

    if (slides.length < 2 || !prevButton || !nextButton) return;

    let current = 0;
    let autoplay = null;
    let touchStartX = 0;
    let touchStartY = 0;

    const total = slides.length;

    function normalize(index) {
      return (index + total) % total;
    }

    function relativePosition(index) {
      let difference = index - current;

      // Hace que el carrusel sea circular.
      if (difference > total / 2) difference -= total;
      if (difference < -total / 2) difference += total;

      return difference;
    }

    function updateCarousel() {
      slides.forEach((slide, index) => {
        slide.classList.remove(
          "is-active",
          "is-prev",
          "is-next",
          "is-prev-2",
          "is-next-2",
          "is-hidden"
        );

        const position = relativePosition(index);

        if (position === 0) {
          slide.classList.add("is-active");
        } else if (position === -1) {
          slide.classList.add("is-prev");
        } else if (position === 1) {
          slide.classList.add("is-next");
        } else if (position === -2) {
          slide.classList.add("is-prev-2");
        } else if (position === 2) {
          slide.classList.add("is-next-2");
        } else {
          slide.classList.add("is-hidden");
        }
      });

      if (counter) {
        counter.textContent = `${String(current + 1).padStart(2, "0")} / ${String(total).padStart(2, "0")}`;
      }

      if (progress) {
        progress.style.width = `${((current + 1) / total) * 100}%`;
      }
    }

    function goTo(index) {
      current = normalize(index);
      updateCarousel();
    }

    function next() {
      goTo(current + 1);
    }

    function previous() {
      goTo(current - 1);
    }

    function stopAutoplay() {
      if (autoplay) {
        clearInterval(autoplay);
        autoplay = null;
      }
    }

    function startAutoplay() {
      stopAutoplay();
      autoplay = setInterval(next, 5000);
    }

    prevButton.addEventListener("click", (event) => {
      event.preventDefault();
      previous();
      startAutoplay();
    });

    nextButton.addEventListener("click", (event) => {
      event.preventDefault();
      next();
      startAutoplay();
    });

    // Si se hace clic en una tarjeta lateral, primero pasa al centro.
    // Si ya está en el centro, funciona como enlace a su sección.
    slides.forEach((slide, index) => {
      slide.addEventListener("click", (event) => {
        if (index !== current) {
          event.preventDefault();
          goTo(index);
          startAutoplay();
        }
      });
    });

    carousel.addEventListener("mouseenter", stopAutoplay);
    carousel.addEventListener("mouseleave", startAutoplay);

    carousel.addEventListener("touchstart", (event) => {
      const touch = event.changedTouches[0];
      touchStartX = touch.clientX;
      touchStartY = touch.clientY;
      stopAutoplay();
    }, { passive: true });

    carousel.addEventListener("touchend", (event) => {
      const touch = event.changedTouches[0];
      const deltaX = touch.clientX - touchStartX;
      const deltaY = touch.clientY - touchStartY;

      // Solo se considera swipe si el movimiento fue principalmente horizontal.
      if (Math.abs(deltaX) > 45 && Math.abs(deltaX) > Math.abs(deltaY)) {
        if (deltaX < 0) next();
        else previous();
      }

      startAutoplay();
    }, { passive: true });

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) stopAutoplay();
      else startAutoplay();
    });

    // Estado inicial: Pintura al centro.
    updateCarousel();
    startAutoplay();
  })();
</script>

<script>
  // Animaciones de aparición al hacer scroll
  const revealElements = document.querySelectorAll(".sa-reveal");

  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.12
    }
  );

  revealElements.forEach((element) => {
    revealObserver.observe(element);
  });
</script>

<script src="JavaScript/script.js"></script>

</body>
</html>