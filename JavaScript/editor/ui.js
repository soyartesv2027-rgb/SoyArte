/**
 * ============================================================
 * ui.js - Interfaz de usuario del editor
 * Maneja paneles, propiedades, modales y renderizado de UI
 * ============================================================
 */

class UIManager {
  constructor(editor) {
    this.editor = editor;
    this.dom = editor.dom;

    // Estado de paneles
    this.panels = {
      layers: true,
      history: true,
      properties: true,
    };

    // Estado móvil
    this.isMobile = window.innerWidth < 768;
  }

  // ============================================================
  // INICIALIZACIÓN
  // ============================================================

  init() {
    this.setupMobileToggle();
    this.setupPanelResize();
    this.renderAll();
  }

  // ============================================================
  // RENDERIZAR TODO
  // ============================================================

  renderAll() {
    this.renderLayers();
    this.renderHistory();
    this.renderProperties();
    this.renderToolbar();
    this.updateModals();
  }

  // ============================================================
  // RENDERIZAR CAPAS
  // ============================================================

  renderLayers() {
    const container = this.dom.layersList;
    if (!container) return;

    const layers = this.editor.layers.layers;
    const activeIndex = this.editor.layers.activeIndex;

    container.innerHTML = "";

    // Mostrar capas de arriba a abajo (invertido)
    for (let i = layers.length - 1; i >= 0; i--) {
      const layer = layers[i];
      const isActive = i === activeIndex;

      const item = document.createElement("div");
      item.className = `layer-item ${isActive ? "active" : ""} ${layer.locked ? "locked" : ""}`;
      item.dataset.index = i;

      // Visibilidad
      const visibility = document.createElement("span");
      visibility.className = "layer-visibility";
      visibility.innerHTML = layer.visible ? "👁" : "🚫";
      visibility.title = layer.visible ? "Ocultar capa" : "Mostrar capa";
      visibility.addEventListener("click", (e) => {
        e.stopPropagation();
        this.editor.layers.toggleVisibility(i);
      });

      // Miniatura
      const thumb = document.createElement("div");
      thumb.className = "layer-thumbnail";
      if (layer.thumbnail) {
        thumb.innerHTML = `<img src="${layer.thumbnail}" alt="${layer.name}">`;
      } else {
        thumb.style.background = "#2d2b38";
        thumb.textContent = "🎨";
        thumb.style.display = "flex";
        thumb.style.alignItems = "center";
        thumb.style.justifyContent = "center";
        thumb.style.fontSize = "0.7rem";
      }

      // Nombre
      const name = document.createElement("span");
      name.className = "layer-name";
      name.textContent = layer.name;

      // Opacidad
      const opacity = document.createElement("span");
      opacity.className = "layer-opacity";
      opacity.textContent = Math.round(layer.opacity * 100) + "%";

      // Bloqueo
      const lock = document.createElement("span");
      lock.className = "layer-lock";
      lock.innerHTML = layer.locked ? "🔒" : "🔓";
      lock.title = layer.locked ? "Desbloquear capa" : "Bloquear capa";
      lock.addEventListener("click", (e) => {
        e.stopPropagation();
        this.editor.layers.toggleLock(i);
      });

      item.appendChild(visibility);
      item.appendChild(thumb);
      item.appendChild(name);
      item.appendChild(opacity);
      item.appendChild(lock);

      // Click para activar
      item.addEventListener("click", () => {
        this.editor.layers.setActiveLayer(i);
      });

      // Drag para reordenar (simple)
      item.addEventListener("dblclick", () => {
        if (i < layers.length - 1) {
          this.editor.layers.moveLayerUp(i);
        } else {
          this.editor.layers.moveLayerDown(i);
        }
      });

      container.appendChild(item);
    }
  }

  // ============================================================
  // RENDERIZAR HISTORIAL
  // ============================================================

  renderHistory() {
    const container = this.dom.historyList;
    if (!container) return;

    const states = this.editor.history.states;
    const currentIndex = this.editor.history.currentIndex;

    container.innerHTML = "";

    // Mostrar estados del más reciente al más antiguo
    for (let i = states.length - 1; i >= 0; i--) {
      const state = states[i];
      const isActive = i === currentIndex;

      const item = document.createElement("div");
      item.className = `history-item ${isActive ? "active" : ""}`;
      item.dataset.index = i;

      // Miniatura
      const thumb = document.createElement("div");
      thumb.className = "history-thumbnail";
      if (state.thumbnail) {
        thumb.innerHTML = `<img src="${state.thumbnail}" alt="Miniatura">`;
      } else {
        thumb.style.background = "#2d2b38";
        thumb.style.display = "flex";
        thumb.style.alignItems = "center";
        thumb.style.justifyContent = "center";
        thumb.textContent = "🎨";
        thumb.style.fontSize = "0.6rem";
      }

      // Información
      const info = document.createElement("div");
      info.className = "history-info";

      const action = document.createElement("span");
      action.className = "history-action";
      action.textContent = state.action;

      const time = document.createElement("span");
      time.className = "history-time";
      time.textContent = this.formatTime(state.timestamp);

      info.appendChild(action);
      info.appendChild(time);

      item.appendChild(thumb);
      item.appendChild(info);

      // Click para ir al estado
      item.addEventListener("click", () => {
        this.editor.history.goTo(i);
      });

      container.appendChild(item);
    }

    // Actualizar contador
    if (this.dom.historyCount) {
      this.dom.historyCount.textContent = states.length + " pasos";
    }
  }

  // ============================================================
  // RENDERIZAR PROPIEDADES
  // ============================================================

  renderProperties() {
    const container = this.dom.propertiesContent;
    if (!container) return;

    const tool = this.editor.state.currentTool;
    const state = this.editor.state;

    // Mantener la estructura básica pero actualizar valores
    // Color
    const colorInput = container.querySelector("#colorPicker");
    if (colorInput) colorInput.value = state.color;

    const colorSecondary = container.querySelector("#colorSecondary");
    if (colorSecondary) colorSecondary.value = state.secondaryColor;

    // Tamaño
    const sizeSlider = container.querySelector("#sizeSlider");
    const sizeValue = container.querySelector("#sizeValue");
    if (sizeSlider) sizeSlider.value = state.size;
    if (sizeValue) sizeValue.textContent = state.size + "px";

    // Opacidad
    const opacitySlider = container.querySelector("#opacitySlider");
    const opacityValue = container.querySelector("#opacityValue");
    if (opacitySlider) opacitySlider.value = Math.round(state.opacity * 100);
    if (opacityValue)
      opacityValue.textContent = Math.round(state.opacity * 100) + "%";

    // Modo (relleno/contorno)
    const modeBtns = container.querySelectorAll(".mode-btn");
    modeBtns.forEach((btn) => {
      btn.classList.toggle(
        "active",
        btn.dataset.fill === String(state.fillMode),
      );
    });

    // Mostrar propiedades específicas según la herramienta
    this.renderToolProperties(tool);
  }

  renderToolProperties(tool) {
    const container = this.dom.propertiesContent;
    if (!container) return;

    // Eliminar propiedades específicas de herramienta anteriores
    const existing = container.querySelector(".tool-properties");
    if (existing) existing.remove();

    // Crear contenedor para propiedades de herramienta
    const toolProps = document.createElement("div");
    toolProps.className = "tool-properties";

    // Propiedades según herramienta
    switch (tool) {
      case "text":
        toolProps.innerHTML = `
                    <div class="prop-group">
                        <label class="prop-label">Fuente</label>
                        <select id="textFont" class="modal-select" style="width:100%;">
                            <option value="Arial">Arial</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Impact">Impact</option>
                            <option value="Times New Roman">Times New Roman</option>
                        </select>
                    </div>
                    <div class="prop-group">
                        <label class="prop-label">Tamaño de texto</label>
                        <div class="slider-row">
                            <input type="range" id="textSize" min="8" max="120" value="32" class="prop-slider">
                            <span class="slider-value" id="textSizeValue">32</span>
                        </div>
                    </div>
                `;
        break;

      case "spray":
        toolProps.innerHTML = `
                    <div class="prop-group">
                        <label class="prop-label">Densidad</label>
                        <div class="slider-row">
                            <input type="range" id="sprayDensity" min="10" max="100" value="50" class="prop-slider">
                            <span class="slider-value" id="sprayDensityValue">50</span>
                        </div>
                    </div>
                `;
        break;

      case "gradient":
        toolProps.innerHTML = `
                    <div class="prop-group">
                        <label class="prop-label">Tipo de degradado</label>
                        <select id="gradientType" class="modal-select" style="width:100%;">
                            <option value="linear">Lineal</option>
                            <option value="radial">Radial</option>
                        </select>
                    </div>
                `;
        break;

      default:
        // No hay propiedades específicas
        break;
    }

    if (toolProps.innerHTML) {
      container.appendChild(toolProps);

      // Configurar eventos para las propiedades específicas
      this.setupToolProperties(tool);
    }
  }

  setupToolProperties(tool) {
    switch (tool) {
      case "text":
        const textSize = document.getElementById("textSize");
        const textSizeValue = document.getElementById("textSizeValue");
        if (textSize && textSizeValue) {
          textSize.addEventListener("input", () => {
            textSizeValue.textContent = textSize.value;
            this.editor.state.textSize = parseInt(textSize.value);
          });
        }
        break;

      case "spray":
        const sprayDensity = document.getElementById("sprayDensity");
        const sprayDensityValue = document.getElementById("sprayDensityValue");
        if (sprayDensity && sprayDensityValue) {
          sprayDensity.addEventListener("input", () => {
            sprayDensityValue.textContent = sprayDensity.value;
            this.editor.state.sprayDensity = parseInt(sprayDensity.value);
          });
        }
        break;
    }
  }

  // ============================================================
  // RENDERIZAR TOOLBAR
  // ============================================================

  renderToolbar() {
    const buttons = this.dom.toolButtons;
    const currentTool = this.editor.state.currentTool;

    buttons.forEach((btn) => {
      btn.classList.toggle("active", btn.dataset.tool === currentTool);
    });

    // Actualizar nombre de herramienta en footer
    const toolNames = {
      brush: "Pincel",
      pencil: "Lápiz",
      marker: "Marcador",
      spray: "Spray",
      eraser: "Borrador",
      select: "Selección",
      rect: "Rectángulo",
      circle: "Círculo",
      triangle: "Triángulo",
      star: "Estrella",
      heart: "Corazón",
      text: "Texto",
      fill: "Relleno",
      gradient: "Degradado",
      picker: "Cuentagotas",
      sticker: "Stickers",
    };

    if (this.dom.toolDisplay) {
      this.dom.toolDisplay.textContent = toolNames[currentTool] || currentTool;
    }
  }

  // ============================================================
  // MODALES
  // ============================================================

  updateModals() {
    // Cerrar modales al hacer clic fuera
    document.querySelectorAll(".modal-overlay").forEach((modal) => {
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.classList.remove("active");
        }
      });
    });

    // Cerrar con ESC
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        document.querySelectorAll(".modal-overlay.active").forEach((modal) => {
          modal.classList.remove("active");
        });
      }
    });
  }

  showModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.add("active");
      document.body.style.overflow = "hidden";
    }
  }

  hideModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.remove("active");
      document.body.style.overflow = "";
    }
  }

  // ============================================================
  // MÓVIL
  // ============================================================

  setupMobileToggle() {
    // Detectar si es móvil
    this.isMobile = window.innerWidth < 768;

    // Botón para abrir/cerrar paneles en móvil
    if (this.isMobile) {
      const toggleBtn = document.createElement("button");
      toggleBtn.className = "mobile-panel-toggle";
      toggleBtn.innerHTML = "📂";
      toggleBtn.title = "Mostrar paneles";
      toggleBtn.style.cssText = `
                position: fixed;
                bottom: 60px;
                right: 20px;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: var(--color-primary);
                color: white;
                border: none;
                font-size: 1.2rem;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(108, 99, 255, 0.4);
                z-index: 30;
                display: none;
            `;

      if (window.innerWidth < 768) {
        toggleBtn.style.display = "flex";
        toggleBtn.style.alignItems = "center";
        toggleBtn.style.justifyContent = "center";
      }

      toggleBtn.addEventListener("click", () => {
        const panels = document.querySelector(".editor-panels");
        if (panels) {
          panels.classList.toggle("mobile-open");
          toggleBtn.innerHTML = panels.classList.contains("mobile-open")
            ? "✕"
            : "📂";
        }
      });

      document.body.appendChild(toggleBtn);

      // Mostrar/ocultar en resize
      window.addEventListener("resize", () => {
        const isMobile = window.innerWidth < 768;
        toggleBtn.style.display = isMobile ? "flex" : "none";
        if (!isMobile) {
          const panels = document.querySelector(".editor-panels");
          if (panels) panels.classList.remove("mobile-open");
        }
      });
    }
  }

  setupPanelResize() {
    // Permitir redimensionar paneles (simple)
    const panels = document.querySelector(".editor-panels");
    if (!panels) return;

    let isResizing = false;
    let startX = 0;
    let startWidth = 0;

    const resizeHandle = document.createElement("div");
    resizeHandle.className = "panel-resize-handle";
    resizeHandle.style.cssText = `
            position: absolute;
            left: -4px;
            top: 0;
            width: 8px;
            height: 100%;
            cursor: ew-resize;
            z-index: 10;
        `;

    // Solo en escritorio
    if (window.innerWidth >= 768) {
      panels.style.position = "relative";
      panels.appendChild(resizeHandle);

      resizeHandle.addEventListener("mousedown", (e) => {
        isResizing = true;
        startX = e.clientX;
        startWidth = panels.offsetWidth;
        document.body.style.cursor = "ew-resize";
        document.body.style.userSelect = "none";
      });

      document.addEventListener("mousemove", (e) => {
        if (!isResizing) return;
        const diff = startX - e.clientX;
        const newWidth = Math.max(200, Math.min(400, startWidth + diff));
        panels.style.width = newWidth + "px";
      });

      document.addEventListener("mouseup", () => {
        if (isResizing) {
          isResizing = false;
          document.body.style.cursor = "";
          document.body.style.userSelect = "";
        }
      });
    }
  }

  // ============================================================
  // UTILIDADES
  // ============================================================

  formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) {
      return "Ahora";
    } else if (diff < 3600000) {
      const mins = Math.floor(diff / 60000);
      return mins + "m";
    } else if (diff < 86400000) {
      const hours = Math.floor(diff / 3600000);
      return hours + "h";
    } else {
      return date.toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
      });
    }
  }

  // ============================================================
  // TOAST (Notificaciones)
  // ============================================================

  showToast(message, type = "info") {
    const toast = this.dom.toast;
    if (!toast) return;

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

// Hacer disponible globalmente
window.UIManager = UIManager;
