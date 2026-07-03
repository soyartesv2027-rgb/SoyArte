const formulario = document.getElementById("formMensaje");
const cajaMensajes = document.getElementById("mensajes");
const inputConversacion = document.getElementById("conversacion");
const inputMensaje = document.getElementById("mensaje");
const botonEmoji = document.getElementById("btnEmoji");
const botonImagen = document.getElementById("btnImagen");
const inputImagen = document.getElementById("imagenChat");
const previewImagen = document.getElementById("previewImagen");
const imgPreview = document.getElementById("imgPreview");
const cancelarImagen = document.getElementById("cancelarImagen");
const enviarImagen = document.getElementById("enviarImagen");
const visorImagen = document.getElementById("visorImagen");
const imagenGrande = document.getElementById("imagenGrande");
const cerrarVisor = document.getElementById("cerrarVisor");
const conversacion = inputConversacion.value;

function limpiarImagenSeleccionada() {
    inputImagen.value = "";
    imgPreview.src = "";
    previewImagen.hidden = true;
}

function enviarMensaje() {
    const mensaje = inputMensaje.value.trim();

    if (mensaje === "" && inputImagen.files.length === 0) {
        return;
    }

    const datos = new FormData();
    datos.append("conversacion", conversacion);
    datos.append("mensaje", mensaje);

    if (inputImagen.files.length > 0) {
        datos.append("imagen", inputImagen.files[0]);
    }

    fetch("php/enviar_mensaje.php", {
        method: "POST",
        body: datos
    })
        .then(res => res.text())
        .then(res => {
            if (res.trim() === "OK") {
                inputMensaje.value = "";
                limpiarImagenSeleccionada();
                cargarMensajes();
                return;
            }

            alert(res);
        })
        .catch(error => {
            console.error(error);
            alert("No se pudo enviar el mensaje.");
        });
}

function marcarEntregado() {
    const datos = new FormData();
    datos.append("conversacion", conversacion);

    fetch("php/marcar_entregado.php", {
        method: "POST",
        body: datos
    }).catch(error => console.error(error));
}

function marcarLeidos() {
    const datos = new FormData();
    datos.append("conversacion", conversacion);

    fetch("php/marcar_leidos.php", {
        method: "POST",
        body: datos
    }).catch(error => console.error(error));
}

function cargarMensajes() {
    const estabaAbajo =
        cajaMensajes.scrollHeight - cajaMensajes.scrollTop <= cajaMensajes.clientHeight + 80;

    fetch("php/obtener_mensajes.php?conversacion=" + encodeURIComponent(conversacion))
        .then(res => res.text())
        .then(html => {
            cajaMensajes.innerHTML = html;
            marcarLeidos();

            if (estabaAbajo) {
                cajaMensajes.scrollTop = cajaMensajes.scrollHeight;
            }
        })
        .catch(error => console.error(error));
}

formulario.addEventListener("submit", function (e) {
    e.preventDefault();
    enviarMensaje();
});

inputMensaje.addEventListener("keydown", function (e) {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        formulario.requestSubmit();
    }
});

botonEmoji.addEventListener("click", function () {
    inputMensaje.value += String.fromCodePoint(0x1F60A);
    inputMensaje.focus();
});

botonImagen.addEventListener("click", function () {
    inputImagen.click();
});

inputImagen.addEventListener("change", function () {
    if (this.files.length === 0) {
        return;
    }

    const archivo = this.files[0];

    if (archivo.size > 5 * 1024 * 1024) {
        alert("La imagen no puede superar los 5 MB.");
        limpiarImagenSeleccionada();
        return;
    }

    if (!archivo.type.startsWith("image/")) {
        alert("Solo se permiten imagenes.");
        limpiarImagenSeleccionada();
        return;
    }

    const lector = new FileReader();

    lector.onload = function (e) {
        imgPreview.src = e.target.result;
        previewImagen.hidden = false;
    };

    lector.readAsDataURL(archivo);
});

cancelarImagen.addEventListener("click", limpiarImagenSeleccionada);
enviarImagen.addEventListener("click", enviarMensaje);

function abrirImagen(src) {
    imagenGrande.src = src;
    visorImagen.style.display = "flex";
}

window.abrirImagen = abrirImagen;

cerrarVisor.addEventListener("click", function () {
    visorImagen.style.display = "none";
});

visorImagen.addEventListener("click", function (e) {
    if (e.target.id === "visorImagen") {
        this.style.display = "none";
    }
});

marcarEntregado();
cargarMensajes();
setInterval(cargarMensajes, 20000);
