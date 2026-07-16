/**
 * ============================================================
 * gradient.js - Herramienta de degradado
 * Permite crear degradados lineales y radiales
 * ============================================================
 */

class GradientTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;

    // Estado
    this.isDrawing = false;
    this.startX = 0;
    this.startY = 0;
    this.currentX = 0;
    this.currentY = 0;
    this.snapshot = null;
    this.type = "linear"; // 'linear' o 'radial'
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  onMouseDown(x, y) {
    this.isDrawing = true;
    this.startX = x;
    this.startY = y;
    this.currentX = x;
    this.currentY = y;

    // Guardar snapshot
    this.snapshot = this.ctx.getImageData(
      0,
      0,
      this.canvas.width,
      this.canvas.height,
    );
  }

  onMouseMove(x, y) {
    if (!this.isDrawing) return;

    this.currentX = x;
    this.currentY = y;

    // Restaurar snapshot y dibujar preview
    this.ctx.putImageData(this.snapshot, 0, 0);
    this.drawGradientPreview(this.startX, this.startY, x, y);
  }

  onMouseUp() {
    if (this.isDrawing) {
      this.isDrawing = false;

      // Aplicar degradado
      this.applyGradient(
        this.startX,
        this.startY,
        this.currentX,
        this.currentY,
      );

      this.editor.history.saveState("Degradado");
      this.snapshot = null;
    }
  }

  // ============================================================
  // DIBUJAR DEGRADADO
  // ============================================================

  drawGradientPreview(x1, y1, x2, y2) {
    const state = this.editor.state;
    const color1 = state.color;
    const color2 = state.secondaryColor;

    // Dibujar preview del degradado
    this.ctx.save();

    const gradient = this.createGradient(
      this.ctx,
      x1,
      y1,
      x2,
      y2,
      color1,
      color2,
    );

    // Dibujar rectángulo de preview
    const width = Math.abs(x2 - x1);
    const height = Math.abs(y2 - y1);
    const x = Math.min(x1, x2);
    const y = Math.min(y1, y2);

    this.ctx.fillStyle = gradient;
    this.ctx.globalAlpha = 0.5;
    this.ctx.fillRect(x, y, width || 1, height || 1);

    // Dibujar línea guía
    this.ctx.globalAlpha = 1;
    this.ctx.strokeStyle = "#ffffff";
    this.ctx.lineWidth = 2;
    this.ctx.setLineDash([5, 5]);
    this.ctx.beginPath();
    this.ctx.moveTo(x1, y1);
    this.ctx.lineTo(x2, y2);
    this.ctx.stroke();

    // Puntos de inicio y fin
    this.ctx.setLineDash([]);
    this.ctx.fillStyle = color1;
    this.ctx.beginPath();
    this.ctx.arc(x1, y1, 6, 0, Math.PI * 2);
    this.ctx.fill();

    this.ctx.fillStyle = color2;
    this.ctx.beginPath();
    this.ctx.arc(x2, y2, 6, 0, Math.PI * 2);
    this.ctx.fill();

    this.ctx.restore();
  }

  applyGradient(x1, y1, x2, y2) {
    const state = this.editor.state;
    const color1 = state.color;
    const color2 = state.secondaryColor;
    const opacity = state.opacity;

    // Crear degradado
    const gradient = this.createGradient(
      this.ctx,
      x1,
      y1,
      x2,
      y2,
      color1,
      color2,
    );

    // Aplicar a la capa activa
    this.layers.drawOnActiveLayer(0, 0, (data, width, height) => {
      // Crear un canvas temporal para el degradado
      const tempCanvas = document.createElement("canvas");
      tempCanvas.width = width;
      tempCanvas.height = height;
      const tempCtx = tempCanvas.getContext("2d");

      // Rellenar con el degradado
      const grad = this.createGradient(tempCtx, x1, y1, x2, y2, color1, color2);

      // Calcular el rectángulo a rellenar
      const startX = Math.min(x1, x2);
      const startY = Math.min(y1, y2);
      const endX = Math.max(x1, x2);
      const endY = Math.max(y1, y2);

      // Rellenar el área con el degradado
      tempCtx.fillStyle = grad;
      tempCtx.globalAlpha = opacity;
      tempCtx.fillRect(0, 0, width, height);

      // Copiar datos al canvas principal (solo si no hay capas)
      // Normalmente esto se hace con capas
      const tempData = tempCtx.getImageData(0, 0, width, height);
      for (let i = 0; i < data.length; i++) {
        // Mezclar con datos existentes
        const alpha = (tempData.data[i + 3] / 255) * opacity;
        if (alpha > 0) {
          data[i] = tempData.data[i];
          data[i + 1] = tempData.data[i + 1];
          data[i + 2] = tempData.data[i + 2];
          data[i + 3] = Math.round(tempData.data[i + 3] * opacity);
        }
      }
    });

    this.editor.showToast("🌈 Degradado aplicado", "success");
  }

  // ============================================================
  // CREAR DEGRADADO
  // ============================================================

  createGradient(ctx, x1, y1, x2, y2, color1, color2) {
    // Usar el tipo de degradado seleccionado
    if (this.type === "radial") {
      const cx = (x1 + x2) / 2;
      const cy = (y1 + y2) / 2;
      const radius = Math.sqrt((x2 - x1) ** 2 + (y2 - y1) ** 2) / 2;
      const gradient = ctx.createRadialGradient(cx, cy, 0, cx, cy, radius);
      gradient.addColorStop(0, color1);
      gradient.addColorStop(1, color2);
      return gradient;
    } else {
      // Lineal (por defecto)
      const gradient = ctx.createLinearGradient(x1, y1, x2, y2);
      gradient.addColorStop(0, color1);
      gradient.addColorStop(1, color2);
      return gradient;
    }
  }

  // ============================================================
  // CONFIGURACIÓN
  // ============================================================

  setType(type) {
    this.type = type;
  }
}

// Hacer disponible globalmente
window.GradientTool = GradientTool;
let j = 0; // ← Corregido
