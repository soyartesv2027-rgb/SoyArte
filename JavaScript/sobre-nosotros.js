// =============================================
// SOBRE NOSOTROS - SOY ARTE
// JavaScript específico para la página
// =============================================

// =============================================
// INICIALIZAR AOS (ANIMATE ON SCROLL)
// =============================================

document.addEventListener('DOMContentLoaded', function () {

    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });

    // =============================================
    // REFRESCAR AOS AL CAMBIAR DE SLIDE
    // =============================================

    const carrusel = document.getElementById('heroCarrusel');

    if (carrusel) {
        carrusel.addEventListener('slid.bs.carousel', function () {
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        });
    }

    // =============================================
    // EFECTO PARALLAX EN BACKGROUND DEL HERO
    // =============================================

    const slides = document.querySelectorAll('.hero-slide-cine');

    slides.forEach(function (slide) {
        slide.addEventListener('mousemove', function (e) {
            const rect = slide.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * 6;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * 6;
            slide.style.backgroundPosition = (50 + x) + '% ' + (50 + y) + '%';
        });

        slide.addEventListener('mouseleave', function () {
            slide.style.backgroundPosition = '';
        });
    });

});
