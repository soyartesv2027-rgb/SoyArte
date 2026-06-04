// =======================
// FORMULARIO MULTI-STEP
// =======================

const steps = document.querySelectorAll(".step");
const nextBtns = document.querySelectorAll(".next");
const backBtns = document.querySelectorAll(".back");
const progress = document.getElementById("progress-bar");

let current = 0;

// Mostrar paso actual
function showStep(index) {

    steps.forEach(step => {
        step.classList.remove("active");
    });

    steps[index].classList.add("active");

    // actualizar barra de progreso
    let percent = ((index + 1) / steps.length) * 100;
    progress.style.width = percent + "%";
}

// BOTÓN SIGUIENTE
nextBtns.forEach(btn => {
    btn.addEventListener("click", () => {

        if (current < steps.length - 1) {
            current++;
            showStep(current);
        }

    });
});

// BOTÓN ATRÁS
backBtns.forEach(btn => {
    btn.addEventListener("click", () => {

        if (current > 0) {
            current--;
            showStep(current);
        }

    });
});

// iniciar
showStep(current);



// =======================
// SELECCIÓN DE CARDS
// =======================

const cards = document.querySelectorAll(".card");

cards.forEach(card => {

    card.addEventListener("click", () => {

        // quitar selección visual
        cards.forEach(c => c.classList.remove("selected"));

        // marcar la card seleccionada
        card.classList.add("selected");

        // activar el radio REAL dentro de la card
        const input = card.querySelector("input");

        if (input) {
            input.checked = true;
        }

    });

});