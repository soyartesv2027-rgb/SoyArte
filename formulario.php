<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
?>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoyArte</title>
    <link rel="stylesheet" href="styles/formulario.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedor">
    <!-- Barra de progreso -->
        <div class="progress">
            <div class="progress-bar" id="progress-bar"></div>
        </div>

        <form action="php/guardar-formulario.php" method="POST">
            <div class="step active">
                <h2>¿Qué tipo de usuario eres?</h2>
                <div class="cards">
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Artista" required>
                        <span>🎨 Artista</span>
                    </label>
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Diseñador">
                        <span>🖌️ Diseñador</span>
                    </label>
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Estudiante">
                        <span>📚 Estudiante</span>
                    </label>
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Aficionado">
                        <span>✨ Aficionado</span>
                    </label>
                </div>
                <button type="button" class="next">Continuar</button>
            </div>
            <div class="step">
                <h2>¿Qué tipo de arte te interesa?</h2>
                <div class="tags">
                    <label>
                        <input type="checkbox" name="intereses[]" value="Pintura">
                        🎨 Pintura
                    </label>
                    <label>
                        <input type="checkbox" name="intereses[]" value="Fotografía">
                        📸 Fotografía
                    </label>
                    <label>
                        <input type="checkbox" name="intereses[]" value="Arte digital">
                        💻 Arte digital
                    </label>
                    <label>
                        <input type="checkbox" name="intereses[]" value="Manualidades">
                        🧵 Manualidades
                    </label>
                    <label>
                        <input type="checkbox" name="intereses[]" value="Escultura">
                        🗿 Escultura
                    </label>
                    <label>
                        <input type="checkbox" name="intereses[]" value="Dibujo">
                        ✏️ Dibujo
                    </label>
                </div>
                <div class="buttons">
                    <button type="button" class="back">
                        Atrás
                    </button>
                    <button type="button" class="next">
                        Continuar
                    </button>
                </div>
            </div>
            <div class="step">
                <h2>Personaliza tu experiencia</h2>
                <div class="campo">
                    <label>¿Qué formato prefieres?</label>
                    <select name="tipo_tutorial" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Video">🎥 Videos</option>
                        <option value="Imagenes">🖼️ Imágenes</option>
                        <option value="Texto">📄 Texto</option>
                    </select>
                </div>
                <div class="campo">
                    <label>¿Con qué frecuencia usarías SoyArte?</label>
                    <select name="frecuencia" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Diario">Todos los días</option>
                        <option value="Semanal">Varias veces por semana</option>
                        <option value="Mensual">Una vez por semana</option>
                        <option value="Ocasional">Ocasionalmente</option>
                    </select>
                </div>
                <div class="campo">
                    <label>¿Qué te gustaría aprender?</label>
                    <textarea name="manualidades" required></textarea>
                </div>
                <div class="buttons">
                    <button type="button" class="back">
                        Atrás
                    </button>
                    <button type="button" class="next">
                        Continuar
                    </button>
                </div>
            </div>
            <div class="step">
                <h2>Comunidad SoyArte</h2>
                <div class="checks">
                    <label>
                        <input type="checkbox" name="subir_obras" value="Sí">
                        Quiero subir mis obras
                    </label>
                    <label>
                        <input type="checkbox" name="interactuar" value="Sí">
                        Interactuar con otros artistas
                    </label>
                    <label>
                        <input type="checkbox" name="comentarios" value="Sí">
                        Ver comentarios y reseñas
                    </label>
                </div>
                <div class="campo">
                    <label>¿Qué función nueva te gustaría?</label>
                    <textarea name="funcion_nueva"></textarea>
                </div>
                <div class="buttons">
                    <button type="button" class="back">
                        Atrás
                    </button>
                    <button type="submit">
                        Finalizar
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script src="JavaScript/formulario.js"></script>

</body>
</html>