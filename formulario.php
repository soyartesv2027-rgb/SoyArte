<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a SoyArte</title>
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="styles/formulario.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="contenedor">

        <div class="form-header">
            <div class="logo"><i class="fa-solid fa-palette"></i> SoyArte</div>
            <p class="subtitulo">Cuéntanos sobre ti para personalizar tu experiencia</p>
        </div>

        <div class="step-indicator">
            <div class="step-dot active">1</div>
            <div class="step-line"></div>
            <div class="step-dot">2</div>
            <div class="step-line"></div>
            <div class="step-dot">3</div>
            <div class="step-line"></div>
            <div class="step-dot">4</div>
        </div>

        <div class="step-label" id="stepLabel">Paso 1 de 4 — Tipo de usuario</div>

        <div id="errorMsg" class="error-msg"></div>

        <form action="php/guardar-formulario.php" method="POST">

            <!-- PASO 1 -->
            <div class="step active">
                <h2>¿Qué tipo de usuario eres?</h2>
                <div class="cards">
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Artista">
                        <span class="card-icon">🎨</span>
                        <span>Artista</span>
                    </label>
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Diseñador">
                        <span class="card-icon">🖌️</span>
                        <span>Diseñador</span>
                    </label>
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Estudiante">
                        <span class="card-icon">📚</span>
                        <span>Estudiante</span>
                    </label>
                    <label class="card">
                        <input type="radio" name="tipo_usuario" value="Aficionado">
                        <span class="card-icon">✨</span>
                        <span>Aficionado</span>
                    </label>
                </div>
                <div class="buttons">
                    <button type="button" class="btn btn-primary next">Continuar <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- PASO 2 -->
            <div class="step">
                <h2>¿Qué tipo de arte te interesa?</h2>
                <div class="tags">
                    <label><input type="checkbox" name="intereses[]" value="Pintura"> 🎨 Pintura</label>
                    <label><input type="checkbox" name="intereses[]" value="Fotografía"> 📸 Fotografía</label>
                    <label><input type="checkbox" name="intereses[]" value="Arte digital"> 💻 Arte digital</label>
                    <label><input type="checkbox" name="intereses[]" value="Manualidades"> 🧵 Manualidades</label>
                    <label><input type="checkbox" name="intereses[]" value="Escultura"> 🗿 Escultura</label>
                    <label><input type="checkbox" name="intereses[]" value="Dibujo"> ✏️ Dibujo</label>
                </div>
                <div class="buttons">
                    <button type="button" class="btn btn-outline back"><i class="fa-solid fa-arrow-left"></i> Atrás</button>
                    <button type="button" class="btn btn-primary next">Continuar <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- PASO 3 -->
            <div class="step">
                <h2>Personaliza tu experiencia</h2>
                <div class="campo">
                    <label>¿Qué formato prefieres?</label>
                    <select name="tipo_tutorial">
                        <option value="">Selecciona una opción</option>
                        <option value="Video">🎥 Videos</option>
                        <option value="Imagenes">🖼️ Imágenes</option>
                        <option value="Texto">📄 Texto</option>
                    </select>
                </div>
                <div class="campo">
                    <label>¿Con qué frecuencia usarías SoyArte?</label>
                    <select name="frecuencia">
                        <option value="">Selecciona una opción</option>
                        <option value="Diario">Todos los días</option>
                        <option value="Semanal">Varias veces por semana</option>
                        <option value="Mensual">Una vez por semana</option>
                        <option value="Ocasional">Ocasionalmente</option>
                    </select>
                </div>
                <div class="campo">
                    <label>¿Qué te gustaría aprender?</label>
                    <textarea name="manualidades" placeholder="Ej: técnicas de acuarela, edición de video, escritura creativa..."></textarea>
                </div>
                <div class="buttons">
                    <button type="button" class="btn btn-outline back"><i class="fa-solid fa-arrow-left"></i> Atrás</button>
                    <button type="button" class="btn btn-primary next">Continuar <i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- PASO 4 -->
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
                    <textarea name="funcion_nueva" placeholder="Ej: un chat en vivo, tutoriales interactivos..."></textarea>
                </div>
                <div class="buttons">
                    <button type="button" class="btn btn-outline back"><i class="fa-solid fa-arrow-left"></i> Atrás</button>
                    <button type="submit" class="btn btn-primary">Finalizar <i class="fa-solid fa-check"></i></button>
                </div>
            </div>

        </form>

    </div>
    <script src="JavaScript/formulario.js?v=<?php echo time(); ?>"></script>
</body>
</html>
