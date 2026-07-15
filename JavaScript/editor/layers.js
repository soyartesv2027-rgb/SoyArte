/**
 * ============================================================
 * layers.js - Sistema de capas del editor
 * Maneja la creación, eliminación, orden y renderizado de capas
 * ============================================================
 */

class LayerManager {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;

    // Array de capas
    this.layers = [];

    // Índice de la capa activa
    this.activeIndex = 0;

    // Contador de IDs
    this._idCounter = 0;

    // Configuración
    this.maxLayers = editor.config.maxLayers || 20;
  }

  // ============================================================
  // INICIALIZACIÓN
  // ============================================================

  init() {
    this.clear();
    this.addLayer("Fondo");
    this.activeIndex = 0;
    this.render();
  }

  clear() {
    this.layers = [];
    this.activeIndex = 0;
    this._idCounter = 0;
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
  }

  // ============================================================
  // CREAR CAPA
  // ============================================================

  addLayer(name = "Capa") {
    if (this.layers.length >= this.maxLayers) {
      if (this.editor)
        this.editor.showToast(
          "Límite de capas alcanzado (" + this.maxLayers + ")",
          "warning",
        );
      return null;
    }

    // Crear imagen de la capa (vacía con fondo transparente)
    const imageData = this.ctx.createImageData(
      this.canvas.width,
      this.canvas.height,
    );

    const layer = {
      id: ++this._idCounter,
      name: name + " " + (this.layers.length + 1),
      visible: true,
      locked: false,
      opacity: 1,
      imageData: imageData,
      thumbnail: null,
    };

    this.layers.push(layer);
    this.activeIndex = this.layers.length - 1;

    this.render();
    this.renderThumbnails();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }

    return layer;
  }

  // ============================================================
  // ELIMINAR CAPA
  // ============================================================

  deleteLayer(index) {
    if (this.layers.length <= 1) {
      if (this.editor)
        this.editor.showToast("No puedes eliminar la última capa", "warning");
      return false;
    }

    if (index < 0 || index >= this.layers.length) return false;

    this.layers.splice(index, 1);

    if (this.activeIndex >= this.layers.length) {
      this.activeIndex = this.layers.length - 1;
    }

    this.render();
    this.renderThumbnails();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }

    return true;
  }

  // ============================================================
  // DUPLICAR CAPA
  // ============================================================

  duplicateLayer(index) {
    if (this.layers.length >= this.maxLayers) {
      if (this.editor)
        this.editor.showToast("Límite de capas alcanzado", "warning");
      return null;
    }

    if (index < 0 || index >= this.layers.length) return null;

    const original = this.layers[index];
    const newImageData = this.ctx.createImageData(
      original.imageData.width,
      original.imageData.height,
    );

    // Copiar datos de imagen
    newImageData.data.set(original.imageData.data);

    const newLayer = {
      id: ++this._idCounter,
      name: original.name + " (copia)",
      visible: original.visible,
      locked: original.locked,
      opacity: original.opacity,
      imageData: newImageData,
      thumbnail: null,
    };

    this.layers.splice(index + 1, 0, newLayer);
    this.activeIndex = index + 1;

    this.render();
    this.renderThumbnails();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }

    return newLayer;
  }

  // ============================================================
  // MOVER CAPA
  // ============================================================

  moveLayerUp(index) {
    if (index >= this.layers.length - 1) return false;

    [this.layers[index], this.layers[index + 1]] = [
      this.layers[index + 1],
      this.layers[index],
    ];

    if (this.activeIndex === index) this.activeIndex = index + 1;
    else if (this.activeIndex === index + 1) this.activeIndex = index;

    this.render();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }

    return true;
  }

  moveLayerDown(index) {
    if (index <= 0) return false;

    [this.layers[index], this.layers[index - 1]] = [
      this.layers[index - 1],
      this.layers[index],
    ];

    if (this.activeIndex === index) this.activeIndex = index - 1;
    else if (this.activeIndex === index - 1) this.activeIndex = index;

    this.render();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }

    return true;
  }

  // ============================================================
  // VISIBILIDAD Y BLOQUEO
  // ============================================================

  toggleVisibility(index) {
    if (index < 0 || index >= this.layers.length) return;

    this.layers[index].visible = !this.layers[index].visible;
    this.render();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }
  }

  toggleLock(index) {
    if (index < 0 || index >= this.layers.length) return;

    this.layers[index].locked = !this.layers[index].locked;
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
  }

  // ============================================================
  // OPACIDAD
  // ============================================================

  setOpacity(index, value) {
    if (index < 0 || index >= this.layers.length) return;

    this.layers[index].opacity = Math.max(0, Math.min(1, value));
    this.render();
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
    if (this.editor && this.editor.markAsModified) {
      this.editor.markAsModified();
    }
  }

  // ============================================================
  // CAPA ACTIVA
  // ============================================================

  setActiveLayer(index) {
    if (index < 0 || index >= this.layers.length) return;
    if (this.layers[index].locked) {
      if (this.editor)
        this.editor.showToast("Esta capa está bloqueada", "warning");
      return;
    }

    this.activeIndex = index;
    if (this.editor && this.editor.ui) {
      this.editor.ui.renderLayers();
    }
  }

  getActiveLayer() {
    return this.layers[this.activeIndex] || null;
  }

  // ============================================================
  // RENDERIZADO
  // ============================================================

  render() {
    // Limpiar canvas
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

    // Dibujar capas de abajo hacia arriba
    for (const layer of this.layers) {
      if (!layer.visible) continue;

      // Aplicar opacidad
      this.ctx.globalAlpha = layer.opacity;

      // Dibujar imagen de la capa
      if (layer.imageData) {
        this.ctx.putImageData(layer.imageData, 0, 0);
      }
    }

    // Restaurar opacidad
    this.ctx.globalAlpha = 1;

    // Actualizar miniatura del editor
    if (this.canvas) {
      this.canvas._lastRender = Date.now();
    }
  }

  // ============================================================
  // MINIATURAS
  // ============================================================

  renderThumbnails() {
    const thumbSize = 32;

    for (const layer of this.layers) {
      try {
        const canvas = document.createElement("canvas");
        canvas.width = thumbSize;
        canvas.height = thumbSize;
        const ctx = canvas.getContext("2d");

        // Dibujar la capa en miniatura
        if (layer.imageData) {
          const tempCanvas = document.createElement("canvas");
          tempCanvas.width = this.canvas.width;
          tempCanvas.height = this.canvas.height;
          const tempCtx = tempCanvas.getContext("2d");
          tempCtx.putImageData(layer.imageData, 0, 0);
          ctx.drawImage(tempCanvas, 0, 0, thumbSize, thumbSize);
        }

        layer.thumbnail = canvas.toDataURL("image/png");
      } catch (e) {
        layer.thumbnail = null;
      }
    }
  }

  // ============================================================
  // DIBUJAR EN CAPA ACTIVA
  // ============================================================

  drawOnActiveLayer(x, y, callback) {
    const layer = this.getActiveLayer();
    if (!layer || layer.locked || !layer.visible) {
      return false;
    }

    try {
      // Obtener imagen de la capa
      const imageData = layer.imageData;
      const data = imageData.data;
      const width = imageData.width;
      const height = imageData.height;

      // Aplicar el dibujo
      callback(data, width, height, x, y);

      // Actualizar la capa
      layer.imageData = imageData;

      this.render();
      this.renderThumbnails();
      if (this.editor && this.editor.markAsModified) {
        this.editor.markAsModified();
      }

      return true;
    } catch (e) {
      console.error("Error dibujando en capa activa:", e);
      return false;
    }
  }

  // ============================================================
  // DIBUJAR EN CAPA ESPECÍFICA
  // ============================================================

  drawOnLayer(index, x, y, callback) {
    if (index < 0 || index >= this.layers.length) return false;

    const layer = this.layers[index];
    if (!layer || layer.locked || !layer.visible) return false;

    try {
      const imageData = layer.imageData;
      const data = imageData.data;
      const width = imageData.width;
      const height = imageData.height;

      callback(data, width, height, x, y);

      layer.imageData = imageData;

      this.render();
      this.renderThumbnails();
      if (this.editor && this.editor.markAsModified) {
        this.editor.markAsModified();
      }

      return true;
    } catch (e) {
      console.error("Error dibujando en capa:", e);
      return false;
    }
  }

  // ============================================================
  // FUNCIONES DE DIBUJO EN CAPA
  // ============================================================

  // Punto
  drawPointOnLayer(index, x, y, color, size) {
    return this.drawOnLayer(index, x, y, (data, width, height, px, py) => {
      const radius = size / 2;
      const startX = Math.max(0, Math.floor(px - radius));
      const endX = Math.min(width, Math.ceil(px + radius));
      const startY = Math.max(0, Math.floor(py - radius));
      const endY = Math.min(height, Math.ceil(py + radius));

      const colorObj = this.hexToRgb(color);

      for (let dy = startY; dy < endY; dy++) {
        for (let dx = startX; dx < endX; dx++) {
          const dist = Math.sqrt((dx - px) ** 2 + (dy - py) ** 2);
          if (dist <= radius) {
            const idx = (dy * width + dx) * 4;
            const alpha = Math.min(1, 1 - dist / radius);
            data[idx] = colorObj.r;
            data[idx + 1] = colorObj.g;
            data[idx + 2] = colorObj.b;
            data[idx + 3] = Math.round(alpha * 255);
          }
        }
      }
    });
  }

  // Línea
  drawLineOnLayer(index, x1, y1, x2, y2, color, size) {
    return this.drawOnLayer(index, 0, 0, (data, width, height) => {
      const colorObj = this.hexToRgb(color);
      const steps = Math.max(Math.abs(x2 - x1), Math.abs(y2 - y1)) * 2;

      for (let i = 0; i <= steps; i++) {
        const t = i / steps;
        const x = x1 + (x2 - x1) * t;
        const y = y1 + (y2 - y1) * t;

        const px = Math.round(x);
        const py = Math.round(y);

        if (px >= 0 && px < width && py >= 0 && py < height) {
          const idx = (py * width + px) * 4;
          data[idx] = colorObj.r;
          data[idx + 1] = colorObj.g;
          data[idx + 2] = colorObj.b;
          data[idx + 3] = 255;
        }
      }
    });
  }

  // ============================================================
  // MERGE Y FLATTEN
  // ============================================================

  mergeDown(index) {
    if (index <= 0 || index >= this.layers.length) return false;

    const layer = this.layers[index];
    const below = this.layers[index - 1];

    if (!layer.visible || !below.visible) {
      if (this.editor)
        this.editor.showToast(
          "Ambas capas deben estar visibles para fusionar",
          "warning",
        );
      return false;
    }

    try {
      const combinedData = this.ctx.createImageData(
        this.canvas.width,
        this.canvas.height,
      );
      const combined = combinedData.data;
      const layerData = layer.imageData.data;
      const belowData = below.imageData.data;
      const opacity = layer.opacity;

      for (let i = 0; i < combined.length; i += 4) {
        const bR = belowData[i];
        const bG = belowData[i + 1];
        const bB = belowData[i + 2];
        const bA = belowData[i + 3] / 255;

        const lR = layerData[i];
        const lG = layerData[i + 1];
        const lB = layerData[i + 2];
        const lA = (layerData[i + 3] / 255) * opacity;

        const outA = bA + lA * (1 - bA);
        if (outA > 0) {
          combined[i] = (lR * lA + bR * bA * (1 - lA)) / outA;
          combined[i + 1] = (lG * lA + bG * bA * (1 - lA)) / outA;
          combined[i + 2] = (lB * lA + bB * bA * (1 - lA)) / outA;
          combined[i + 3] = outA * 255;
        } else {
          combined[i] = 0;
          combined[i + 1] = 0;
          combined[i + 2] = 0;
          combined[i + 3] = 0;
        }
      }

      below.imageData = combinedData;
      this.layers.splice(index, 1);

      if (this.activeIndex >= this.layers.length) {
        this.activeIndex = this.layers.length - 1;
      }

      this.render();
      this.renderThumbnails();
      if (this.editor && this.editor.ui) {
        this.editor.ui.renderLayers();
      }
      if (this.editor && this.editor.markAsModified) {
        this.editor.markAsModified();
      }

      return true;
    } catch (e) {
      console.error("Error fusionando capas:", e);
      return false;
    }
  }

  flatten() {
    if (this.layers.length <= 1) return;

    try {
      const combinedData = this.ctx.createImageData(
        this.canvas.width,
        this.canvas.height,
      );
      const combined = combinedData.data;

      for (let i = 0; i < combined.length; i++) {
        combined[i] = 0;
      }

      for (const layer of this.layers) {
        if (!layer.visible) continue;

        const layerData = layer.imageData.data;
        const opacity = layer.opacity;

        for (let i = 0; i < combined.length; i += 4) {
          const srcR = layerData[i];
          const srcG = layerData[i + 1];
          const srcB = layerData[i + 2];
          const srcA = (layerData[i + 3] / 255) * opacity;

          const dstR = combined[i];
          const dstG = combined[i + 1];
          const dstB = combined[i + 2];
          const dstA = combined[i + 3] / 255;

          const outA = dstA + srcA * (1 - dstA);
          if (outA > 0) {
            combined[i] = (srcR * srcA + dstR * dstA * (1 - srcA)) / outA;
            combined[i + 1] = (srcG * srcA + dstG * dstA * (1 - srcA)) / outA;
            combined[i + 2] = (srcB * srcA + dstB * dstA * (1 - srcA)) / outA;
            combined[i + 3] = outA * 255;
          }
        }
      }

      this.clear();
      this.addLayer("Fondo");
      this.layers[0].imageData = combinedData;
      this.activeIndex = 0;

      this.render();
      this.renderThumbnails();
      if (this.editor && this.editor.ui) {
        this.editor.ui.renderLayers();
      }
      if (this.editor && this.editor.markAsModified) {
        this.editor.markAsModified();
      }
    } catch (e) {
      console.error("Error aplanando capas:", e);
    }
  }

  // ============================================================
  // CARGA Y GUARDADO
  // ============================================================

  loadFromData(data) {
    this.clear();

    if (!data || !data.length) {
      this.addLayer("Fondo");
      return;
    }

    try {
      for (const layerData of data) {
        const imageData = this.ctx.createImageData(
          this.canvas.width,
          this.canvas.height,
        );

        if (layerData.imageData) {
          const pixels = layerData.imageData;
          const len = Math.min(pixels.length, imageData.data.length);
          for (let i = 0; i < len; i++) {
            imageData.data[i] = pixels[i];
          }
        }

        this.layers.push({
          id: ++this._idCounter,
          name: layerData.name || "Capa",
          visible: layerData.visible !== false,
          locked: layerData.locked || false,
          opacity: layerData.opacity || 1,
          imageData: imageData,
          thumbnail: null,
        });
      }

      this.activeIndex = 0;
      this.render();
      this.renderThumbnails();
      if (this.editor && this.editor.ui) {
        this.editor.ui.renderLayers();
      }
    } catch (e) {
      console.error("Error cargando datos de capas:", e);
      this.clear();
      this.addLayer("Fondo");
    }
  }

  exportData() {
    // ANTES: exportaba todos los píxeles
    // AHORA: solo exporta metadatos
    return this.layers.map((layer) => ({
      name: layer.name,
      visible: layer.visible,
      locked: layer.locked,
      opacity: layer.opacity,
      // NO exportar imageData
    }));
  }

  // ============================================================
  // UTILIDADES
  // ============================================================

  hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result
      ? {
          r: parseInt(result[1], 16),
          g: parseInt(result[2], 16),
          b: parseInt(result[3], 16),
        }
      : { r: 0, g: 0, b: 0 };
  }

  getLayerCount() {
    return this.layers.length;
  }

  getLayerName(index) {
    return this.layers[index]?.name || "";
  }
}

// Hacer disponible globalmente
window.LayerManager = LayerManager;
