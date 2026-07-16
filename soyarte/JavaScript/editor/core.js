/**
 * ============================================================
 * core.js - Controlador principal del editor
 * ============================================================
 */

class Editor {
  constructor() {
    // ===== REFERENCIAS A MÓDULOS =====
    this.canvas = null;
    this.layers = null;
    this.history = null;
    this.ui = null;
    this.storage = null;

    // ===== HERRAMIENTAS =====
    this.tools = {};

    // ===== ESTADO GLOBAL =====
    this.state = {
      currentTool: "brush",
      color: "#6C63FF",
      secondaryColor: "#ffffff",
      size: 6,
      opacity: 1,
      fillMode: false,
      zoom: 1,
      isDrawing: false,
      startX: 0,
      startY: 0,
      lastX: 0,
      lastY: 0,
      projectId: null,
      projectName: "Sin título",
      isModified: false,
    };

    // ===== CONFIGURACIÓN =====
    this.config = {
      maxLayers: 20,
      maxHistory: 50,
      canvasWidth: 1100,
      canvasHeight: 700,
      autosaveInterval: 30000,
    };

    // ===== ELEMENTOS DOM =====
    this.dom = {};
  }

  // ============================================================
  // INICIALIZACIÓN
  // ============================================================

  init() {
    console.log("🎨 ArtStudio Editor iniciando...");

    this.captureDomElements();
    this.canvas = new CanvasManager(this);
    this.layers = new LayerManager(this);
    this.history = new HistoryManager(this);
    this.ui = new UIManager(this);
    this.storage = new StorageManager(this);

    this.initTools();
    this.setupEvents();
    this.setupShortcuts();

    this.createNewProject(
      "Mi obra",
      this.config.canvasWidth,
      this.config.canvasHeight,
    );

    this.updateUI();
    if (this.ui) this.ui.renderAll();

    console.log("✅ Editor listo!");
  }

  // ============================================================
  // DOM - CON VERIFICACIÓN DE EXISTENCIA
  // ============================================================

  captureDomElements() {
    const safe = (id) => document.getElementById(id);

    this.dom = {
      canvas: safe("mainCanvas"),
      viewport: safe("viewport"),
      cursor: safe("cursorPreview"),

      toolbar: safe("toolbar"),
      toolButtons: document.querySelectorAll(".tool-btn"),

      layersList: safe("layersList"),
      historyList: safe("historyList"),
      propertiesContent: safe("propertiesContent"),

      colorPicker: safe("colorPicker"),
      colorSecondary: safe("colorSecondary"),
      sizeSlider: safe("sizeSlider"),
      sizeValue: safe("sizeValue"),
      opacitySlider: safe("opacitySlider"),
      opacityValue: safe("opacityValue"),
      modeButtons: document.querySelectorAll(".mode-btn"),

      coordsDisplay: safe("coordsDisplay"),
      dimensionsDisplay: safe("dimensionsDisplay"),
      toolDisplay: safe("currentToolName"),
      zoomDisplay: safe("zoomDisplay"),

      btnNuevo: safe("btnNuevo"),
      btnGuardar: safe("btnGuardar"),
      btnAbrir: safe("btnAbrir"),
      btnExportar: safe("btnExportar"),
      btnAddLayer: safe("btnAddLayer"),
      btnDeleteLayer: safe("btnDeleteLayer"),
      zoomIn: safe("zoomIn"),
      zoomOut: safe("zoomOut"),
      zoomFit: safe("zoomFit"),

      toast: safe("toast"),
    };

    if (!document.getElementById("historyCount")) {
      const historyPanel = document.querySelector(
        ".history-panel .panel-header",
      );
      if (historyPanel) {
        const el = document.createElement("span");
        el.className = "history-count";
        el.id = "historyCount";
        el.textContent = "0 pasos";
        historyPanel.appendChild(el);
      }
    }
  }

  // ============================================================
  // HERRAMIENTAS
  // ============================================================

  initTools() {
    const toolClasses = {
      brush: BrushTool,
      pencil: BrushTool,
      marker: BrushTool,
      spray: BrushTool,
      eraser: BrushTool,
      rect: ShapesTool,
      circle: ShapesTool,
      triangle: ShapesTool,
      star: ShapesTool,
      heart: ShapesTool,
      text: TextTool,
      fill: FillTool,
      gradient: GradientTool,
      picker: PickerTool,
      select: null,
    };

    for (const [name, ToolClass] of Object.entries(toolClasses)) {
      if (ToolClass) {
        try {
          this.tools[name] = new ToolClass(this);
        } catch (e) {
          console.warn(`⚠️ Herramienta "${name}" no disponible:`, e.message);
        }
      }
    }

    console.log("🛠️ Herramientas inicializadas:", Object.keys(this.tools));
  }

  getCurrentTool() {
    return this.tools[this.state.currentTool] || null;
  }

  setTool(toolName) {
    if (!this.tools[toolName]) {
      console.warn(`⚠️ Herramienta "${toolName}" no encontrada`);
      return;
    }

    this.state.currentTool = toolName;

    document.querySelectorAll(".tool-btn").forEach((btn) => {
      btn.classList.toggle("active", btn.dataset.tool === toolName);
    });

    const toolNames = {
      brush: "Pincel",
      pencil: "Lápiz",
      marker: "Marcador",
      spray: "Spray",
      eraser: "Borrador",
      rect: "Rectángulo",
      circle: "Círculo",
      triangle: "Triángulo",
      star: "Estrella",
      heart: "Corazón",
      text: "Texto",
      fill: "Relleno",
      gradient: "Degradado",
      picker: "Cuentagotas",
    };

    const display = document.getElementById("currentToolName");
    if (display) display.textContent = toolNames[toolName] || toolName;

    this.updateCursor();
  }

  // ============================================================
  // PROYECTO
  // ============================================================

  createNewProject(name, width, height) {
    if (!this.canvas) {
      console.error("❌ Canvas no inicializado");
      return;
    }

    this.canvas.resize(width || 1100, height || 700);
    this.canvas.clear("#ffffff");

    if (this.layers) {
      this.layers.clear();
      this.layers.addLayer("Fondo");
    }

    if (this.history) {
      this.history.clear();
      this.history.saveState("inicio");
    }

    this.state.projectName = name || "Sin título";
    this.state.projectId = null;
    this.state.isModified = false;

    // ===== ACTUALIZAR EL INPUT DE TÍTULO =====
    const tituloInput = document.getElementById("titleInput");
    if (tituloInput) {
      tituloInput.value = name || "Mi obra";
    }

    this.updateUI();
    if (this.ui) this.ui.renderAll();

    this.showToast("🎨 Nuevo proyecto creado", "success");
  }

  // ============================================================
  // ZOOM
  // ============================================================

  changeZoom(delta) {
    this.state.zoom = Math.min(4, Math.max(0.15, this.state.zoom + delta));
    this.applyZoom();
  }

  applyZoom() {
    const canvas = this.dom.canvas;
    if (!canvas) return;

    canvas.style.transform = `scale(${this.state.zoom})`;
    canvas.style.transformOrigin = "top left";

    const viewport = this.dom.viewport;
    if (viewport) {
      viewport.style.width = canvas.width * this.state.zoom + "px";
      viewport.style.height = canvas.height * this.state.zoom + "px";
    }

    const display = this.dom.zoomDisplay;
    if (display) display.textContent = Math.round(this.state.zoom * 100) + "%";
  }

  zoomFit() {
    const wrapper =
      document.querySelector(".editor-canvas-area") ||
      this.dom.viewport?.parentElement;
    if (!wrapper) return;

    const rect = wrapper.getBoundingClientRect();
    const pad = 40;
    const canvas = this.dom.canvas;
    if (!canvas) return;

    const zx = (rect.width - pad) / canvas.width;
    const zy = (rect.height - pad) / canvas.height;
    this.state.zoom = Math.min(Math.min(zx, zy, 1), 1);
    this.applyZoom();
  }

  // ============================================================
  // CURSOR
  // ============================================================

  updateCursor() {
    const cursor = this.dom.cursor;
    if (!cursor) return;

    const show = ["brush", "pencil", "marker", "spray", "eraser"].includes(
      this.state.currentTool,
    );
    if (!show) {
      cursor.style.display = "none";
      return;
    }

    const size = this.state.size * this.state.zoom;
    cursor.style.display = "block";
    cursor.style.width = Math.max(size * 1.5, 4) + "px";
    cursor.style.height = Math.max(size * 1.5, 4) + "px";
    cursor.style.borderColor =
      this.state.currentTool === "eraser" ? "#fc5c7c" : this.state.color;
    cursor.style.opacity = Math.max(0.3, this.state.opacity);
  }

  // ============================================================
  // UI
  // ============================================================

  updateUI() {
    const canvas = this.dom.canvas;
    if (!canvas) return;

    const dims = this.dom.dimensionsDisplay;
    if (dims) dims.textContent = `${canvas.width} × ${canvas.height}`;

    const zoom = this.dom.zoomDisplay;
    if (zoom) zoom.textContent = Math.round(this.state.zoom * 100) + "%";

    const sizeVal = this.dom.sizeValue;
    if (sizeVal) sizeVal.textContent = this.state.size + "px";

    const sizeSlider = this.dom.sizeSlider;
    if (sizeSlider) sizeSlider.value = this.state.size;

    const opacityVal = this.dom.opacityValue;
    if (opacityVal)
      opacityVal.textContent = Math.round(this.state.opacity * 100) + "%";

    const opacitySlider = this.dom.opacitySlider;
    if (opacitySlider)
      opacitySlider.value = Math.round(this.state.opacity * 100);

    const colorPicker = this.dom.colorPicker;
    if (colorPicker) colorPicker.value = this.state.color;
  }

  // ============================================================
  // MARK AS MODIFIED
  // ============================================================

  markAsModified() {
    this.state.isModified = true;
    document.title = `* ${this.state.projectName} | ArtStudio`;
  }

  // ============================================================
  // MODAL DE ABRIR PROYECTO
  // ============================================================

  showOpenModal() {
    let modal = document.getElementById("modalAbrir");
    if (!modal) {
      modal = document.createElement("div");
      modal.id = "modalAbrir";
      modal.className = "modal-overlay";
      modal.innerHTML = `
                <div class="modal-content modal-large">
                    <div class="modal-header">
                        <h2><i class="fa-regular fa-folder-open"></i> Mis proyectos</h2>
                        <button class="modal-close" id="closeModalAbrir">✕</button>
                    </div>
                    <div class="modal-body">
                        <div id="listaProyectos" class="proyectos-grid">
                            <div class="loading-proyectos">Cargando proyectos...</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-cancel" id="cancelModalAbrir">Cerrar</button>
                    </div>
                </div>
            `;
      document.body.appendChild(modal);

      document
        .getElementById("closeModalAbrir")
        .addEventListener("click", () => {
          modal.classList.remove("active");
        });
      document
        .getElementById("cancelModalAbrir")
        .addEventListener("click", () => {
          modal.classList.remove("active");
        });
      modal.addEventListener("click", (e) => {
        if (e.target === modal) modal.classList.remove("active");
      });
    }

    modal.classList.add("active");
    if (this.storage) {
      this.storage.loadProjectList();
    }
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  setupEvents() {
    const canvas = this.dom.canvas;
    if (canvas) {
      canvas.addEventListener("mousedown", (e) => this.onMouseDown(e));
      canvas.addEventListener("mousemove", (e) => this.onMouseMove(e));
      canvas.addEventListener("mouseup", (e) => this.onMouseUp(e));
      canvas.addEventListener("mouseleave", (e) => this.onMouseUp(e));

      canvas.addEventListener("touchstart", (e) => this.onTouchStart(e), {
        passive: false,
      });
      canvas.addEventListener("touchmove", (e) => this.onTouchMove(e), {
        passive: false,
      });
      canvas.addEventListener("touchend", (e) => this.onTouchEnd(e), {
        passive: false,
      });
    }

    document.querySelectorAll(".tool-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        this.setTool(btn.dataset.tool);
      });
    });

    const colorPicker = this.dom.colorPicker;
    if (colorPicker) {
      colorPicker.addEventListener("input", (e) => {
        this.state.color = e.target.value;
        this.updateCursor();
      });
    }

    const colorSecondary = this.dom.colorSecondary;
    if (colorSecondary) {
      colorSecondary.addEventListener("input", (e) => {
        this.state.secondaryColor = e.target.value;
      });
    }

    const sizeSlider = this.dom.sizeSlider;
    if (sizeSlider) {
      sizeSlider.addEventListener("input", (e) => {
        this.state.size = parseInt(e.target.value);
        const val = this.dom.sizeValue;
        if (val) val.textContent = this.state.size + "px";
        this.updateCursor();
      });
    }

    const opacitySlider = this.dom.opacitySlider;
    if (opacitySlider) {
      opacitySlider.addEventListener("input", (e) => {
        this.state.opacity = parseInt(e.target.value) / 100;
        const val = this.dom.opacityValue;
        if (val) val.textContent = e.target.value + "%";
      });
    }

    document.querySelectorAll(".mode-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        this.state.fillMode = btn.dataset.fill === "true";
        document
          .querySelectorAll(".mode-btn")
          .forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");
      });
    });

    // ===== BOTONES =====
    const btnNuevo = this.dom.btnNuevo;
    if (btnNuevo) {
      btnNuevo.addEventListener("click", () => {
        this.createNewProject("Mi obra", 1100, 700);
      });
    }

    const btnGuardar = this.dom.btnGuardar;
    if (btnGuardar) {
      btnGuardar.addEventListener("click", () => {
        this.showToast("💾 Guardando...", "info");
        if (this.storage) this.storage.saveToDatabase();
      });
    }

    // ===== BOTÓN ABRIR =====
    const btnAbrir = this.dom.btnAbrir;
    if (btnAbrir) {
      btnAbrir.addEventListener("click", () => {
        this.showOpenModal();
      });
    }

    const btnExportar = this.dom.btnExportar;
    if (btnExportar) {
      btnExportar.addEventListener("click", () => {
        if (this.storage) this.storage.export("png");
      });
    }

    const btnAddLayer = this.dom.btnAddLayer;
    if (btnAddLayer && this.layers) {
      btnAddLayer.addEventListener("click", () => {
        this.layers.addLayer("Capa " + (this.layers.layers.length + 1));
        if (this.ui) this.ui.renderLayers();
        this.showToast("➕ Capa añadida", "info");
      });
    }

    const btnDeleteLayer = this.dom.btnDeleteLayer;
    if (btnDeleteLayer && this.layers) {
      btnDeleteLayer.addEventListener("click", () => {
        if (this.layers.layers.length <= 1) {
          this.showToast("No puedes eliminar la última capa", "warning");
          return;
        }
        this.layers.deleteLayer(this.layers.activeIndex);
        if (this.ui) this.ui.renderLayers();
        this.showToast("🗑️ Capa eliminada", "info");
      });
    }

    const zoomIn = this.dom.zoomIn;
    if (zoomIn) zoomIn.addEventListener("click", () => this.changeZoom(0.1));
    const zoomOut = this.dom.zoomOut;
    if (zoomOut) zoomOut.addEventListener("click", () => this.changeZoom(-0.1));
    const zoomFit = this.dom.zoomFit;
    if (zoomFit) zoomFit.addEventListener("click", () => this.zoomFit());

    window.addEventListener("resize", () => this.zoomFit());

    const viewport = this.dom.viewport;
    if (viewport) {
      viewport.addEventListener(
        "wheel",
        (e) => {
          if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            this.changeZoom(e.deltaY > 0 ? -0.1 : 0.1);
          }
        },
        { passive: false },
      );
    }
  }

  // ============================================================
  // EVENTOS DEL MOUSE
  // ============================================================

  getCanvasCoords(e) {
    const canvas = this.dom.canvas;
    if (!canvas) return { x: 0, y: 0 };

    const rect = canvas.getBoundingClientRect();
    const touch = e.touches ? e.touches[0] : e;
    const x = (touch.clientX - rect.left) * (canvas.width / rect.width);
    const y = (touch.clientY - rect.top) * (canvas.height / rect.height);
    return { x, y };
  }

  onMouseDown(e) {
    e.preventDefault();
    const { x, y } = this.getCanvasCoords(e);

    this.state.isDrawing = true;
    this.state.startX = x;
    this.state.startY = y;
    this.state.lastX = x;
    this.state.lastY = y;

    const tool = this.getCurrentTool();
    if (tool && tool.onMouseDown) {
      tool.onMouseDown(x, y);
    }

    this.updateCoords(x, y);
  }

  onMouseMove(e) {
    const { x, y } = this.getCanvasCoords(e);

    if (this.state.isDrawing) {
      const tool = this.getCurrentTool();
      if (tool && tool.onMouseMove) {
        tool.onMouseMove(x, y);
      }
    }

    this.updateCoords(x, y);
    this.updateCursorPosition(e);
  }

  onMouseUp(e) {
    if (this.state.isDrawing) {
      const tool = this.getCurrentTool();
      if (tool && tool.onMouseUp) {
        tool.onMouseUp();
      }
      if (this.history) {
        const toolName = this.state.currentTool;
        const names = {
          brush: "Pincel",
          pencil: "Lápiz",
          marker: "Marcador",
          spray: "Spray",
          eraser: "Borrador",
          rect: "Rectángulo",
          circle: "Círculo",
          triangle: "Triángulo",
          star: "Estrella",
          heart: "Corazón",
          text: "Texto",
          fill: "Relleno",
          gradient: "Degradado",
        };
        this.history.saveState(names[toolName] || toolName);
      }
    }
    this.state.isDrawing = false;
  }

  // ============================================================
  // EVENTOS TÁCTILES
  // ============================================================

  onTouchStart(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent("mousedown", {
      clientX: touch.clientX,
      clientY: touch.clientY,
    });
    this.onMouseDown(mouseEvent);
  }

  onTouchMove(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent("mousemove", {
      clientX: touch.clientX,
      clientY: touch.clientY,
    });
    this.onMouseMove(mouseEvent);
  }

  onTouchEnd(e) {
    e.preventDefault();
    this.onMouseUp(e);
  }

  // ============================================================
  // COORDENADAS Y CURSOR
  // ============================================================

  updateCoords(x, y) {
    const display = this.dom.coordsDisplay;
    if (display) {
      display.innerHTML = `<i class="fa-regular fa-location-dot"></i> X: ${Math.round(x)}, Y: ${Math.round(y)}`;
    }
  }

  updateCursorPosition(e) {
    const cursor = this.dom.cursor;
    if (!cursor) return;

    const viewport = this.dom.viewport;
    if (!viewport) return;

    const rect = viewport.getBoundingClientRect();
    const touch = e.touches ? e.touches[0] : e;
    const x = touch.clientX - rect.left;
    const y = touch.clientY - rect.top;

    cursor.style.display = "block";
    cursor.style.left = x + "px";
    cursor.style.top = y + "px";
  }

  // ============================================================
  // ATAJOS DE TECLADO
  // ============================================================

  setupShortcuts() {
    document.addEventListener("keydown", (e) => {
      // Si el usuario está escribiendo en un input, no ejecutar atajos
      const activeElement = document.activeElement;
      if (
        activeElement &&
        (activeElement.tagName === "INPUT" ||
          activeElement.tagName === "TEXTAREA" ||
          activeElement.tagName === "SELECT")
      ) {
        return;
      }

      // Ctrl+Z = Deshacer
      if (e.ctrlKey && e.key === "z" && !e.shiftKey) {
        e.preventDefault();
        if (this.history) {
          this.history.undo();
          this.showToast("↶ Deshacer", "info");
        }
        return;
      }

      // Ctrl+Y = Rehacer
      if (
        (e.ctrlKey && e.key === "y") ||
        (e.ctrlKey && e.key === "z" && e.shiftKey)
      ) {
        e.preventDefault();
        if (this.history) {
          this.history.redo();
          this.showToast("↷ Rehacer", "info");
        }
        return;
      }

      // Ctrl+S = Guardar
      if (e.ctrlKey && e.key === "s") {
        e.preventDefault();
        this.showToast("💾 Guardando...", "info");
        if (this.storage) this.storage.saveToDatabase();
        return;
      }

      // ATAJOS DE HERRAMIENTAS
      const toolMap = {
        b: "brush",
        p: "pencil",
        m: "marker",
        a: "spray",
        e: "eraser",
        r: "rect",
        c: "circle",
        t: "triangle",
        s: "star",
        h: "heart",
        g: "fill",
        d: "gradient",
        i: "picker",
      };

      const tool = toolMap[e.key.toLowerCase()];
      if (tool && !e.ctrlKey && !e.metaKey && !e.altKey) {
        e.preventDefault();
        this.setTool(tool);
      }
    });
  }

  // ============================================================
  // TOAST
  // ============================================================

  showToast(message, type = "info") {
    const toast = this.dom.toast;
    if (!toast) {
      console.log(`[${type}] ${message}`);
      return;
    }

    const icons = {
      success: "fa-regular fa-circle-check",
      error: "fa-regular fa-circle-xmark",
      info: "fa-regular fa-circle-info",
      warning: "fa-regular fa-triangle-exclamation",
    };

    toast.className = "toast";
    toast.classList.add(type);
    toast.innerHTML = `<i class="${icons[type] || icons.info}"></i> ${message}`;
    toast.classList.add("show");

    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
      toast.classList.remove("show");
    }, 3000);
  }
}

window.Editor = Editor;
