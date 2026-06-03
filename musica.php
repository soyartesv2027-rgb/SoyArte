<?php
session_start();
require_once 'php/conexion.php';

$sql = "SELECT * FROM musica ORDER BY musica_id DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoyArte - Música</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="styles/musica.css">
    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("components/navbar.php"); ?>

<section class="banner">

    <img src="images/banner.jpeg" alt="Banner Música">

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

    <input
        type="text"
        id="buscador"
        placeholder="Buscar canción o cantante..."
    >

</div>

<main class="cards" id="lista-musica">

<?php if($resultado->num_rows > 0): ?>

   <?php while($musica = $resultado->fetch_assoc()): ?>

<a
    href="ver_musica.php?id=<?php echo $musica['musica_id']; ?>"
    class="card-link tarjeta-musica"
>

    <div class="card">

        <div class="card-image">

            <img
                src="uploads/musica/<?php echo htmlspecialchars($musica['portada']); ?>"
                alt="<?php echo htmlspecialchars($musica['nombre_cancion']); ?>"
            >

            <button class="play-btn">

                <i class="fa-solid fa-play"></i>

            </button>

        </div>

        <div class="card-content">

            <div class="title-row">

                <div>

                    <h3 class="nombre-cancion">

                        <?php echo htmlspecialchars($musica['nombre_cancion']); ?>

                    </h3>

                    <p class="nombre-cantante">

                        <?php echo htmlspecialchars($musica['nombre_cantante']); ?>

                    </p>

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

</a>

<?php endwhile; ?>

<?php else: ?>

    <div style="text-align:center;width:100%;padding:40px;">

        <h3>No hay publicaciones musicales todavía 🎵</h3>

        <p>Sé el primero en compartir una canción.</p>

    </div>

<?php endif; ?>

</main>

<?php if(isset($_SESSION['usuario_id'])): ?>

<a href="publicar_musica.php" class="floating-btn">

    <i class="fa-solid fa-plus"></i>

</a>

<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
const buscador = document.getElementById("buscador");

buscador.addEventListener("keyup", function(){

    let filtro = buscador.value.toLowerCase();

    let tarjetas = document.querySelectorAll(".tarjeta-musica");

    tarjetas.forEach(tarjeta => {

        let cancion =
            tarjeta.querySelector(".nombre-cancion")
            .textContent
            .toLowerCase();

        let cantante =
            tarjeta.querySelector(".nombre-cantante")
            .textContent
            .toLowerCase();

        if(
            cancion.includes(filtro) ||
            cantante.includes(filtro)
        ){
            tarjeta.style.display = "block";
        }else{
            tarjeta.style.display = "none";
        }

    });

});
</script>
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