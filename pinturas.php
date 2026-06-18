<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "soyarte");

if ($conexion->connect_error) {
    die("Error de conexión");
}

$sql = "SELECT * FROM pinturas ORDER BY ID DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pinturas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="styles/pinturas.css">
</head>

<body>

<?php include("components/navbar.php"); ?>

<header class="banner-container">
    <div class="banner-header">
        <div class="pincel">
            <i class="fa-solid fa-paintbrush"></i>
        </div>

        <h1 class="banner-title">Pinturas</h1>
    </div>

    <p class="frase">
        "Es el silencio que se vuelve visible para permitir que el alma hable a través de los colores y la luz."
    </p>
</header>

<style>
.banner-container {
    width: 100%;
    background-image:
    linear-gradient(rgba(255,255,255,0.4),
    rgba(255,255,255,0.4)),
    url(images/fondo.png.jpeg);

    background-size: cover;
    background-position: center;
    padding: 100px 395px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.banner-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    margin-bottom: 20px;
}

.pincel {
    font-size: 57px;
}

.banner-title {
    font-size: 3.5rem;
    font-weight: 400;
}

.frase {
    font-weight: 850;
    font-style: italic;
    font-size: 15px;
    max-width: 700px;
    margin: auto;
}
</style>

<div class="contenedor-pinturas">

<?php while ($fila = $resultado->fetch_assoc()) { ?>

<div class="art-card">

    <div class="card-image-area">

        <img
            src="<?php echo $fila['imagen']; ?>"
            alt="Pintura"
            class="imagen-pintura">

        <button class="heart-button"
                onclick="toggleHeart(this)">
            <i class="fa-regular fa-heart"></i>
        </button>

    </div>

    <div class="card-info-area">

        <h2 class="paint-title">
            <?php echo htmlspecialchars($fila['nombre_pintura']); ?>
        </h2>

        <p class="author-name">
            <?php echo htmlspecialchars($fila['autor']); ?>
        </p>

        <span class="tipoarte">
            <?php echo htmlspecialchars($fila['descripcion']); ?>
        </span>

    </div>

</div>

<?php } ?>

</div>

<a href="form_pintura.html" class="añadir-boton">
    <button class="boton-plus">
        <i class="fa-solid fa-plus"></i>
    </button>
</a>

<script>
function toggleHeart(button){
    button.classList.toggle("active");

    const icon = button.querySelector("i");

    if(button.classList.contains("active")){
        icon.classList.remove("fa-regular");
        icon.classList.add("fa-solid");
    } else {
        icon.classList.remove("fa-solid");
        icon.classList.add("fa-regular");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>