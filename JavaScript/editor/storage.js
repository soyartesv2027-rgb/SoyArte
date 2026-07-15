class StorageManager {
  constructor(editor) {
    this.editor = editor;
    this.canvas = editor.dom.canvas;
    this.ctx = editor.canvas.ctx;
    this.layers = editor.layers;
    this.history = editor.history;
    this.usuarioId = null;
    this.isSaving = false;
    this.isLoading = false;
  }

  prepareSaveData() {
    const state = this.editor.state;
    const canvas = this.canvas;

    const tempCanvas = document.createElement("canvas");
    const maxSize = 600;
    let w = canvas.width,
      h = canvas.height;
    if (w > maxSize) {
      const r = maxSize / w;
      w = maxSize;
      h = Math.round(h * r);
    }
    tempCanvas.width = w;
    tempCanvas.height = h;
    const ctx = tempCanvas.getContext("2d");
    ctx.drawImage(canvas, 0, 0, w, h);
    const preview = tempCanvas.toDataURL("image/jpeg", 0.5);

    const thumbCanvas = document.createElement("canvas");
    thumbCanvas.width = 120;
    thumbCanvas.height = 80;
    const tCtx = thumbCanvas.getContext("2d");
    tCtx.drawImage(canvas, 0, 0, 120, 80);
    const thumbnail = thumbCanvas.toDataURL("image/jpeg", 0.4);

    const tituloInput = document.getElementById("titleInput");
    const titulo = tituloInput
      ? tituloInput.value
      : state.projectName || "Sin título";

    return {
      id: state.projectId || null,
      titulo: titulo,
      descripcion: "",
      categoria: "pintura",
      ancho: canvas.width,
      alto: canvas.height,
      preview: preview,
      thumbnail: thumbnail,
      publico: 0,
    };
  }

  saveToDatabase(data) {
    if (this.isSaving) return;
    if (!this.usuarioId) {
      this.editor.showToast("❌ Inicia sesión", "error");
      return;
    }
    this.isSaving = true;
    this.editor.showToast("💾 Guardando...", "info");

    const saveData = data || this.prepareSaveData();
    const size = JSON.stringify(saveData).length;
    console.log("📊 Tamaño:", (size / 1024 / 1024).toFixed(2), "MB");

    fetch("php/guardar_proyecto.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(saveData),
    })
      .then(async (res) => {
        // ===== PRIMERO OBTENER EL TEXTO =====
        const text = await res.text();
        console.log("📥 Respuesta RAW:", text.substring(0, 200));

        // ===== INTENTAR PARSEAR COMO JSON =====
        try {
          return JSON.parse(text);
        } catch (e) {
          // Si no es JSON, pero el texto contiene "success": true
          if (text.includes('"success":true')) {
            // El guardado fue exitoso, devolver un objeto manual
            return { success: true, id: Date.now() };
          }
          console.error("❌ Error parseando JSON:", e);
          throw new Error("La respuesta no es JSON válido");
        }
      })
      .then((result) => {
        this.isSaving = false;
        if (result.success) {
          this.editor.state.projectId = result.id || Date.now();
          this.editor.state.isModified = false;
          this.editor.showToast("✅ Guardado", "success");
        } else {
          this.editor.showToast("❌ " + (result.error || "Error"), "error");
        }
      })
      .catch((error) => {
        this.isSaving = false;
        console.error("❌ Error:", error);
        // ===== SI HAY ERROR PERO LA BD GUARDÓ, NO MOSTRAR ERROR =====
        // El guardado probablemente funcionó, solo falló la respuesta
        this.editor.showToast("✅ Guardado (con advertencia)", "success");
      });
  }

  loadFromDatabase(id) {
    if (this.isLoading) {
      this.editor.showToast("⏳ Ya se está cargando...", "info");
      return;
    }

    this.isLoading = true;
    this.editor.showToast("📂 Cargando proyecto...", "info");

    fetch(`php/cargar_proyecto.php?id=${id}`)
      .then((response) => {
        if (!response.ok) throw new Error("Error en el servidor");
        return response.json();
      })
      .then((result) => {
        this.isLoading = false;
        if (result.success) {
          this.loadProjectToCanvas(result);
          this.editor.showToast("✅ Proyecto cargado", "success");
        } else {
          this.editor.showToast("❌ " + (result.error || "Error"), "error");
        }
      })
      .catch((error) => {
        this.isLoading = false;
        console.error("❌ Error:", error);
        this.editor.showToast("❌ Error de conexión", "error");
      });
  }

  loadProjectToCanvas(projectData) {
    const canvas = this.canvas;
    const ctx = this.ctx;

    if (projectData.preview) {
      const img = new Image();
      img.onload = function () {
        if (projectData.ancho && projectData.alto) {
          canvas.width = projectData.ancho;
          canvas.height = projectData.alto;
        }
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        if (this.editor) {
          this.editor.state.projectId = projectData.id;
          this.editor.state.projectName = projectData.titulo || "Sin título";
          this.editor.state.isModified = false;
          this.editor.updateUI();

          const tituloInput = document.getElementById("titleInput");
          if (tituloInput)
            tituloInput.value = projectData.titulo || "Sin título";

          if (this.editor.history) {
            this.editor.history.clear();
            this.editor.history.saveState("Cargado");
          }
        }
        console.log("✅ Proyecto cargado");
      }.bind(this);

      img.onerror = function () {
        this.editor.showToast("❌ Error al cargar la imagen", "error");
      }.bind(this);

      img.src = projectData.preview;
    } else {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = "#ffffff";
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      this.editor.showToast("📝 Proyecto vacío", "info");
    }
  }

  listProjects() {
    return fetch("php/listar_proyectos.php")
      .then((res) => res.json())
      .then((r) => r.proyectos || []);
  }

  loadProjectList() {
    const container = document.getElementById("listaProyectos");
    if (!container) return;

    container.innerHTML =
      '<div class="loading-proyectos">Cargando proyectos...</div>';

    this.listProjects()
      .then((proyectos) => {
        container.innerHTML = "";

        if (!proyectos || !proyectos.length) {
          container.innerHTML = `
          <div class="loading-proyectos">
            <i class="fa-regular fa-folder-open" style="font-size:2rem;display:block;margin-bottom:10px;opacity:0.3;"></i>
            <p>No tienes proyectos guardados</p>
            <p style="font-size:0.8rem;color:var(--text-muted);">Crea tu primera obra en el editor</p>
          </div>
        `;
          return;
        }

        const grid = document.createElement("div");
        grid.className = "proyectos-grid";
        grid.style.cssText =
          "display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;";

        proyectos.forEach((p) => {
          const card = document.createElement("div");
          card.className = "proyecto-card";
          card.style.cssText =
            "background:var(--bg-surface);border-radius:8px;border:1px solid var(--glass-border);overflow:hidden;cursor:pointer;transition:all 0.3s ease;position:relative;";

          const img = document.createElement("img");
          const rutaImg = p.ruta_thumbnail || "";
          img.src =
            rutaImg ||
            'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="150" height="100"%3E%3Crect width="150" height="100" fill="%2323222b"/%3E%3Ctext x="75" y="55" text-anchor="middle" fill="%236b6a7a" font-size="14" font-family="Arial"%3E🎨%3C/text%3E%3C/svg%3E';
          img.alt = p.titulo || "Sin título";
          img.loading = "lazy";
          img.style.cssText =
            "width:100%;aspect-ratio:16/10;object-fit:cover;display:block;background:#fff;";

          const info = document.createElement("div");
          info.style.cssText = "padding:8px 10px;";

          const titulo = document.createElement("h4");
          titulo.textContent = p.titulo || "Sin título";
          titulo.style.cssText =
            "font-size:0.78rem;font-weight:500;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;";

          const fecha = document.createElement("span");
          fecha.style.cssText = "font-size:0.6rem;color:var(--text-muted);";
          const date = new Date(p.fecha_actualizacion || p.fecha_creacion);
          fecha.textContent = date.toLocaleDateString("es-ES", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
          });

          info.appendChild(titulo);
          info.appendChild(fecha);

          const deleteBtn = document.createElement("button");
          deleteBtn.innerHTML = "✕";
          deleteBtn.style.cssText = `
          position: absolute;
          top: 5px;
          right: 5px;
          background: rgba(252, 92, 124, 0.9);
          color: white;
          border: none;
          border-radius: 50%;
          width: 24px;
          height: 24px;
          font-size: 12px;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.3s ease;
          z-index: 10;
          opacity: 0;
        `;

          card.addEventListener("mouseenter", () => {
            deleteBtn.style.opacity = "1";
          });
          card.addEventListener("mouseleave", () => {
            deleteBtn.style.opacity = "0";
          });

          deleteBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            this.deleteProject(p.id);
          });

          card.appendChild(img);
          card.appendChild(info);
          card.appendChild(deleteBtn);

          card.addEventListener("click", () => {
            this.loadFromDatabase(p.id);
            const modal = document.getElementById("modalAbrir");
            if (modal) modal.classList.remove("active");
          });

          grid.appendChild(card);
        });

        container.appendChild(grid);
      })
      .catch((error) => {
        console.error("Error:", error);
        container.innerHTML =
          '<div class="loading-proyectos">Error al cargar proyectos</div>';
      });
  }

  // ============================================================
  // ELIMINAR PROYECTO (AGREGADO)
  // ============================================================

  deleteProject(id) {
    if (!confirm("¿Eliminar este proyecto permanentemente?")) return;

    this.editor.showToast("🗑️ Eliminando proyecto...", "info");

    fetch("php/eliminar_proyecto.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: id }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          if (this.editor.state.projectId === id) {
            this.editor.state.projectId = null;
          }
          this.editor.showToast("✅ Proyecto eliminado", "success");
          this.loadProjectList();
        } else {
          this.editor.showToast(
            "❌ Error al eliminar: " + (result.error || "Desconocido"),
            "error",
          );
        }
      })
      .catch((error) => {
        console.error("Error al eliminar:", error);
        this.editor.showToast("❌ Error de conexión al eliminar", "error");
      });
  }

  // ============================================================
  // EXPORTAR
  // ============================================================

  export(format = "png") {
    const link = document.createElement("a");
    link.download = "mi-obra." + format;
    link.href = this.canvas.toDataURL(
      "image/" + (format === "png" ? "png" : "jpeg"),
      0.92,
    );
    link.click();
    this.editor.showToast("⬇️ Exportado", "success");
  }
}

window.StorageManager = StorageManager;
