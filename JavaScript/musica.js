// Manejo del menú lateral (si existe)
const menuBtnMusic = document.getElementById("btn-like");
const sidebarMusic = document.getElementById("sidebarMusic");
const overlayMusic = document.getElementById("overlayMusic");

if (menuBtnMusic) {
    menuBtnMusic.addEventListener("click", () => {
        sidebarMusic.classList.toggle("active");
        overlayMusic.classList.toggle("active");
    });
}

if (overlayMusic) {
    overlayMusic.addEventListener("click", () => {
        sidebarMusic.classList.remove("active");
        overlayMusic.classList.remove("active");
    });
}

// ============ FUNCIONALIDAD DE LIKES ============
document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los botones de like
    const botonesLike = document.querySelectorAll('.btn-like');
    
    botonesLike.forEach(boton => {
        boton.addEventListener('click', function(e) {
            // Detener la propagación del evento
            e.preventDefault();
            e.stopPropagation();
            
            // Verificar si el usuario está logueado
            const usuarioId = parseInt(this.dataset.usuario);
            if (usuarioId === 0) {
                if (confirm('Por favor, inicia sesión para dar like. ¿Quieres ir al login?')) {
                    window.location.href = 'login.php';
                }
                return;
            }
            
            // Obtener el ID de la música
            const musicaId = this.dataset.id;
            
            // Llamar a la función para manejar el like
            manejarLike(musicaId, this);
        });
    });
});

// Función para manejar el like con AJAX
function manejarLike(musicaId, boton) {
    // Guardar el estado actual del ícono
    const icono = boton.querySelector('i');
    const contador = boton.querySelector('.contador-like');
    const esLiked = icono.classList.contains('fa-solid');
    const likesActuales = parseInt(contador.textContent);
    
    // Deshabilitar el botón temporalmente
    boton.disabled = true;
    
    // Mostrar animación de carga
    icono.className = 'fa-solid fa-spinner fa-spin';
    
    // Realizar petición AJAX
    fetch('like_musica.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'musica_id=' + musicaId
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Actualizar el contador
            contador.textContent = data.likes;
            
            // Cambiar el icono (corazón lleno/vacío)
            if (data.liked) {
                icono.className = 'fa-solid fa-heart';
                // Animación de like
                icono.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    icono.style.transform = 'scale(1)';
                }, 200);
            } else {
                icono.className = 'fa-regular fa-heart';
            }
            
            // Mostrar un pequeño feedback
            boton.style.transition = 'all 0.2s ease';
            boton.style.transform = 'scale(1.1)';
            setTimeout(() => {
                boton.style.transform = 'scale(1)';
            }, 200);
        } else {
            // Si hay error, restaurar el estado anterior
            if (esLiked) {
                icono.className = 'fa-solid fa-heart';
            } else {
                icono.className = 'fa-regular fa-heart';
            }
            contador.textContent = likesActuales;
            console.error('Error del servidor:', data.error);
            
            // Mostrar mensaje de error
            if (data.error === 'Usuario no autenticado') {
                if (confirm('Tu sesión ha expirado. ¿Quieres iniciar sesión nuevamente?')) {
                    window.location.href = 'login.php';
                }
            } else {
                alert('Error al procesar el like: ' + data.error);
            }
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        // Restaurar el estado anterior
        if (esLiked) {
            icono.className = 'fa-solid fa-heart';
        } else {
            icono.className = 'fa-regular fa-heart';
        }
        contador.textContent = likesActuales;
        alert('Error de conexión. Por favor, intenta de nuevo.');
    })
    .finally(() => {
        // Habilitar el botón nuevamente
        boton.disabled = false;
    });
}

// ============ BUSCADOR ============
const buscador = document.getElementById("buscador");
if (buscador) {
    buscador.addEventListener("keyup", function() {
        let filtro = buscador.value.toLowerCase();
        let tarjetas = document.querySelectorAll(".tarjeta-musica");
        
        tarjetas.forEach(tarjeta => {
            let cancion = tarjeta
                .querySelector(".nombre-cancion")
                .textContent
                .toLowerCase();
            
            let cantante = tarjeta
                .querySelector(".nombre-cantante")
                .textContent
                .toLowerCase();
            
            if (cancion.includes(filtro) || cantante.includes(filtro)) {
                tarjeta.closest('.card-wrapper').style.display = "block";
            } else {
                tarjeta.closest('.card-wrapper').style.display = "none";
            }
        });
    });
}

// ============ SCROLL ANIMATION ============
window.addEventListener("scroll", () => {
    const section = document.querySelector(".info-soyarte");
    if(section) {
        const position = section.getBoundingClientRect().top;
        const screen = window.innerHeight;
        if(position < screen - 100) {
            section.classList.add("visible");
        }
    }
});