<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "soyarte");
if ($conexion->connect_error) {
    die("Error de conexión");
}
$sql = "SELECT * FROM pinturas ORDER BY ID DESC";
$resultado = $conexion->query($sql);

$idUsuario = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pinturas</title>
  <link rel="stylesheet" href="styles/pinturas.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
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
      background-image: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.4)), url(images/fondo.png.jpeg);
      background-size: cover;
      background-position: center;
      padding: 100px 395px;
      text-align: center;
      box-sizing: border-box;
      font-family: 'Comfortaa', sans-serif;
      display: flex;
      flex-direction: column;
      justify-content: center;
      box-sizing: border-box;
    }
    .banner-header {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 15px; 
      margin-bottom: 20px;
    }
    .pincel{
      font-size: 57px;
    }
    .banner-title {
      font-size: 3.5rem;
      font-weight: 400;
      margin: 0;
      color: #000000;
    }
    .frase {
      font-family: 'Montserrat', sans-serif;
      font-weight: 850;   
      font-style: italic;
      font-size: 17.5px;
      margin: 0 auto;
      max-width: 700px;
      color: #000000;
      line-height: 1.4;
    }
    .texto{
      margin-left: 60px;
    }
    @media screen and (max-width: 768px){

  .banner-container{
    padding: 60px 20px;
  }

  .banner-title{
    font-size: 2rem;
  }

  .pincel{
    font-size: 35px;
  }

  .frase{
    font-size: 13px;
    max-width: 100%;
  }

}  
  </style>

<div class="contenedor-buscador">

    <div class="buscador">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="buscarPintura"
            placeholder="Buscar pintura o autor...">

    </div>

</div>

    <div class="contenedor-pinturas">

    <?php while ($fila = $resultado->fetch_assoc()) { ?>
    <?php
$tieneLike = false;

if($idUsuario > 0){
    $consultaLike = $conexion->prepare("SELECT id FROM likes_pinturas WHERE id_usuario=? AND id_pintura=?");
    $consultaLike->bind_param("ii",$idUsuario,$fila['ID']);
    $consultaLike->execute();

    $tieneLike = $consultaLike->get_result()->num_rows > 0;
}
?>

<a href="ver_pintura.php?id=<?php echo $fila['ID']; ?>" class="card-link">

    <div class="art-card">

        <div class="card-image-area">

            <img
                src="<?php echo $fila['imagen']; ?>"
                alt="Pintura"
                class="imagen-pintura">

          
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

<?php
$consultaLikes = $conexion->prepare("SELECT COUNT(*) AS total FROM likes_pinturas WHERE id_pintura=?");
$consultaLikes->bind_param("i", $fila['ID']);
$consultaLikes->execute();
$totalLikes = $consultaLikes->get_result()->fetch_assoc();
?>

<div class="likes">

    <button
        class="contador btn-like <?php echo $tieneLike ? 'activo' : ''; ?>"
        data-id="<?php echo $fila['ID']; ?>">

        <i class="<?php echo $tieneLike ? 'fa-solid' : 'fa-regular'; ?> fa-heart"></i>

        <span id="likes-<?php echo $fila['ID']; ?>">
            <?php echo $totalLikes['total']; ?>
        </span>

    </button>

</div>

        </div>

    </div>

</a>

<?php } ?>

    </div>
  <a href="form_pintura.html" class="añadir-boton">
    <button class="boton-plus">
      <i class="fa-solid fa-plus"></i>
    </button>
  </a>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="JavaScript/script.js"></script>
  <script>
document.querySelectorAll(".btn-like").forEach(boton=>{

    boton.addEventListener("click",function(e){

        e.preventDefault();
        e.stopPropagation();

        let id=this.dataset.id;

        fetch("php/dar_like.php",{

            method:"POST",

            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },

            body:"id_pintura="+id

        })

        .then(r=>r.json())

        .then(data=>{

            if(data.estado=="login"){

                alert("Debes iniciar sesión para dar like.");
                return;

            }

            document.getElementById("likes-"+id).innerText=data.likes;

            if(data.like){
                this.classList.add("activo");
this.querySelector("i").classList.remove("fa-regular");
this.querySelector("i").classList.add("fa-solid");

            }else{

          this.classList.remove("activo");
this.querySelector("i").classList.remove("fa-solid");
this.querySelector("i").classList.add("fa-regular");
            }

        });

    });

});
</script>

<script>

const buscador=document.getElementById("buscarPintura");

buscador.addEventListener("keyup",()=>{

    let texto=buscador.value.toLowerCase();

    document.querySelectorAll(".art-card").forEach(card=>{

        let contenido=card.innerText.toLowerCase();

        if(contenido.includes(texto)){

            card.parentElement.style.display="";

        }else{

            card.parentElement.style.display="none";

        }

    });

});

</script>


</body>
</html>