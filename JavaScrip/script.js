// ===== ELEMENTOS =====

const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");


// ===== ABRIR / CERRAR SIDEBAR =====

menuBtn.addEventListener("click", () => {

    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");

});


// ===== CERRAR AL DAR CLICK EN EL OVERLAY =====

overlay.addEventListener("click", () => {

    sidebar.classList.remove("active");
    overlay.classList.remove("active");

});