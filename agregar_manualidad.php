<?php session_start(); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Manualidad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="styles/agregar_manualidad.css">
</head>
<body>

    <div class="contenedor-form">

        <div class="form-card">

            <!-- TÍTULO -->
            <div class="form-header">
                <i class="fa-solid fa-scissors"></i>
                <h2>Nueva Manualidad</h2>
            </div>

            <form action="php/agregar_manualidades.php" method="POST" enctype="multipart/form-data">

                <!-- NOMBRE -->
                <div class="campo">
                    <label>Nombre</label>
                    <input type="text" name="nombre" placeholder="Nombre de la manualidad">
                </div>

                <!-- AUTOR -->
                <div class="campo">
                    <label>Autor</label>
                    <input type="text" name="autor" placeholder="Tu nombre">
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="campo">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="4" placeholder="Describe tu manualidad..."></textarea>
                </div>

                <!-- IMAGEN -->
                <div class="campo">
                    <label>Imagen</label>
                    <input type="file" name="imagen" accept="image/*">
                </div>

                <!-- BOTÓN -->
                <button type="submit" class="btn-guardar">
                    <i class="fa-solid fa-plus"></i> Guardar
                </button>

            </form>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>