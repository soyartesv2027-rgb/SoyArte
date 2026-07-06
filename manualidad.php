<?php
session_start();
require_once "php/conexion.php";

$sql = "SELECT * FROM manualidades ORDER BY id DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manualidades</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="style.css">

<link rel="stylesheet" href="styles/manualidades.css?v=<?php echo time(); ?>">

</head>

<body>

<<<<<<< HEAD
    <?php $seccion = 'manualidades'; include("components/navbar-unificado.php"); ?>

    
    <!-- CONTENEDOR -->
    <div class="contenedor">
    
    
        <!-- BANNER -->
    <section class="banner">    
=======
<?php include("components/navbar.php"); ?>

<div class="contenedor">

    <!-- ========================= -->
    <!-- BANNER -->
    <!-- ========================= -->

    <section class="banner">

>>>>>>> 9e2a7902b5621d9597bcab8cf71d6d9be9133c83
        <div class="contenido-banner">

            <h2> Manualidades</h2>

            <p>
                Donde las manos transforman ideas en arte.
            </p>

        </div>

    </section>

    <!-- ========================= -->
    <!-- BUSCADOR -->
    <!-- ========================= -->

    <section class="contenedor-buscador">

        <div class="buscador">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="buscador"
                placeholder="Buscar manualidades..."
            >

        </div>

    </section>

    <!-- ========================= -->
    <!-- TARJETAS -->
    <!-- ========================= -->

    <div class="manualidades-grid">

<?php if($resultado->num_rows > 0): ?>

<?php while($fila = $resultado->fetch_assoc()): ?>

<div
class="manualidad-card"

data-texto="<?php

echo strtolower(

$fila['nombre']." ".
$fila['autor']." ".
$fila['descripcion']

);

?>">

<div class="manualidad-imagen">

<img

src="<?php echo htmlspecialchars($fila['imagen']); ?>"

alt="<?php echo htmlspecialchars($fila['nombre']); ?>"

onerror="this.src='images/sin-imagen.png'"

>

<span class="manualidad-tag">

Manualidad

</span>

</div>

<div class="manualidad-info">

<h3 class="manualidad-titulo">

<?php echo htmlspecialchars($fila['nombre']); ?>

</h3>

<p class="manualidad-autor">

<i class="fa-regular fa-user"></i>

<?php echo htmlspecialchars($fila['autor']); ?>

</p>

<p class="manualidad-descripcion">

<?php

echo nl2br(

htmlspecialchars($fila['descripcion'])

);

?>

</p>

<div class="manualidad-footer">

<span>

<i class="fa-regular fa-calendar"></i>

<?php

echo date(

"d/m/Y",

strtotime($fila["fecha"])

);

?>

</span>

<a

href="ver_manualidad.php?id=<?php echo $fila['id']; ?>"

class="manualidad-boton"

>

Ver más

</a>

</div>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="sin-publicaciones">

<i class="fa-solid fa-scissors"></i>

<h3>

Todavía no hay manualidades publicadas

</h3>

<p>

Sé el primero en compartir una.

</p>

</div>

<?php endif; ?>

</div>

</div>

<!-- ========================= -->
<!-- BOTÓN FLOTANTE -->
<!-- ========================= -->

<?php
if (isset($_SESSION['id']) || isset($_SESSION['usuario_id'])) {
?>
    <a href="agregar_manualidad.php" class="boton-flotante">
        <i class="fa-solid fa-plus"></i>
    </a>
<?php
}
?>

<!-- ========================= -->
<!-- BUSCADOR -->
<!-- ========================= -->

<script>

const buscador = document.getElementById("buscador");

const tarjetas = document.querySelectorAll(".manualidad-card");

buscador.addEventListener("keyup", ()=>{

let texto = buscador.value.toLowerCase();

tarjetas.forEach(card=>{

let contenido = card.dataset.texto;

card.style.display =

contenido.includes(texto)

?

"block"

:

"none";

});

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

window.addEventListener("scroll",()=>{

const section=document.querySelector(".info-soyarte");

if(section){

const position=section.getBoundingClientRect().top;

const screen=window.innerHeight;

if(position<screen-100){

section.classList.add("visible");

}

}

});

</script>

<script src="JavaScript/script.js"></script>

</body>

</html>