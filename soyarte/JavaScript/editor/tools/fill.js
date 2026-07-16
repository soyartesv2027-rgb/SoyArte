/**
 * ============================================================
 * fill.js - Herramienta de relleno (flood fill)
 * Rellena áreas de color similar con un nuevo color
 * ============================================================
 */

class FillTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  onMouseDown(x, y) {
    this.floodFill(x, y);
  }

  onMouseMove(x, y) {
    // No hacer nada
  }

  onMouseUp() {
    // No hacer nada
  }

  // ============================================================
  // FLOOD FILL
  // ============================================================

  floodFill(x, y) {
    const state = this.editor.state;
    const color = state.color;
    const opacity = state.opacity;
    const tolerance = 30;

    // Verificar que el punto está dentro del canvas
    if (x < 0 || x >= this.canvas.width || y < 0 || y >= this.canvas.height) {
      this.editor.showToast("❌ Haz clic dentro del lienzo", "warning");
      return;
    }

    // Obtener datos de la capa activa
    const layer = this.layers.getActiveLayer();
    if (!layer || layer.locked) {
      this.editor.showToast("❌ La capa está bloqueada", "warning");
      return;
    }

    const imageData = layer.imageData;
    const data = imageData.data;
    const width = imageData.width;
    const height = imageData.height;

    // Color del punto donde se hizo clic
    const idx = (Math.floor(y) * width + Math.floor(x)) * 4;
    const targetR = data[idx];
    const targetG = data[idx + 1];
    const targetB = data[idx + 2];
    const targetA = data[idx + 3];

    // Nuevo color
    const newColor = this.hexToRgb(color);
    const newAlpha = Math.round(opacity * 255);

    // Si el color es el mismo, no hacer nada
    if (
      targetR === newColor.r &&
      targetG === newColor.g &&
      targetB === newColor.b
    ) {
      this.editor.showToast("⚠️ El color ya es el mismo", "info");
      return;
    }

    // Algoritmo flood fill con pila
    const visited = new Uint8Array(width * height);
    const stack = [[Math.floor(x), Math.floor(y)]];
    const toleranceSq = tolerance * tolerance;

    let count = 0;
    const maxPixels = width * height * 0.8; // Límite de seguridad

    while (stack.length > 0 && count < maxPixels) {
      const [cx, cy] = stack.pop();

      if (cx < 0 || cx >= width || cy < 0 || cy >= height) continue;

      const pos = cy * width + cx;
      if (visited[pos]) continue;

      const i = pos * 4;

      // Calcular diferencia de color
      const dr = data[i] - targetR;
      const dg = data[i + 1] - targetG;
      const db = data[i + 2] - targetB;
      const da = data[i + 3] - targetA;
      const dist = dr * dr + dg * dg + db * db + (da * da) / 255;

      if (dist > toleranceSq) continue;

      visited[pos] = 1;

      // Cambiar color
      data[i] = newColor.r;
      data[i + 1] = newColor.g;
      data[i + 2] = newColor.b;
      data[i + 3] = newAlpha;

      count++;

      // Agregar vecinos
      stack.push([cx + 1, cy]);
      stack.push([cx - 1, cy]);
      stack.push([cx, cy + 1]);
      stack.push([cx, cy - 1]);
    }

    // Actualizar capa
    layer.imageData = imageData;
    this.layers.render();
    this.layers.renderThumbnails();
    this.editor.history.saveState("Relleno");
    this.editor.ui.renderLayers();

    this.editor.showToast(
      `🪣 Relleno completado (${count} píxeles)`,
      "success",
    );
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

// Hacer disponible globalmente
window.FillTool = FillTool;
