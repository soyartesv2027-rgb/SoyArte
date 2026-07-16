const steps = document.querySelectorAll(".step");
const nextBtns = document.querySelectorAll(".next");
const backBtns = document.querySelectorAll(".back");
const dots = document.querySelectorAll(".step-dot");
const lines = document.querySelectorAll(".step-line");
const stepLabel = document.getElementById("stepLabel");
const errorMsg = document.getElementById("errorMsg");

let current = 0;
const totalSteps = steps.length;

function showStep(index, goingBack) {
    steps.forEach((step, i) => {
        step.classList.remove("active", "step-back");
    });

    const el = steps[index];
    el.classList.add("active");
    if (goingBack) el.classList.add("step-back");

    dots.forEach((dot, i) => {
        dot.classList.remove("active", "completed");
        if (i === index) dot.classList.add("active");
        else if (i < index) dot.classList.add("completed");
    });

    lines.forEach((line, i) => {
        line.classList.remove("completed");
        if (i < index) line.classList.add("completed");
    });

    const labels = ["Tipo de usuario", "Intereses", "Experiencia", "Comunidad"];
    stepLabel.textContent = "Paso " + (index + 1) + " de " + totalSteps + " — " + labels[index];

    hideError();

    setTimeout(() => {
        const firstInput = el.querySelector("input, select, textarea");
        if (firstInput) firstInput.focus();
    }, 100);
}

function validateStep(index) {
    const step = steps[index];

    if (index === 0) {
        const checked = step.querySelector('input[name="tipo_usuario"]:checked');
        if (!checked) {
            showError("Selecciona qué tipo de usuario eres");
            return false;
        }
    }

    if (index === 1) {
        const checked = step.querySelectorAll('input[name="intereses[]"]:checked');
        if (checked.length === 0) {
            showError("Selecciona al menos un área de interés");
            return false;
        }
    }

    if (index === 2) {
        const tutorial = step.querySelector('select[name="tipo_tutorial"]');
        if (!tutorial.value) {
            showError("Selecciona un formato de contenido");
            tutorial.focus();
            return false;
        }
        const frecuencia = step.querySelector('select[name="frecuencia"]');
        if (!frecuencia.value) {
            showError("Selecciona tu frecuencia de uso");
            frecuencia.focus();
            return false;
        }
        const aprendizaje = step.querySelector('textarea[name="manualidades"]');
        if (!aprendizaje.value.trim()) {
            showError("Escribe qué te gustaría aprender");
            aprendizaje.focus();
            return false;
        }
    }

    return true;
}

function showError(msg) {
    errorMsg.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + msg;
    errorMsg.classList.add("show");
    errorMsg.scrollIntoView({ behavior: "smooth", block: "nearest" });
}

function hideError() {
    errorMsg.classList.remove("show");
}

nextBtns.forEach(btn => {
    btn.addEventListener("click", () => {
        if (!validateStep(current)) return;
        if (current < totalSteps - 1) {
            current++;
            showStep(current, false);
        }
    });
});

backBtns.forEach(btn => {
    btn.addEventListener("click", () => {
        if (current > 0) {
            current--;
            showStep(current, true);
        }
    });
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        const activeStep = steps[current];
        const btn = activeStep.querySelector(".next") || activeStep.querySelector('button[type="submit"]');
        if (btn) btn.click();
    }
});

// =======================
// SELECCIÓN DE CARDS
// =======================

const cards = document.querySelectorAll(".card");

cards.forEach(card => {
    card.addEventListener("click", () => {
        cards.forEach(c => c.classList.remove("selected"));
        card.classList.add("selected");
        const input = card.querySelector("input");
        if (input) input.checked = true;
    });
});

showStep(0, false);
