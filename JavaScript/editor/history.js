/**
 * ============================================================
 * history.js - Sistema de historial del editor
 * ============================================================
 */

class HistoryManager {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;

    this.states = [];
    this.currentIndex = -1;
    this.maxStates = 50;
    this.isRestoring = false;
    this._idCounter = 0;
  }

  init() {
    this.clear();
    this.saveState("Inicio");
    this.renderList();
  }

  clear() {
    this.states = [];
    this.currentIndex = -1;
    this._idCounter = 0;
    this.renderList();
    this.updateCounter();
  }

  saveState(action = "Acción") {
    if (this.isRestoring) return;

    if (this.currentIndex < this.states.length - 1) {
      this.states = this.states.slice(0, this.currentIndex + 1);
    }

    const layersData = [];
    if (this.layers && this.layers.layers) {
      for (const layer of this.layers.layers) {
        layersData.push({
          name: layer.name,
          visible: layer.visible,
          locked: layer.locked,
          opacity: layer.opacity,
          imageData: layer.imageData
            ? new Uint8ClampedArray(layer.imageData.data)
            : null,
        });
      }
    }

    const thumbnail = this.generateThumbnail();

    const state = {
      id: ++this._idCounter,
      action: action,
      timestamp: Date.now(),
      layers: layersData,
      thumbnail: thumbnail,
    };

    this.states.push(state);

    if (this.states.length > this.maxStates) {
      this.states.shift();
    }

    this.currentIndex = this.states.length - 1;

    this.renderList();
    this.updateButtons();
    this.updateCounter();

    return state;
  }

  undo() {
    if (this.currentIndex <= 0) {
      if (this.editor)
        this.editor.showToast("No hay más pasos para deshacer", "info");
      return false;
    }

    this.currentIndex--;
    this.restoreState(this.currentIndex);
    this.renderList();
    this.updateButtons();
    this.updateCounter();

    return true;
  }

  redo() {
    if (this.currentIndex >= this.states.length - 1) {
      if (this.editor)
        this.editor.showToast("No hay más pasos para rehacer", "info");
      return false;
    }

    this.currentIndex++;
    this.restoreState(this.currentIndex);
    this.renderList();
    this.updateButtons();
    this.updateCounter();

    return true;
  }

  goTo(index) {
    if (index < 0 || index >= this.states.length) return false;
    if (index === this.currentIndex) return true;

    this.currentIndex = index;
    this.restoreState(this.currentIndex);
    this.renderList();
    this.updateButtons();
    this.updateCounter();

    return true;
  }

  restoreState(index) {
    if (index < 0 || index >= this.states.length) return;

    this.isRestoring = true;

    const state = this.states[index];
    const layersData = state.layers;

    if (this.layers && this.layers.layers) {
      for (
        let i = 0;
        i < this.layers.layers.length && i < layersData.length;
        i++
      ) {
        const layer = this.layers.layers[i];
        const data = layersData[i];

        if (data) {
          layer.name = data.name || layer.name;
          layer.visible =
            data.visible !== undefined ? data.visible : layer.visible;
          layer.locked = data.locked || false;
          layer.opacity = data.opacity || 1;

          if (data.imageData && this.ctx) {
            try {
              const imageData = this.ctx.createImageData(
                this.canvas.width,
                this.canvas.height,
              );
              const srcData = data.imageData;
              const dstData = imageData.data;
              const len = Math.min(srcData.length, dstData.length);
              for (let j = 0; j < len; j++) {
                dstData[j] = srcData[j];
              }
              layer.imageData = imageData;
            } catch (e) {
              console.warn("Error restaurando imagen de capa:", e);
            }
          }
        }
      }
    }

    if (this.layers) {
      this.layers.render();
      if (this.layers.renderThumbnails) this.layers.renderThumbnails();
    }

    this.isRestoring = false;
  }

  generateThumbnail() {
    try {
      const width = 60;
      const height = 40;
      const canvas = document.createElement("canvas");
      canvas.width = width;
      canvas.height = height;
      const ctx = canvas.getContext("2d");
      if (this.canvas) {
        ctx.drawImage(this.canvas, 0, 0, width, height);
      }
      return canvas.toDataURL("image/png");
    } catch (e) {
      return "";
    }
  }

  updateButtons() {
    const undoBtn =
      document.getElementById("btnUndo") ||
      document.querySelector('.header-btn[title="Deshacer"]');
    const redoBtn =
      document.getElementById("btnRedo") ||
      document.querySelector('.header-btn[title="Rehacer"]');

    if (undoBtn) {
      undoBtn.disabled = this.currentIndex <= 0;
      undoBtn.style.opacity = this.currentIndex <= 0 ? "0.4" : "1";
    }

    if (redoBtn) {
      redoBtn.disabled = this.currentIndex >= this.states.length - 1;
      redoBtn.style.opacity =
        this.currentIndex >= this.states.length - 1 ? "0.4" : "1";
    }
  }

  updateCounter() {
    const el = document.getElementById("historyCount");
    if (el) {
      el.textContent = this.states.length + " pasos";
    }
  }

  renderList() {
    const container = document.getElementById("historyList");
    if (!container) {
      // Si no existe, intentar crearlo
      const historyPanel = document.querySelector(".history-panel");
      if (historyPanel) {
        const list = document.createElement("div");
        list.className = "history-list";
        list.id = "historyList";
        historyPanel.appendChild(list);
        this.renderList();
      }
      return;
    }

    container.innerHTML = "";

    if (this.states.length === 0) {
      container.innerHTML =
        '<div class="history-empty" style="padding:10px;text-align:center;color:var(--text-muted);font-size:0.7rem;">No hay acciones</div>';
      return;
    }

    for (let i = this.states.length - 1; i >= 0; i--) {
      const state = this.states[i];
      const isActive = i === this.currentIndex;

      const item = document.createElement("div");
      item.className = `history-item ${isActive ? "active" : ""}`;
      item.dataset.index = i;

      const thumb = document.createElement("div");
      thumb.className = "history-thumbnail";
      if (state.thumbnail) {
        thumb.innerHTML = `<img src="${state.thumbnail}" alt="Miniatura">`;
      } else {
        thumb.style.cssText =
          "background:#2d2b38;display:flex;align-items:center;justify-content:center;";
        thumb.textContent = "🎨";
        thumb.style.fontSize = "0.6rem";
      }

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

      item.addEventListener("click", () => {
        this.goTo(i);
      });

      container.appendChild(item);
    }
  }

  formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return "Ahora";
    if (diff < 3600000) return Math.floor(diff / 60000) + "m";
    if (diff < 86400000) return Math.floor(diff / 3600000) + "h";
    return date.toLocaleDateString("es-ES", {
      day: "2-digit",
      month: "2-digit",
    });
  }

  loadFromData(data) {
    this.clear();
    if (!data || !data.length) {
      this.saveState("Inicio");
      return;
    }
    for (const stateData of data) {
      const state = {
        id: ++this._idCounter,
        action: stateData.action || "Acción",
        timestamp: stateData.timestamp || Date.now(),
        layers: stateData.layers || [],
        thumbnail: stateData.thumbnail || "",
      };
      this.states.push(state);
    }
    this.currentIndex = this.states.length - 1;
    this.renderList();
    this.updateButtons();
    this.updateCounter();
  }

  exportData() {
    return this.states.map((state) => ({
      action: state.action,
      timestamp: state.timestamp,
      layers: state.layers,
      thumbnail: state.thumbnail,
    }));
  }

  getCurrentState() {
    return this.states[this.currentIndex] || null;
  }

  getStateCount() {
    return this.states.length;
  }
}

window.HistoryManager = HistoryManager;
