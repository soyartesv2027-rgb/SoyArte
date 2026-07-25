// =============================================
// COMUNIDAD (FORO) - SOY ARTE
// =============================================

document.addEventListener('DOMContentLoaded', function () {

    // =============================================
    // LIKE / UNLIKE EN TEMAS Y RESPUESTAS
    // =============================================

    document.querySelectorAll('.btn-like, .btn-like-resp').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tipo = this.dataset.tipo;
            var targetId = this.dataset.target;

            fetch('comunidad/procesos/reaccionar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tipo=' + encodeURIComponent(tipo) + '&target_id=' + encodeURIComponent(targetId)
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    if (data.accion === 'inserto') {
                        btn.classList.add('liked');
                    } else {
                        btn.classList.remove('liked');
                    }
                    var contador = btn.querySelector('.like-count');
                    if (contador) {
                        contador.textContent = data.total;
                    }
                }
            })
            .catch(function (err) {
                console.error('Error al reaccionar:', err);
            });
        });
    });

    // =============================================
    // CONFIRMAR ELIMINACIÓN DE RESPUESTA
    // =============================================

    document.querySelectorAll('.btn-eliminar-respuesta').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('¿Eliminar esta respuesta?')) {
                e.preventDefault();
            }
        });
    });

});
