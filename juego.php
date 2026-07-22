<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title> SoyArte</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="styles/editor.css?v=<?php echo time(); ?>" />
</head>
<body>

<!-- ============================================================ -->
<!-- EDITOR                                                       -->
<!-- ============================================================ -->
<div id="app" class="editor-container" style="display:flex;flex-direction:column;height:100vh;">

    <!-- HEADER -->
    <header class="editor-header">
        <div class="header-left">
            <div class="logo">
                <span class="logo-icon">🎨</span>
                <span class="logo-text">Juego SoyArte</span>
            </div>
        </div>
        <div class="header-center">
          <!-- TÍTULO DEL PROYECTO -->
            <div class="header-center" style="display:flex;align-items:center;gap:8px;">
                <input type="text" id="titleInput" value="Mi obra" 
                      style="
                          background: transparent;
                          border: none;
                          border-bottom: 2px solid var(--glass-border);
                          color: var(--text-primary);
                          font-size: 1rem;
                          font-weight: 600;
                          padding: 4px 8px;
                          min-width: 150px;
                          max-width: 250px;
                          outline: none;
                          transition: border-color 0.3s ease;
                          font-family: inherit;
                      "
                      placeholder="Título de la obra"
                      onfocus="this.select()"
                >
                <!-- El resto de los botones (Nuevo, Guardar, Abrir, Exportar) -->
            </div>
            <button class="header-btn" id="btnNuevo" title="Nuevo">
                <i class="fa-regular fa-file"></i> Nuevo
            </button>
            <button class="header-btn" id="btnGuardar" title="Guardar">
                <i class="fa-regular fa-floppy-disk"></i> Guardar
            </button>
            <button class="header-btn" id="btnAbrir" title="Abrir proyecto">
                <i class="fa-regular fa-folder-open"></i> Abrir
            </button>
            <button class="header-btn" id="btnExportar" title="Exportar">
                <i class="fa-regular fa-image"></i> Exportar
            </button>
        </div>
        <div class="header-right">
            <span class="header-user" style="display:flex;align-items:center;gap:8px;color:var(--text-secondary);font-size:0.8rem;">
                <i class="fa-regular fa-circle-user"></i>
                <?php echo htmlspecialchars($nombre_usuario); ?>
            </span>
            <a href="index.php" class="header-exit" title="Salir">
                <i class="fa-solid fa-xmark"></i>
            </a>
        </div>
    </header>

    <!-- BODY -->
    <div class="editor-body">

        <!-- TOOLBAR -->
        <aside class="editor-toolbar" id="toolbar">
            <button class="tool-btn active" data-tool="brush" title="Pincel (B)">
                <i class="fa-solid fa-paintbrush"></i>
                <span class="tool-label">Pincel</span>
            </button>
            <button class="tool-btn" data-tool="pencil" title="Lápiz (P)">
                <i class="fa-solid fa-pencil"></i>
                <span class="tool-label">Lápiz</span>
            </button>
            <button class="tool-btn" data-tool="marker" title="Marcador (M)">
                <i class="fa-solid fa-highlighter"></i>
                <span class="tool-label">Marcador</span>
            </button>
            <button class="tool-btn" data-tool="spray" title="Spray (A)">
                <i class="fa-solid fa-cloud"></i>
                <span class="tool-label">Spray</span>
            </button>
            <button class="tool-btn" data-tool="eraser" title="Borrador (E)">
                <i class="fa-solid fa-eraser"></i>
                <span class="tool-label">Borrador</span>
            </button>
            <div class="tool-divider"></div>
            <button class="tool-btn" data-tool="rect" title="Rectángulo (R)">
                <i class="fa-regular fa-square"></i>
                <span class="tool-label">Rect.</span>
            </button>
            <button class="tool-btn" data-tool="circle" title="Círculo (C)">
                <i class="fa-regular fa-circle"></i>
                <span class="tool-label">Círculo</span>
            </button>
            <button class="tool-btn" data-tool="triangle" title="Triángulo (T)">
                <i class="fa-regular fa-triangle"></i>
                <span class="tool-label">Triángulo</span>
            </button>
            <button class="tool-btn" data-tool="star" title="Estrella (S)">
                <i class="fa-regular fa-star"></i>
                <span class="tool-label">Estrella</span>
            </button>
            <button class="tool-btn" data-tool="heart" title="Corazón (H)">
                <i class="fa-solid fa-heart"></i>
                <span class="tool-label">Corazón</span>
            </button>
            <div class="tool-divider"></div>
            <button class="tool-btn" data-tool="text" title="Texto (T)">
                <i class="fa-solid fa-font"></i>
                <span class="tool-label">Texto</span>
            </button>
            <button class="tool-btn" data-tool="fill" title="Relleno (G)">
                <i class="fa-solid fa-fill-drip"></i>
                <span class="tool-label">Relleno</span>
            </button>
            <button class="tool-btn" data-tool="gradient" title="Degradado (D)">
                <i class="fa-solid fa-rainbow"></i>
                <span class="tool-label">Degradado</span>
            </button>
            <button class="tool-btn" data-tool="picker" title="Cuentagotas (I)">
                <i class="fa-solid fa-eyedropper"></i>
                <span class="tool-label">Color</span>
            </button>
        </aside>

        <!-- CANVAS -->
        <main class="editor-canvas-area">
            <div class="editor-viewport" id="viewport">
                <canvas id="mainCanvas" width="1100" height="700"></canvas>
                <div class="cursor-preview" id="cursorPreview"></div>
            </div>
        </main>

        <!-- PANELS -->
        <aside class="editor-panels" id="panels">

            <!-- CAPAS -->
            <div class="panel layers-panel">
                <div class="panel-header">
                    <h3><i class="fa-regular fa-layer-group"></i> Capas</h3>
                    <div class="panel-actions">
                        <button class="panel-btn" id="btnAddLayer" title="Añadir capa"><i class="fa-solid fa-plus"></i></button>
                        <button class="panel-btn" id="btnDeleteLayer" title="Eliminar capa"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                </div>
                <div class="layers-list" id="layersList"></div>
            </div>

            <!-- HISTORIAL -->
            <div class="panel history-panel">
                <div class="panel-header">
                    <h3><i class="fa-regular fa-clock"></i> Historial</h3>
                    <span class="history-count" id="historyCount">0 pasos</span>
                </div>
                <div class="history-list" id="historyList"></div>
            </div>

            <!-- PROPIEDADES -->
            <div class="panel properties-panel">
                <div class="panel-header">
                    <h3><i class="fa-regular fa-sliders"></i> Propiedades</h3>
                </div>
                <div class="properties-content" id="propertiesContent">
                    <div class="prop-group">
                        <label class="prop-label">Color</label>
                        <div class="color-row">
                            <input type="color" id="colorPicker" value="#6C63FF" class="color-input">
                            <input type="color" id="colorSecondary" value="#ffffff" class="color-input">
                        </div>
                    </div>
                    <div class="prop-group">
                        <label class="prop-label">Tamaño</label>
                        <div class="slider-row">
                            <input type="range" id="sizeSlider" min="1" max="80" value="6" class="prop-slider">
                            <span class="slider-value" id="sizeValue">6px</span>
                        </div>
                    </div>
                    <div class="prop-group">
                        <label class="prop-label">Opacidad</label>
                        <div class="slider-row">
                            <input type="range" id="opacitySlider" min="5" max="100" value="100" class="prop-slider">
                            <span class="slider-value" id="opacityValue">100%</span>
                        </div>
                    </div>
                    <div class="prop-group">
                        <label class="prop-label">Modo</label>
                        <div class="mode-row">
                            <button class="mode-btn active" data-fill="false">▢ Contorno</button>
                            <button class="mode-btn" data-fill="true">■ Relleno</button>
                        </div>
                    </div>
                </div>
            </div>

        </aside>
    </div>

    <!-- FOOTER -->
    <footer class="editor-footer">
        <div class="footer-left">
            <span class="footer-item" id="coordsDisplay"><i class="fa-regular fa-location-dot"></i> X: 0, Y: 0</span>
            <span class="footer-divider">|</span>
            <span class="footer-item" id="dimensionsDisplay"><i class="fa-regular fa-arrow-up-right-from-square"></i> 1100 × 700</span>
            <span class="footer-divider">|</span>
            <span class="footer-item" id="toolDisplay"><i class="fa-regular fa-paintbrush"></i> <span id="currentToolName">Pincel</span></span>
        </div>
        <div class="footer-right">
            <button class="zoom-btn" id="zoomOut">−</button>
            <span class="zoom-value" id="zoomDisplay">100%</span>
            <button class="zoom-btn" id="zoomIn">+</button>
            <button class="zoom-btn" id="zoomFit">⤢</button>
        </div>
    </footer>

</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- ============================================================ -->
<!-- SCRIPTS                                                      -->
<!-- ============================================================ -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- ===== CORE ===== -->
<script src="JavaScript/editor/canvas.js"></script>
<script src="JavaScript/editor/layers.js"></script>
<script src="JavaScript/editor/history.js"></script>
<script src="JavaScript/editor/ui.js"></script>
<script src="JavaScript/editor/storage.js"></script>

<!-- ===== TOOLS ===== -->
<script src="JavaScript/editor/tools/brush.js"></script>
<script src="JavaScript/editor/tools/shapes.js"></script>
<script src="JavaScript/editor/tools/selection.js"></script>
<script src="JavaScript/editor/tools/text.js"></script>
<script src="JavaScript/editor/tools/fill.js"></script>
<script src="JavaScript/editor/tools/gradient.js"></script>
<script src="JavaScript/editor/tools/picker.js"></script>

<!-- ===== CORE PRINCIPAL (siempre al final) ===== -->
<script src="JavaScript/editor/core.js"></script>

<!-- ===== INICIALIZACIÓN ===== -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log("🚀 Inicializando ArtStudio...");
    try {
        const editor = new Editor();
        editor.init();
        window._editor = editor;
        
        // Pasar el ID del usuario a storage
        if (window._editor && window._editor.storage) {
            window._editor.storage.usuarioId = <?php echo $usuario_id; ?>;
        }
        
        console.log("✅ Editor iniciado correctamente");
        console.log("👤 Usuario ID:", <?php echo $usuario_id; ?>);
    } catch (error) {
        console.error("❌ Error al iniciar el editor:", error);
    }
});
</script>

</body>
</html>