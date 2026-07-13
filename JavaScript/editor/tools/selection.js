/**
 * ============================================================
 * selection.js - Herramienta de selección
 * Permite seleccionar, mover, copiar y pegar áreas del canvas
 * ============================================================
 */

class SelectionTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;

    // Estado
    this.isSelecting = false;
    this.isMoving = false;
    this.startX = 0;
    this.startY = 0;
    this.currentX = 0;
    this.currentY = 0;
    this.selection = null;
    this.clipboard = null;
    this.snapshot = null;
    this.moveOffsetX = 0;
    this.moveOffsetY = 0;
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  onMouseDown(x, y) {
    // Verificar si hay una selección y si se hizo clic dentro
    if (this.selection && this.isInsideSelection(x, y)) {
      this.isMoving = true;
      this.moveOffsetX = x - this.selection.x;
      this.moveOffsetY = y - this.selection.y;
      this.snapshot = this.ctx.getImageData(
        0,
        0,
        this.canvas.width,
        this.canvas.height,
      );
      return;
    }

    // Iniciar nueva selección
    this.isSelecting = true;
    this.startX = x;
    this.startY = y;
    this.currentX = x;
    this.currentY = y;
    this.selection = null;
    this.snapshot = this.ctx.getImageData(
      0,
      0,
      this.canvas.width,
      this.canvas.height,
    );
  }

  onMouseMove(x, y) {
    if (this.isMoving && this.selection) {
      // Mover selección
      this.moveSelection(x, y);
      return;
    }

    if (!this.isSelecting) return;

    this.currentX = x;
    this.currentY = y;

    // Restaurar snapshot y dibujar preview de selección
    this.ctx.putImageData(this.snapshot, 0, 0);
    this.drawSelectionBox(this.startX, this.startY, x, y);
  }

  onMouseUp() {
    if (this.isMoving) {
      this.isMoving = false;
      this.editor.history.saveState("Mover selección");
      return;
    }

    if (this.isSelecting) {
      this.isSelecting = false;

      // Crear selección
      const x = Math.min(this.startX, this.currentX);
      const y = Math.min(this.startY, this.currentY);
      const width = Math.abs(this.currentX - this.startX);
      const height = Math.abs(this.currentY - this.startY);

      if (width > 5 && height > 5) {
        // Capturar la selección
        const imageData = this.ctx.getImageData(x, y, width, height);
        this.selection = {
          x: x,
          y: y,
          width: width,
          height: height,
          data: imageData,
        };
        this.drawSelectionBox(x, y, x + width, y + height);
      } else {
        this.selection = null;
        this.ctx.putImageData(this.snapshot, 0, 0);
      }
    }
  }

  // ============================================================
  // SELECCIÓN
  // ============================================================

  isInsideSelection(x, y) {
    if (!this.selection) return false;
    return (
      x >= this.selection.x &&
      x <= this.selection.x + this.selection.width &&
      y >= this.selection.y &&
      y <= this.selection.y + this.selection.height
    );
  }

  drawSelectionBox(x1, y1, x2, y2) {
    const x = Math.min(x1, x2);
    const y = Math.min(y1, y2);
    const width = Math.abs(x2 - x1);
    const height = Math.abs(y2 - y1);

    this.ctx.save();
    this.ctx.strokeStyle = "#6C63FF";
    this.ctx.lineWidth = 2;
    this.ctx.setLineDash([6, 6]);
    this.ctx.strokeRect(x, y, width, height);

    // Esquinas
    const cornerSize = 8;
    this.ctx.setLineDash([]);
    this.ctx.fillStyle = "#6C63FF";
    // Superior izquierda
    this.ctx.fillRect(x, y, cornerSize, 2);
    this.ctx.fillRect(x, y, 2, cornerSize);
    // Superior derecha
    this.ctx.fillRect(x + width - cornerSize, y, cornerSize, 2);
    this.ctx.fillRect(x + width - 2, y, 2, cornerSize);
    // Inferior izquierda
    this.ctx.fillRect(x, y + height - 2, cornerSize, 2);
    this.ctx.fillRect(x, y + height - cornerSize, 2, cornerSize);
    // Inferior derecha
    this.ctx.fillRect(x + width - cornerSize, y + height - 2, cornerSize, 2);
    this.ctx.fillRect(x + width - 2, y + height - cornerSize, 2, cornerSize);

    this.ctx.restore();
  }

  // ============================================================
  // MOVER
  // ============================================================

  moveSelection(x, y) {
    if (!this.selection) return;

    // Calcular nueva posición
    const newX = x - this.moveOffsetX;
    const newY = y - this.moveOffsetY;

    // Restaurar snapshot (limpiar área)
    this.ctx.putImageData(this.snapshot, 0, 0);

    // Dibujar selección en nueva posición
    const tempCanvas = document.createElement("canvas");
    tempCanvas.width = this.selection.width;
    tempCanvas.height = this.selection.height;
    const tempCtx = tempCanvas.getContext("2d");
    tempCtx.putImageData(this.selection.data, 0, 0);

    this.ctx.drawImage(tempCanvas, newX, newY);

    // Actualizar posición de selección
    this.selection.x = newX;
    this.selection.y = newY;

    // Dibujar borde de selección
    this.drawSelectionBox(
      newX,
      newY,
      newX + this.selection.width,
      newY + this.selection.height,
    );
  }

  // ============================================================
  // ACCIONES
  // ============================================================

  copy() {
    if (!this.selection) {
      this.editor.showToast("No hay selección para copiar", "warning");
      return;
    }

    this.clipboard = {
      x: 0,
      y: 0,
      width: this.selection.width,
      height: this.selection.height,
      data: this.selection.data,
    };

    this.editor.showToast("📋 Copiado", "success");
  }

  cut() {
    if (!this.selection) {
      this.editor.showToast("No hay selección para cortar", "warning");
      return;
    }

    // Copiar al portapapeles
    this.clipboard = {
      x: 0,
      y: 0,
      width: this.selection.width,
      height: this.selection.height,
      data: this.selection.data,
    };

    // Eliminar selección (rellenar con blanco)
    const imageData = this.ctx.getImageData(
      this.selection.x,
      this.selection.y,
      this.selection.width,
      this.selection.height,
    );
    const data = imageData.data;
    for (let i = 0; i < data.length; i += 4) {
      data[i] = 255;
      data[i + 1] = 255;
      data[i + 2] = 255;
      data[i + 3] = 255;
    }
    this.ctx.putImageData(imageData, this.selection.x, this.selection.y);

    this.selection = null;
    this.editor.history.saveState("Cortar");
    this.editor.showToast("✂️ Cortado", "success");
  }

  paste() {
    if (!this.clipboard) {
      this.editor.showToast("No hay nada en el portapapeles", "warning");
      return;
    }

    // Pegar en el centro del canvas
    const x = (this.canvas.width - this.clipboard.width) / 2;
    const y = (this.canvas.height - this.clipboard.height) / 2;

    // Crear un canvas temporal
    const tempCanvas = document.createElement("canvas");
    tempCanvas.width = this.clipboard.width;
    tempCanvas.height = this.clipboard.height;
    const tempCtx = tempCanvas.getContext("2d");
    tempCtx.putImageData(this.clipboard.data, 0, 0);

    // Dibujar en la capa activa
    this.layers.drawOnActiveLayer(0, 0, (data, width, height) => {
      const imgData = tempCtx.getImageData(
        0,
        0,
        this.clipboard.width,
        this.clipboard.height,
      );
      const srcData = imgData.data;

      for (let dy = 0; dy < this.clipboard.height; dy++) {
        for (let dx = 0; dx < this.clipboard.width; dx++) {
          const sx = Math.round(x + dx);
          const sy = Math.round(y + dy);

          if (sx >= 0 && sx < width && sy >= 0 && sy < height) {
            const srcIdx = (dy * this.clipboard.width + dx) * 4;
            const dstIdx = (sy * width + sx) * 4;

            data[dstIdx] = srcData[srcIdx];
            data[dstIdx + 1] = srcData[srcIdx + 1];
            data[dstIdx + 2] = srcData[srcIdx + 2];
            data[dstIdx + 3] = srcData[srcIdx + 3];
          }
        }
      }
    });

    this.editor.history.saveState("Pegar");
    this.editor.showToast("📄 Pegado", "success");
  }

  delete() {
    if (!this.selection) {
      this.editor.showToast("No hay selección para eliminar", "warning");
      return;
    }

    // Rellenar selección con blanco
    const imageData = this.ctx.getImageData(
      this.selection.x,
      this.selection.y,
      this.selection.width,
      this.selection.height,
    );
    const data = imageData.data;
    for (let i = 0; i < data.length; i += 4) {
      data[i] = 255;
      data[i + 1] = 255;
      data[i + 2] = 255;
      data[i + 3] = 255;
    }
    this.ctx.putImageData(imageData, this.selection.x, this.selection.y);

    this.selection = null;
    this.editor.history.saveState("Eliminar selección");
    this.editor.showToast("🗑️ Selección eliminada", "info");
  }

  clearSelection() {
    this.selection = null;
    this.isSelecting = false;
    this.isMoving = false;
    this.ctx.putImageData(this.snapshot, 0, 0);
  }
}

// Hacer disponible globalmente
window.SelectionTool = SelectionTool;
