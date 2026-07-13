/**
 * ============================================================
 * canvas.js - Gestión del canvas y renderizado
 * Maneja el contexto, dimensiones, y dibujo básico
 * ============================================================
 */

class CanvasManager {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = this.canvas.getContext("2d", {
      willReadFrequently: true,
    });

    this.width = editor.config.canvasWidth;
    this.height = editor.config.canvasHeight;
    this.zoom = 1;

    // Buffer para operaciones rápidas
    this.buffer = null;
    this.bufferCtx = null;
  }

  // ============================================================
  // INICIALIZACIÓN
  // ============================================================

  init() {
    this.resize(this.width, this.height);
    this.clear("#ffffff");
    this.setupBuffer();
  }

  setupBuffer() {
    // Crear buffer para operaciones rápidas
    this.buffer = document.createElement("canvas");
    this.buffer.width = this.width;
    this.buffer.height = this.height;
    this.bufferCtx = this.buffer.getContext("2d");
  }

  // ============================================================
  // DIMENSIONES
  // ============================================================

  resize(width, height) {
    this.width = width;
    this.height = height;

    this.canvas.width = width;
    this.canvas.height = height;

    // Actualizar buffer
    if (this.buffer) {
      this.buffer.width = width;
      this.buffer.height = height;
    }

    // Actualizar vista
    this.editor.zoomFit();
    this.editor.updateUI();
  }

  // ============================================================
  // LIMPIAR
  // ============================================================

  clear(color = "#ffffff") {
    this.ctx.fillStyle = color;
    this.ctx.fillRect(0, 0, this.width, this.height);
  }

  // ============================================================
  // DIBUJO BÁSICO
  // ============================================================

  drawPoint(x, y, color, size, opacity = 1) {
    this.ctx.save();
    this.ctx.globalAlpha = opacity;
    this.ctx.fillStyle = color;
    this.ctx.beginPath();
    this.ctx.arc(x, y, size / 2, 0, Math.PI * 2);
    this.ctx.fill();
    this.ctx.restore();
  }

  drawLine(x1, y1, x2, y2, color, size, opacity = 1) {
    this.ctx.save();
    this.ctx.globalAlpha = opacity;
    this.ctx.strokeStyle = color;
    this.ctx.lineWidth = size;
    this.ctx.lineCap = "round";
    this.ctx.lineJoin = "round";
    this.ctx.beginPath();
    this.ctx.moveTo(x1, y1);
    this.ctx.lineTo(x2, y2);
    this.ctx.stroke();
    this.ctx.restore();
  }

  drawRect(x, y, w, h, color, fill = false, size = 2, opacity = 1) {
    this.ctx.save();
    this.ctx.globalAlpha = opacity;
    this.ctx.strokeStyle = color;
    this.ctx.fillStyle = fill ? color : "transparent";
    this.ctx.lineWidth = size;
    this.ctx.beginPath();
    this.ctx.rect(x, y, w, h);
    if (fill) this.ctx.fill();
    this.ctx.stroke();
    this.ctx.restore();
  }

  drawCircle(cx, cy, radius, color, fill = false, size = 2, opacity = 1) {
    this.ctx.save();
    this.ctx.globalAlpha = opacity;
    this.ctx.strokeStyle = color;
    this.ctx.fillStyle = fill ? color : "transparent";
    this.ctx.lineWidth = size;
    this.ctx.beginPath();
    this.ctx.arc(cx, cy, radius, 0, Math.PI * 2);
    if (fill) this.ctx.fill();
    this.ctx.stroke();
    this.ctx.restore();
  }

  drawText(text, x, y, color, size, font = "Arial", opacity = 1) {
    this.ctx.save();
    this.ctx.globalAlpha = opacity;
    this.ctx.fillStyle = color;
    this.ctx.font = `${size}px ${font}`;
    this.ctx.textBaseline = "top";
    this.ctx.fillText(text, x, y);
    this.ctx.restore();
  }

  // ============================================================
  // IMAGEN
  // ============================================================

  getImageData() {
    return this.ctx.getImageData(0, 0, this.width, this.height);
  }

  putImageData(imageData) {
    this.ctx.putImageData(imageData, 0, 0);
  }

  getDataURL(format = "image/png") {
    return this.canvas.toDataURL(format);
  }

  loadImage(src) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      img.onload = () => resolve(img);
      img.onerror = reject;
      img.src = src;
    });
  }

  drawImage(img, x, y, w, h) {
    this.ctx.drawImage(img, x, y, w, h);
  }

  // ============================================================
  // PREVIEW
  // ============================================================

  loadPreview(dataUrl) {
    const img = new Image();
    img.onload = () => {
      this.clear("#ffffff");
      this.ctx.drawImage(img, 0, 0, this.width, this.height);
    };
    img.src = dataUrl;
  }

  generateThumbnail(width = 150, height = 100) {
    const canvas = document.createElement("canvas");
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(this.canvas, 0, 0, width, height);
    return canvas.toDataURL("image/png");
  }

  // ============================================================
  // CAPTURAR / RESTAURAR
  // ============================================================

  captureState() {
    return this.ctx.getImageData(0, 0, this.width, this.height);
  }

  restoreState(imageData) {
    this.ctx.putImageData(imageData, 0, 0);
  }

  // ============================================================
  // COORDENADAS
  // ============================================================

  getCanvasCoords(e) {
    const rect = this.canvas.getBoundingClientRect();
    const touch = e.touches ? e.touches[0] : e;
    const x = (touch.clientX - rect.left) * (this.width / rect.width);
    const y = (touch.clientY - rect.top) * (this.height / rect.height);
    return { x, y };
  }

  // ============================================================
  // TRANSFORMACIONES
  // ============================================================

  setTransform(zoom, panX, panY) {
    this.zoom = zoom || 1;
    // Aplicar transformación al canvas
    this.canvas.style.transform = `scale(${this.zoom}) translate(${panX || 0}px, ${panY || 0}px)`;
    this.canvas.style.transformOrigin = "top left";
  }

  // ============================================================
  // EFECTOS
  // ============================================================

  applyFilter(filterFunction) {
    const imageData = this.getImageData();
    const data = imageData.data;
    filterFunction(data);
    this.putImageData(imageData);
    this.editor.markAsModified();
  }

  // Filtros básicos
  invert() {
    this.applyFilter((data) => {
      for (let i = 0; i < data.length; i += 4) {
        data[i] = 255 - data[i];
        data[i + 1] = 255 - data[i + 1];
        data[i + 2] = 255 - data[i + 2];
      }
    });
  }

  grayscale() {
    this.applyFilter((data) => {
      for (let i = 0; i < data.length; i += 4) {
        const avg = (data[i] + data[i + 1] + data[i + 2]) / 3;
        data[i] = avg;
        data[i + 1] = avg;
        data[i + 2] = avg;
      }
    });
  }

  sepia() {
    this.applyFilter((data) => {
      for (let i = 0; i < data.length; i += 4) {
        const r = data[i];
        const g = data[i + 1];
        const b = data[i + 2];
        data[i] = Math.min(255, r * 0.393 + g * 0.769 + b * 0.189);
        data[i + 1] = Math.min(255, r * 0.349 + g * 0.686 + b * 0.168);
        data[i + 2] = Math.min(255, r * 0.272 + g * 0.534 + b * 0.131);
      }
    });
  }

  brightness(value) {
    this.applyFilter((data) => {
      for (let i = 0; i < data.length; i += 4) {
        data[i] = Math.min(255, data[i] * value);
        data[i + 1] = Math.min(255, data[i + 1] * value);
        data[i + 2] = Math.min(255, data[i + 2] * value);
      }
    });
  }

  contrast(value) {
    this.applyFilter((data) => {
      const factor = (259 * (value * 100 + 255)) / (255 * (259 - value * 100));
      for (let i = 0; i < data.length; i += 4) {
        data[i] = Math.min(255, Math.max(0, factor * (data[i] - 128) + 128));
        data[i + 1] = Math.min(
          255,
          Math.max(0, factor * (data[i + 1] - 128) + 128),
        );
        data[i + 2] = Math.min(
          255,
          Math.max(0, factor * (data[i + 2] - 128) + 128),
        );
      }
    });
  }

  // ============================================================
  // UTILIDADES
  // ============================================================

  isPointInCanvas(x, y) {
    return x >= 0 && x <= this.width && y >= 0 && y <= this.height;
  }

  clamp(x, min, max) {
    return Math.min(Math.max(x, min), max);
  }

  distance(x1, y1, x2, y2) {
    return Math.sqrt((x2 - x1) ** 2 + (y2 - y1) ** 2);
  }
}

// Hacer disponible globalmente
window.CanvasManager = CanvasManager;
