<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoyArte-Musica</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="styles/musica.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>

   <header class="topbar">
    
        <svg id="menuBtn" class="menu-btn" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">

            <path fill="black" d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"></path>
        </svg>

        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="index.php">

            Soy Arte

            <img src="images/Arty.png" alt="Arty" width="40" height="40" style="object-fit: contain;">

        </a>

        <div class="icons">
            <div class="color-boxes">
                <div class="box naranja"> <i class="fa-solid fa-paintbrush"></i> </div>
                <div class="box rosa">  <i class="fa-solid fa-music"></i> </div>
                <div class="box verde"> <i class="fa-solid fa-earth-americas"> </i></div>
            </div>

           
          
          
            
        </div>
    </header> 


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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

</body>
</html>