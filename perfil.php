<?php
session_start();

// Protección: solo usuarios logueados pueden ver el perfil
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mi Perfil · Soy Arte</title>

  <!-- Bootstrap (para que el navbar funcione igual) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Estilos propios del perfil (no tocan el style.css del index) -->
  <link rel="stylesheet" href="styles/perfil.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Navbar reutilizable — no altera nada del perfil -->
  <?php include("components/navbar.php"); ?>

  <!-- Contenido del perfil -->
  <div class="page">
    <main class="card">
      <div class="avatar-area">
        <div class="avatar"><span>🎨</span></div>
        <h1 id="nombre-display">Cargando...</h1>
        <p id="correo-display" class="subtitulo"></p>
      </div>

      <div id="mensaje" class="mensaje oculto"></div>

      <form id="form-perfil">

        <div class="grupo">
          <label for="nombre">Nombre completo</label>
          <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required />
        </div>

        <div class="grupo">
          <label for="correo">Correo electrónico</label>
          <input type="email" id="correo" name="correo" placeholder="tucorreo@email.com" required />
        </div>

        <div class="grupo">
          <label for="fecha_nacimiento">Fecha de nacimiento</label>
          <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" />
        </div>

        <div class="grupo">
          <label for="pais">País</label>
          <input type="text" id="pais" name="pais" placeholder="Ej: El Salvador" />
        </div>

        <div class="grupo">
          <label for="biografia">Biografía</label>
          <textarea id="biografia" name="biografia" placeholder="Cuéntanos algo sobre ti..." rows="3"></textarea>
        </div>

        <hr class="divisor" />
        <p class="nota">Dejar en blanco si no deseas cambiar tu contraseña.</p>

        <div class="grupo">
          <label for="password">Nueva contraseña</label>
          <input type="password" id="password" name="password" placeholder="••••••••" />
        </div>

        <div class="grupo">
          <label for="password_confirm">Confirmar contraseña</label>
          <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" />
        </div>

        <button type="submit" class="btn-guardar">Guardar cambios</button>

      </form>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="JavaScrip/perfil.js"></script>
  <script src="JavaScrip/script.js"></script>
</body>
</html>