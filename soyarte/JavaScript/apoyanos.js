let selectedAmount = 5;

// Selección de monto
document.querySelectorAll(".amount").forEach(btn => {
  btn.addEventListener("click", () => {

    document.querySelectorAll(".amount").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    selectedAmount = btn.dataset.value;
  });
});

// Botón donar
document.getElementById("donateBtn").addEventListener("click", () => {

  window.open(
    "https://www.paypal.com/donate/?hosted_button_id=9XV2PKG34BNYA",
    "_blank"
  );

});

// Botón regresar
document.getElementById("backBtn").addEventListener("click", () => {
  history.back();
});