/**
 * ============================================================
 * shapes.js - Herramientas de formas geométricas
 * Incluye: Rectángulo, Círculo, Triángulo, Estrella, Corazón
 * ============================================================
 */

class ShapesTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;
    this.history = editor.history;

    // Estado
    this.isDrawing = false;
    this.startX = 0;
    this.startY = 0;
    this.currentX = 0;
    this.currentY = 0;
    this.snapshot = null;
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

    // Guardar snapshot para preview
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
    this.drawShape(this.startX, this.startY, x, y, true);
  }

  onMouseUp() {
    if (this.isDrawing) {
      this.isDrawing = false;

      // Dibujar la forma final en la capa
      this.drawShape(
        this.startX,
        this.startY,
        this.currentX,
        this.currentY,
        false,
      );

      // Guardar en historial
      this.editor.history.saveState(this.getActionName());

      // Limpiar snapshot
      this.snapshot = null;
    }
  }

  // ============================================================
  // DIBUJO DE FORMAS
  // ============================================================

  drawShape(x1, y1, x2, y2, preview = false) {
    const tool = this.editor.state.currentTool;
    const state = this.editor.state;
    const color = state.color;
    const fill = state.fillMode;
    const size = state.size;

    const width = x2 - x1;
    const height = y2 - y1;

    // Si es preview, dibujar directamente en el canvas
    if (preview) {
      this.ctx.save();
      this.ctx.strokeStyle = color;
      this.ctx.fillStyle = fill ? color : "transparent";
      this.ctx.lineWidth = size;
      this.ctx.lineCap = "round";
      this.ctx.lineJoin = "round";

      this.drawShapeOnContext(this.ctx, x1, y1, x2, y2, tool);

      this.ctx.restore();
      return;
    }

    // Dibujar en la capa activa
    this.layers.drawOnActiveLayer(0, 0, (data, canvasWidth, canvasHeight) => {
      // Crear un canvas temporal para dibujar la forma
      const tempCanvas = document.createElement("canvas");
      tempCanvas.width = canvasWidth;
      tempCanvas.height = canvasHeight;
      const tempCtx = tempCanvas.getContext("2d");

      tempCtx.putImageData(
        this.ctx.getImageData(0, 0, canvasWidth, canvasHeight),
        0,
        0,
      );
      tempCtx.save();
      tempCtx.strokeStyle = color;
      tempCtx.fillStyle = fill ? color : "transparent";
      tempCtx.lineWidth = size;
      tempCtx.lineCap = "round";
      tempCtx.lineJoin = "round";

      this.drawShapeOnContext(tempCtx, x1, y1, x2, y2, tool);

      tempCtx.restore();

      // Copiar datos del canvas temporal a la capa
      const tempData = tempCtx.getImageData(0, 0, canvasWidth, canvasHeight);
      for (let i = 0; i < data.length; i++) {
        data[i] = tempData.data[i];
      }
    });
  }

  drawShapeOnContext(ctx, x1, y1, x2, y2, tool) {
    const width = x2 - x1;
    const height = y2 - y1;

    switch (tool) {
      case "rect":
        ctx.beginPath();
        ctx.rect(x1, y1, width, height);
        break;

      case "circle":
        const radius = Math.sqrt(width * width + height * height) / 2;
        const cx = x1 + width / 2;
        const cy = y1 + height / 2;
        ctx.beginPath();
        ctx.arc(cx, cy, radius, 0, Math.PI * 2);
        break;

      case "triangle":
        ctx.beginPath();
        ctx.moveTo(x1 + width / 2, y1);
        ctx.lineTo(x2, y2);
        ctx.lineTo(x1, y2);
        ctx.closePath();
        break;

      case "star":
        this.drawStar(ctx, x1, y1, width, height);
        break;

      case "heart":
        this.drawHeart(ctx, x1, y1, width, height);
        break;

      default:
        ctx.beginPath();
        ctx.rect(x1, y1, width, height);
    }

    // Si la forma tiene relleno, rellenar y dibujar contorno
    if (this.editor.state.fillMode) {
      ctx.fill();
    }
    ctx.stroke();
  }

  // ============================================================
  // FORMAS ESPECIALES
  // ============================================================

  drawStar(ctx, x, y, width, height) {
    const points = 5;
    const outerRadius = Math.min(Math.abs(width), Math.abs(height)) / 2;
    const innerRadius = outerRadius * 0.4;
    const cx = x + width / 2;
    const cy = y + height / 2;

    ctx.beginPath();
    for (let i = 0; i < points * 2; i++) {
      const radius = i % 2 === 0 ? outerRadius : innerRadius;
      const angle = (i / (points * 2)) * Math.PI * 2 - Math.PI / 2;
      const px = cx + Math.cos(angle) * radius;
      const py = cy + Math.sin(angle) * radius;
      i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
    }
    ctx.closePath();
  }

  drawHeart(ctx, x, y, width, height) {
    const cx = x + width / 2;
    const cy = y + height / 2;
    const size = Math.min(Math.abs(width), Math.abs(height)) / 2;

    ctx.beginPath();
    ctx.moveTo(cx, cy + size * 0.3);
    ctx.bezierCurveTo(
      cx - size,
      cy - size * 0.5,
      cx - size * 1.2,
      cy + size * 0.3,
      cx,
      cy + size * 0.8,
    );
    ctx.bezierCurveTo(
      cx + size * 1.2,
      cy + size * 0.3,
      cx + size,
      cy - size * 0.5,
      cx,
      cy + size * 0.3,
    );
    ctx.closePath();
  }

  // ============================================================
  // UTILIDADES
  // ============================================================

  getActionName() {
    const tool = this.editor.state.currentTool;
    const names = {
      rect: "Rectángulo",
      circle: "Círculo",
      triangle: "Triángulo",
      star: "Estrella",
      heart: "Corazón",
    };
    return names[tool] || "Forma";
  }
}

// Hacer disponible globalmente
window.ShapesTool = ShapesTool;
