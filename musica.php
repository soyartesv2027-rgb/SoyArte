<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoyArte-Musica</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="styles/musica.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

    <?php include("components/navbar.php"); ?>

    <section class="banner">
    <img src="images/banner.jpeg" alt="Banner Image">
        <div class="overlay">
            <h2>
                <i class="fa-solid fa-music"></i>
                Música
            </h2>

            <p>
                "La música expresa lo que no puede ser dicho y aquello sobre lo que es imposible permanecer en silencio."
            </p>
        </div>
    </section>

    <div class="search-container">
        <input type="text" placeholder="Buscar">
    </div>

    
    <main class="cards" >

        <?php for($i = 0; $i < 6; $i++) { ?>

        <div class="card" >

            <div class="card-image">
                <button class="play-btn">
                    <i class="fa-solid fa-play"></i>
                </button>
            </div>

            <div class="card-content">
                <div class="title-row">
                    <div>
                        <h3>Nombre de la música</h3>
                        <p>Compositor</p>
                    </div>

                    <i class="fa-regular fa-heart"></i>
                </div>

                <div class="player">
                    <i class="fa-solid fa-circle-play"></i>

                    <input type="range">

                    <span>0:00</span>
                </div>
            </div>

        </div>

        <?php } ?>

    </main>

    
    <button class="floating-btn">
        <i class="fa-solid fa-plus"></i>
    </button>


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