

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/manualidades.css">
</head>
<body>

     <?php include("components/navbar.php"); ?>
    
    <!-- CONTENEDOR -->
    <div class="contenedor">
    
    
        <!-- BANNER -->
    <section class="banner">    
        <div class="contenido-banner">
            <div class="titulo-banner">
                <img src="">
                <h2>Manualidades</h2>
            </div>
            <p>
                "Es el diálogo sagrado entre las manos y la materia,
                donde la paciencia transforma un objeto simple
                en una extensión del creador."
            </p>
        </div>
    </section>
    <!-- BUSCADOR -->
    <section class="contenedor-buscador">
        <div class="buscador">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Buscar">
        </div>
    </section>
    <button class="boton-flotante">
        <a href="agregar_manualidad.php">
            <i class="fa-solid fa-plus"></i>
        </a>
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