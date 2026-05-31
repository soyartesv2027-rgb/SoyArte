<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Soy Arte - Publicar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    
    <?php include("componentes/navbar.php"); ?>

    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow p-4">
            <h2 class="text-center mb-4">Nueva Publicación</h2>
            <form action="procesar_publicacion.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Poema</label>
                    <textarea name="poema" class="form-control" rows="6" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ilustración</label>
                    <input type="file" name="imagen" class="form-control" accept="image/*" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-dark">Publicar</button>
                    <a href="poesia.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>