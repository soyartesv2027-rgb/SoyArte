const form     = document.getElementById('form-perfil');
const mensaje  = document.getElementById('mensaje');

// --- 1. Al cargar la página, pedimos los datos actuales del usuario ---
window.addEventListener('DOMContentLoaded', () => {
  fetch('obtener-perfil.php')
    .then(res => res.json())
    .then(data => {
      if (data.error) return mostrarMensaje(data.error, 'error');

      document.getElementById('nombre').value           = data.nombre           || '';
      document.getElementById('correo').value           = data.correo           || '';
      document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento || '';
      document.getElementById('pais').value             = data.pais             || '';
      document.getElementById('biografia').value        = data.biografia        || '';

      // Mostramos nombre y correo en el encabezado de la tarjeta
      document.getElementById('nombre-display').textContent = data.nombre || 'Mi Perfil';
      document.getElementById('correo-display').textContent = data.correo || '';
    })
    .catch(() => mostrarMensaje('No se pudieron cargar los datos.', 'error'));
});

// --- 2. Al enviar el formulario, mandamos los cambios ---
form.addEventListener('submit', (e) => {
  e.preventDefault(); // Evitamos que la página se recargue

  const password        = document.getElementById('password').value;
  const passwordConfirm = document.getElementById('password_confirm').value;

  // Validamos que las contraseñas coincidan si el usuario las llenó
  if (password && password !== passwordConfirm) {
    return mostrarMensaje('Las contraseñas no coinciden.', 'error');
  }

  // Juntamos todos los datos del formulario
  const datos = new FormData(form);

  fetch('guardar-perfil.php', {
    method: 'POST',
    body: datos
  })
    .then(res => res.json())
    .then(data => {
      if (data.exito) {
        mostrarMensaje('¡Cambios guardados correctamente! 🎉', 'exito');
        // Actualizamos el nombre que aparece en el encabezado
        document.getElementById('nombre-display').textContent = document.getElementById('nombre').value;
      } else {
        mostrarMensaje(data.error || 'Algo salió mal.', 'error');
      }
    })
    .catch(() => mostrarMensaje('Error de conexión con el servidor.', 'error'));
});

// --- Función auxiliar para mostrar mensajes ---
function mostrarMensaje(texto, tipo) {
  mensaje.textContent = texto;
  mensaje.className   = `mensaje ${tipo}`; // 'exito' o 'error'
  mensaje.scrollIntoView({ behavior: 'smooth', block: 'center' });
}