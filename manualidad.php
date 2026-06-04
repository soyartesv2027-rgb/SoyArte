<?php
session_start();
require_once 'php/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/manualidades.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- CONTENEDOR -->
    <div class="contenedor">
    
    <!-- NAVBAR -->
    <header class="navbar">
        <!-- IZQUIERDA -->
        <div class="nav-izquierda">
                <a href="index.php">
                    <img src="images/Arty.png" class="logo">
                </a>
                <a href="index.php"></a>
                    <h1>SoyArte</h1>
                </a>
        </div>
        
            <!-- DERECHA -->
        <div class="nav-derecha">
            <a href="#" class="btn-categoria naranja" title="Pintura">
                <i class="fa-solid fa-paint-brush"></i>
            </a>
            <a href="poesia.php" class="btn-categoria rosa" title="Manualidades">
                <i class="fa-solid fa-scroll"></i>
            </a>
            <a href="musica.php" class="btn-categoria azul" title="Música">
                <i class="fa-solid fa-music"></i>
            </a>
        </div>    
    </header>
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
    
</body>
</html>