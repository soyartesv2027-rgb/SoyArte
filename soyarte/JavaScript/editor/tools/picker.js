/**
 * ============================================================
 * picker.js - Herramienta cuentagotas
 * Toma el color de un punto del canvas
 * ============================================================
 */

class PickerTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  onMouseDown(x, y) {
    this.pickColor(x, y);
  }

  onMouseMove(x, y) {
    // No hacer nada
  }

  onMouseUp() {
    // No hacer nada
  }

  // ============================================================
  // TOMAR COLOR
  // ============================================================

  pickColor(x, y) {
    try {
      // Obtener el color del píxel en la posición (x, y)
      const imageData = this.ctx.getImageData(
        Math.floor(x),
        Math.floor(y),
        1,
        1,
      );

      const px = imageData.data;

      // Si el píxel es transparente, usar blanco
      if (px[3] < 10) {
        this.editor.showToast("🎨 Color transparente, usando blanco", "info");
        this.editor.state.color = "#ffffff";
        this.updateColorUI("#ffffff");
        return;
      }

      // Convertir RGB a HEX
      const hex =
        "#" +
        px[0].toString(16).padStart(2, "0") +
        px[1].toString(16).padStart(2, "0") +
        px[2].toString(16).padStart(2, "0");

      // Actualizar el color en el editor
      this.editor.state.color = hex;
      this.updateColorUI(hex);

      this.editor.showToast("🎨 Color tomado: " + hex, "success");
    } catch (e) {
      console.error("Error al tomar color:", e);
      this.editor.showToast("❌ Error al tomar color", "error");
    }
  }

  // ============================================================
  // ACTUALIZAR UI
  // ============================================================

  updateColorUI(hex) {
    // Actualizar el input de color
    const colorPicker = document.getElementById("colorPicker");
    if (colorPicker) {
      colorPicker.value = hex;
    }

    // Actualizar el cursor
    if (this.editor) {
      this.editor.updateCursor();
    }

    // Actualizar el preview en propiedades
    const colorInputs = document.querySelectorAll(".color-input");
    colorInputs.forEach((input) => {
      if (input.id === "colorPicker" || input.type === "color") {
        input.value = hex;
      }
    });
  }
}

window.PickerTool = PickerTool;
