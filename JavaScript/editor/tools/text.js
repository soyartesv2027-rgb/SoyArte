/**
 * ============================================================
 * text.js - Herramienta de texto
 * Permite agregar texto con diferentes fuentes y tamaños
 * ============================================================
 */

class TextTool {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;

    this.isActive = false;
    this.startX = 0;
    this.startY = 0;
    this.text = "";
    this.font = "Arial";
    this.size = 32;
    this.color = "#000000";
  }

  // ============================================================
  // EVENTOS
  // ============================================================

  onMouseDown(x, y) {
    this.startX = x;
    this.startY = y;
    this.showTextInput(x, y);
  }

  onMouseMove(x, y) {
    // No hacer nada
  }

  onMouseUp() {
    // No hacer nada
  }

  // ============================================================
  // INPUT DE TEXTO - VERSIÓN SIMPLIFICADA Y ESTABLE
  // ============================================================

  showTextInput(x, y) {
    // Obtener el color actual del editor
    this.color = this.editor.state.color;
    this.size = this.editor.state.textSize || 32;

    // Crear un contenedor para el input
    const container = document.createElement("div");
    container.id = "textInputContainer";
    container.style.cssText = `
            position: fixed;
            z-index: 9999;
            pointer-events: none;
        `;

    // Convertir coordenadas de canvas a pantalla
    const rect = this.canvas.getBoundingClientRect();
    const scaleX = rect.width / this.canvas.width;
    const scaleY = rect.height / this.canvas.height;
    const left = rect.left + x * scaleX;
    const top = rect.top + y * scaleY;

    // Crear el input
    const input = document.createElement("input");
    input.type = "text";
    input.placeholder = "Escribe tu texto...";
    input.value = "";
    input.style.cssText = `
            pointer-events: auto;
            padding: 8px 14px;
            border: 2px solid #6C63FF;
            border-radius: 8px;
            background: #1a1921;
            color: #f0edf6;
            font-size: ${this.size}px;
            font-family: ${this.font};
            outline: none;
            min-width: 200px;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5);
            position: absolute;
            left: ${left}px;
            top: ${top}px;
        `;

    container.appendChild(input);
    document.body.appendChild(container);

    // Enfocar el input
    setTimeout(() => {
      input.focus();
      input.select();
    }, 50);

    // ===== EVENTOS DEL INPUT =====

    // Keydown - Capturar teclas importantes
    input.addEventListener(
      "keydown",
      (e) => {
        // Enter = finalizar y colocar texto
        if (e.key === "Enter") {
          e.preventDefault();
          e.stopPropagation();
          this.text = input.value.trim();
          if (this.text) {
            this.placeText(this.startX, this.startY);
          }
          this.cleanup(container);
          return;
        }

        // Escape = cancelar
        if (e.key === "Escape") {
          e.preventDefault();
          e.stopPropagation();
          this.cleanup(container);
          return;
        }

        // Evitar que atajos del editor se activen
        if (e.ctrlKey || e.metaKey) {
          e.stopPropagation();
        }
      },
      true,
    );

    // Keyup - Permitir escritura normal
    input.addEventListener(
      "keyup",
      (e) => {
        // Solo detener propagación para evitar que el editor capture teclas
        e.stopPropagation();
      },
      true,
    );

    // Blur - Si se pierde el foco, guardar el texto
    input.addEventListener("blur", (e) => {
      // Pequeño delay para que no se active si se hizo clic en el input
      setTimeout(() => {
        if (document.body.contains(container)) {
          this.text = input.value.trim();
          if (this.text) {
            this.placeText(this.startX, this.startY);
          }
          this.cleanup(container);
        }
      }, 200);
    });

    // Prevenir que el input propague eventos al canvas
    input.addEventListener("mousedown", (e) => {
      e.stopPropagation();
    });

    input.addEventListener("mouseup", (e) => {
      e.stopPropagation();
    });

    input.addEventListener("click", (e) => {
      e.stopPropagation();
    });
  }

  // ============================================================
  // LIMPIAR
  // ============================================================

  cleanup(container) {
    if (container && document.body.contains(container)) {
      document.body.removeChild(container);
    }
    // Restaurar el foco al canvas
    if (this.canvas) {
      this.canvas.focus();
    }
  }

  // ============================================================
  // COLOCAR TEXTO
  // ============================================================

  placeText(x, y) {
    if (!this.text) return;

    const state = this.editor.state;
    const color = state.color;
    const size = this.editor.state.textSize || this.size;
    const font = this.editor.state.textFont || this.font;

    let fontStyle = "";
    if (this.isBold) fontStyle += "bold ";
    if (this.isItalic) fontStyle += "italic ";
    fontStyle += `${size}px "${font}"`;

    this.layers.drawOnActiveLayer(0, 0, (data, width, height) => {
      const tempCanvas = document.createElement("canvas");
      tempCanvas.width = width;
      tempCanvas.height = height;
      const tempCtx = tempCanvas.getContext("2d");

      tempCtx.putImageData(this.ctx.getImageData(0, 0, width, height), 0, 0);

      tempCtx.save();
      tempCtx.fillStyle = color;
      tempCtx.font = fontStyle;
      tempCtx.textBaseline = "top";
      tempCtx.fillText(this.text, x, y);
      tempCtx.restore();

      const tempData = tempCtx.getImageData(0, 0, width, height);
      for (let i = 0; i < data.length; i++) {
        data[i] = tempData.data[i];
      }
    });

    // Restaurar el foco al canvas
    if (this.canvas) {
      this.canvas.focus();
    }

    this.editor.history.saveState("Texto");
    this.editor.showToast("📝 Texto agregado", "success");

    // Limpiar el texto para la próxima vez
    this.text = "";
  }

  // ============================================================
  // CONFIGURACIÓN
  // ============================================================

  setFont(font) {
    this.font = font;
  }

  setSize(size) {
    this.size = size;
  }

  setColor(color) {
    this.color = color;
  }

  setBold(bold) {
    this.isBold = bold;
  }

  setItalic(italic) {
    this.isItalic = italic;
  }

  setUnderline(underline) {
    this.isUnderline = underline;
  }
}

window.TextTool = TextTool;
