const listaConversaciones = document.getElementById("listaConversaciones");

function cargarConversaciones() {
    fetch("php/obtener_conversaciones.php")
        .then(res => {
            if (!res.ok) {
                return "";
            }

            return res.text();
        })
        .then(html => {
            if (html.trim() !== "") {
                listaConversaciones.innerHTML = html;
            }
        })
        .catch(error => console.error(error));
}

setInterval(cargarConversaciones, 15000);
