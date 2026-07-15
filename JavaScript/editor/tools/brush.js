/**
 * ============================================================
 * brush.js - Sistema de pinceles del editor
 * Incluye: Lápiz, Pincel, Marcador, Spray, Borrador
 * ============================================================
 */

class BrushTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;

    this.isDrawing = false;
    this.lastX = 0;
    this.lastY = 0;
    this.sprayTimer = null;
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  onMouseDown(x, y) {
    this.isDrawing = true;
    this.lastX = x;
    this.lastY = y;

    const tool = this.editor.state.currentTool;

    if (tool === "spray") {
      // Spray: dibujar partículas inmediatamente
      this.drawSprayPoint(x, y);
      // Y seguir dibujando mientras se mantenga presionado
      if (this.sprayTimer) clearInterval(this.sprayTimer);
      this.sprayTimer = setInterval(() => {
        if (this.isDrawing) {
          this.drawSprayPoint(this.lastX, this.lastY);
        }
      }, 30);
    } else {
      // Otras herramientas: dibujar punto normal
      this.drawPoint(x, y);
    }
  }

  onMouseMove(x, y) {
    if (!this.isDrawing) return;

    const tool = this.editor.state.currentTool;

    if (tool === "spray") {
      // Spray: actualizar posición y soltar partículas
      this.lastX = x;
      this.lastY = y;
      this.drawSprayPoint(x, y);
    } else {
      // Otras herramientas: dibujar línea
      this.drawLine(this.lastX, this.lastY, x, y);
      this.lastX = x;
      this.lastY = y;
    }
  }

  onMouseUp() {
    if (this.isDrawing) {
      this.isDrawing = false;
      if (this.sprayTimer) {
        clearInterval(this.sprayTimer);
        this.sprayTimer = null;
      }
      if (this.editor && this.editor.history) {
        const tool = this.editor.state.currentTool;
        const names = {
          pencil: "Lápiz",
          brush: "Pincel",
          marker: "Marcador",
          spray: "Spray",
          eraser: "Borrador",
        };
        this.editor.history.saveState(names[tool] || "Dibujo");
      }
    }
  }

  // ============================================================
  // DIBUJO NORMAL (Pincel, Lápiz, Marcador, Borrador)
  // ============================================================

  drawPoint(x, y) {
    const tool = this.editor.state.currentTool;
    const state = this.editor.state;
    const isEraser = tool === "eraser";
    const color = isEraser ? "#ffffff" : state.color;
    const opacity = isEraser ? 1 : state.opacity;

    let size = state.size;
    if (tool === "pencil") size = Math.max(1, state.size * 0.4);
    else if (tool === "marker") size = state.size * 0.9;
    else if (tool === "eraser") size = state.size * 1.5;

    this.layers.drawOnActiveLayer(0, 0, (data, width, height) => {
      const radius = size / 2;

      switch (tool) {
        case "pencil":
          this.drawPencil(data, width, height, x, y, color, radius, opacity);
          break;
        case "marker":
          this.drawMarker(data, width, height, x, y, color, radius, opacity);
          break;
        case "eraser":
          this.drawEraser(data, width, height, x, y, radius);
          break;
        default:
          this.drawBrush(data, width, height, x, y, color, radius, opacity);
      }
    });
  }

  drawLine(x1, y1, x2, y2) {
    const tool = this.editor.state.currentTool;
    const state = this.editor.state;
    const isEraser = tool === "eraser";
    const color = isEraser ? "#ffffff" : state.color;
    const opacity = isEraser ? 1 : state.opacity;

    let size = state.size;
    if (tool === "pencil") size = Math.max(1, state.size * 0.4);
    else if (tool === "marker") size = state.size * 0.9;
    else if (tool === "eraser") size = state.size * 1.5;

    this.layers.drawOnActiveLayer(0, 0, (data, width, height) => {
      const dist = Math.sqrt((x2 - x1) ** 2 + (y2 - y1) ** 2);
      const steps = Math.max(Math.ceil(dist / 0.5), 1);
      const radius = size / 2;

      for (let i = 0; i <= steps; i++) {
        const t = i / steps;
        const x = x1 + (x2 - x1) * t;
        const y = y1 + (y2 - y1) * t;

        switch (tool) {
          case "pencil":
            this.drawPencil(data, width, height, x, y, color, radius, opacity);
            break;
          case "marker":
            this.drawMarker(data, width, height, x, y, color, radius, opacity);
            break;
          case "eraser":
            this.drawEraser(data, width, height, x, y, radius);
            break;
          default:
            this.drawBrush(data, width, height, x, y, color, radius, opacity);
        }
      }
    });
  }

  // ============================================================
  // SPRAY - Partículas
  // ============================================================

  drawSprayPoint(x, y) {
    const state = this.editor.state;
    const color = state.color;
    const opacity = state.opacity;
    const size = state.size * 1.2;

    this.layers.drawOnActiveLayer(0, 0, (data, width, height) => {
      const colorObj = this.hexToRgb(color);
      const radius = size;
      const count = Math.max(30, Math.round(radius * 5));

      for (let i = 0; i < count; i++) {
        const angle = Math.random() * Math.PI * 2;
        const dist = Math.random() * radius;
        const px = Math.round(x + Math.cos(angle) * dist);
        const py = Math.round(y + Math.sin(angle) * dist);

        if (px >= 0 && px < width && py >= 0 && py < height) {
          const dotSize = Math.random() * radius * 0.15 + 0.5;
          const alpha = Math.random() * opacity * 0.35;

          const startX = Math.max(0, Math.floor(px - dotSize));
          const endX = Math.min(width, Math.ceil(px + dotSize));
          const startY = Math.max(0, Math.floor(py - dotSize));
          const endY = Math.min(height, Math.ceil(py + dotSize));

          for (let dy = startY; dy < endY; dy++) {
            for (let dx = startX; dx < endX; dx++) {
              const distP = Math.sqrt((dx - px) ** 2 + (dy - py) ** 2);
              if (distP <= dotSize) {
                const idx = (dy * width + dx) * 4;
                if (idx >= 0 && idx < data.length) {
                  const a = alpha * (1 - distP / dotSize);
                  const existingAlpha = data[idx + 3] / 255;
                  const newAlpha = Math.min(1, existingAlpha + a);
                  data[idx] = colorObj.r * a + data[idx] * (1 - a);
                  data[idx + 1] = colorObj.g * a + data[idx + 1] * (1 - a);
                  data[idx + 2] = colorObj.b * a + data[idx + 2] * (1 - a);
                  data[idx + 3] = Math.round(newAlpha * 255);
                }
              }
            }
          }
        }
      }
    });
  }

  // ============================================================
  // ESTILOS DE CADA HERRAMIENTA
  // ============================================================

  drawPencil(data, width, height, x, y, color, radius, opacity) {
    const colorObj = this.hexToRgb(color);
    const startX = Math.max(0, Math.floor(x - radius));
    const endX = Math.min(width, Math.ceil(x + radius));
    const startY = Math.max(0, Math.floor(y - radius));
    const endY = Math.min(height, Math.ceil(y + radius));

    for (let dy = startY; dy < endY; dy++) {
      for (let dx = startX; dx < endX; dx++) {
        const dist = Math.sqrt((dx - x) ** 2 + (dy - y) ** 2);
        if (dist <= radius) {
          const idx = (dy * width + dx) * 4;
          const alpha = Math.min(1, 1 - (dist / radius) * 1.3);
          const finalAlpha = Math.min(1, alpha * opacity * 1.2);
          const existingAlpha = data[idx + 3] / 255;
          const newAlpha = Math.min(1, existingAlpha + finalAlpha);
          data[idx] = colorObj.r * finalAlpha + data[idx] * (1 - finalAlpha);
          data[idx + 1] =
            colorObj.g * finalAlpha + data[idx + 1] * (1 - finalAlpha);
          data[idx + 2] =
            colorObj.b * finalAlpha + data[idx + 2] * (1 - finalAlpha);
          data[idx + 3] = Math.round(newAlpha * 255);
        }
      }
    }
  }

  drawBrush(data, width, height, x, y, color, radius, opacity) {
    const colorObj = this.hexToRgb(color);
    const startX = Math.max(0, Math.floor(x - radius));
    const endX = Math.min(width, Math.ceil(x + radius));
    const startY = Math.max(0, Math.floor(y - radius));
    const endY = Math.min(height, Math.ceil(y + radius));

    for (let dy = startY; dy < endY; dy++) {
      for (let dx = startX; dx < endX; dx++) {
        const dist = Math.sqrt((dx - x) ** 2 + (dy - y) ** 2);
        if (dist <= radius) {
          const idx = (dy * width + dx) * 4;
          const alpha = Math.min(1, 1 - dist / radius);
          const finalAlpha = alpha * opacity * 0.9;
          const existingAlpha = data[idx + 3] / 255;
          const newAlpha = Math.min(1, existingAlpha + finalAlpha);
          data[idx] = colorObj.r * finalAlpha + data[idx] * (1 - finalAlpha);
          data[idx + 1] =
            colorObj.g * finalAlpha + data[idx + 1] * (1 - finalAlpha);
          data[idx + 2] =
            colorObj.b * finalAlpha + data[idx + 2] * (1 - finalAlpha);
          data[idx + 3] = Math.round(newAlpha * 255);
        }
      }
    }
  }

  drawMarker(data, width, height, x, y, color, radius, opacity) {
    const colorObj = this.hexToRgb(color);
    const startX = Math.max(0, Math.floor(x - radius));
    const endX = Math.min(width, Math.ceil(x + radius));
    const startY = Math.max(0, Math.floor(y - radius));
    const endY = Math.min(height, Math.ceil(y + radius));

    for (let dy = startY; dy < endY; dy++) {
      for (let dx = startX; dx < endX; dx++) {
        const dist = Math.sqrt((dx - x) ** 2 + (dy - y) ** 2);
        if (dist <= radius) {
          const idx = (dy * width + dx) * 4;
          const alpha =
            dist < radius * 0.8
              ? 1
              : Math.max(0, 1 - (dist - radius * 0.8) / (radius * 0.2));
          const finalAlpha = alpha * opacity * 0.85;
          const existingAlpha = data[idx + 3] / 255;
          const newAlpha = Math.min(1, existingAlpha + finalAlpha);
          data[idx] = colorObj.r * finalAlpha + data[idx] * (1 - finalAlpha);
          data[idx + 1] =
            colorObj.g * finalAlpha + data[idx + 1] * (1 - finalAlpha);
          data[idx + 2] =
            colorObj.b * finalAlpha + data[idx + 2] * (1 - finalAlpha);
          data[idx + 3] = Math.round(newAlpha * 255);
        }
      }
    }
  }

  drawEraser(data, width, height, x, y, radius) {
    const startX = Math.max(0, Math.floor(x - radius));
    const endX = Math.min(width, Math.ceil(x + radius));
    const startY = Math.max(0, Math.floor(y - radius));
    const endY = Math.min(height, Math.ceil(y + radius));

    for (let dy = startY; dy < endY; dy++) {
      for (let dx = startX; dx < endX; dx++) {
        const dist = Math.sqrt((dx - x) ** 2 + (dy - y) ** 2);
        if (dist <= radius) {
          const idx = (dy * width + dx) * 4;
          const alpha = Math.min(1, 1 - dist / radius);
          const finalAlpha = Math.min(1, alpha * 0.95);
          data[idx] = 255;
          data[idx + 1] = 255;
          data[idx + 2] = 255;
          data[idx + 3] = Math.round(
            Math.min(255, data[idx + 3] + finalAlpha * 255),
          );
        }
      }
    }
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
}

window.BrushTool = BrushTool;
