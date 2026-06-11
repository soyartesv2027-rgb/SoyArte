<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>pinturas</title>
  <link rel="stylesheet" href="styles/pinturas.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
            font-size: 15px;
            margin: 0 auto;
            max-width: 700px;
            color: #000000;
            line-height: 1.4;

        }

        .texto{
            margin-left: 60px;
        }
        
    </style>

    

 <div class="art-card">
    <div class="card-image-area">
    
      <div class="card-image-area">

 </div>

<div class="card-image-area">
  <span class="placeholder-text">La pintura</span>
  <button class="heart-button" onclick="toggleHeart(this)">
    <i class="fa-regular fa-heart"></i>
  </button>
</div>
    </div>

    <div class="card-info-area">
      <h2 class="paint-title">Nombre de la pintura</h2>
      <p class="author-name">Nombre del autor</p>
      <span class="tipoarte">
        Tipo de arte
      </span>
    </div>
  </div>
    
  <a href="form_pintura.html" class="añadir-boton">
    <button class="boton-plus">
        <i class="fa-solid fa-plus"></i>
    </button>
</a>


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