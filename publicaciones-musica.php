<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Musica</title>

    <!-- CSS -->
    <link rel="stylesheet" href="styles/publicaciones-musica.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <canvas id="canvas"></canvas>
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="app">

        <!-- HEADER -->
        <header class="topbar">

            <div class="back">
                <i class="fa-solid fa-arrow-left"></i>
            </div>

            <div class="user-top">
                <img src="images/Arty.png" alt="">
            </div>

        </header>

        <!-- CONTENIDO -->
        <main class="contenido">

            <!-- IMAGEN O VIDEO -->
            <div class="album">

                <img src="images/album.jpg" alt="">

            </div>

            <h1>TITULO DE MUSICA</h1>

            <!-- DESCRIPCION -->
            <div class="descripcion">

                <h3>Descripcion:</h3>

                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Ut et massa mi. Aliquam in hendrerit urna.
                    Pellentesque sit amet sapien fringilla, mattis ligula
                    consectetur, ultrices mauris.
                </p>

            </div>

            <!-- PERFIL -->
            <div class="perfil-artista">

                <i class="fa-regular fa-user"></i>

                <span>Perfil del artista</span>

            </div>

            <!-- COMENTARIOS -->
            <div class="comentarios">

            </div>

            <!-- INPUT -->
            <div class="enviar-comentario">

                <div class="foto-user"></div>

                <input type="text" placeholder="Comentar">

                <button>
                    <i class="fa-solid fa-paper-plane"></i>
                </button>

            </div>

        </main>

    </div>
    
</body>

</html>