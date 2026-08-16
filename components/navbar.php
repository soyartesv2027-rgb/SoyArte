<?php
// =============================================
// NAVBAR - SOY ARTE
// IMPORTANTE:
// session_start() debe ir antes del include
// =============================================
 
$mensajesPendientes = 0;
 
if (isset($_SESSION['usuario_id'])) {
    require_once __DIR__ . '/../php/conexion.php';
 
    $usuarioNavbar = (int)$_SESSION['usuario_id'];
    $sqlMensajesNavbar = "SELECT COUNT(*) AS total
                          FROM mensajes m
                          INNER JOIN conversaciones c
                          ON m.conversacion_id = c.id
                          WHERE m.emisor_id <> ?
                          AND m.leido = 0
                          AND
                          (
                              (
                                  c.usuario1_id = ?
                                  AND c.oculto_usuario1 = 0
                              )
                              OR
                              (
                                  c.usuario2_id = ?
                                  AND c.oculto_usuario2 = 0
                              )
                          )";
    $stmtMensajesNavbar = $conn->prepare($sqlMensajesNavbar);
 
    if ($stmtMensajesNavbar) {
        $stmtMensajesNavbar->bind_param("iii", $usuarioNavbar, $usuarioNavbar, $usuarioNavbar);
        $stmtMensajesNavbar->execute();
        $resultadoMensajesNavbar = $stmtMensajesNavbar->get_result()->fetch_assoc();
        $mensajesPendientes = (int)$resultadoMensajesNavbar['total'];
    }
}
?>
<!-- OVERLAY -->
<div id="overlay"></div>
 
<!-- SIDEBAR -->
<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3">
 
    <!-- TITULO -->
    <a href="index.php" class="d-flex align-items-center mb-3 text-decoration-none">
        <span class="fs-4 text-dark fw-bold">
            Soy Arte
        </span>
    </a>
 
    <hr>
 
    <!-- MENU -->
    <ul class="nav nav-pills flex-column mb-auto">
 
        <li class="nav-item mb-2">
            <a href="index.php" class="nav-link active">
                <i class="fa-solid fa-house me-2"></i>
                Inicio
            </a>
        </li>
 
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-pintura.html" class="nav-link link-dark">
                <i class="fa-solid fa-image me-2"></i>
                Pinturas
            </a>
        </li>
 
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-musica.html" class="nav-link link-dark">
                <i class="fa-solid fa-music"></i>
                Musica
            </a>
        </li>
 
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-poesia.html" class="nav-link link-dark">
                <i class="fa-solid fa-feather-pointed"></i>
                Poesia
            </a>
        </li>
 
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-manualidades.html" class="nav-link link-dark">
                <i class="fa-solid fa-cube"></i>
                Manualidades
            </a>
        </li>
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-realidad.html" class="nav-link link-dark">
                <i class="fa-solid fa-vr-cardboard"></i>
                Realidad Virtual
            </a>
        </li>
            <li class="mb-2">
            <a href="Pantalla-de-carga/PC-shop.html" class="nav-link link-dark">
            <i class="fa-solid fa-cart-shopping"></i>
                Tienda
            </a>
        </li>
        <li class="mb-2">
            <a href="Pantalla-de-carga/PC-comunidad.html" class="nav-link link-dark">
            <i class="fa-solid fa-comments me-2"></i>
                Comunidad
            </a>
        </li>
    </ul>
 
   
 
 
 
</div>
 
<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg shadow-sm" style="background-color: #2c4e7e;">
 
    <div class="container-fluid">
 
        <!-- BOTON MENU -->
        <svg
             id="menuBtn"
                class="menu-btn"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 640"
                width="30"
                height="30"
                style="cursor:pointer; flex-shrink:0;">
 
            <path
                fill="white"
                d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/>
        </svg>
 
        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white"
            href="index.php">
 
           
 
            <img
                src="images/Logo-Blanco.png"
                alt="Arty"
                width="100px"
                ;>
        </a>
 
            <!-- SESION -->
            <div class="ms-auto d-flex align-items-center">
 
                <?php if (isset($_SESSION['usuario_id'])): ?>
 
                    <!-- USUARIO LOGUEADO -->
                    <div class="dropdown">
 
                        <a class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
 
                            <i class="fa-solid fa-circle-user"></i>
 
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
 
                        </a>
 
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
 
                            <li>
                                <a class="dropdown-item" href="perfil.php">
                                    <i class="fa-solid fa-id-card me-2"></i>
                                    Perfil
                                </a>
                            </li>
 
<li>
                                <a class="dropdown-item" href="mensajes.php">
                                    <i class="fa-solid fa-message me-2"></i>
                                    <span
                                        id="textoMensajesMenu"
                                        data-base="Mensajes">
                                        Mensajes<?php echo $mensajesPendientes > 0 ? " (" . $mensajesPendientes . ")" : ""; ?>
                                    </span>
                                </a>
                            </li>

                            <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                            <li>
                                <a class="dropdown-item" href="moderacion.php">
                                    <i class="fa-solid fa-shield-halved me-2"></i>
                                    Moderación
                                </a>
                            </li>
                            <?php endif; ?>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
 
                            <li>
                                <a class="dropdown-item text-danger"
                                    href="php/logout.php">
 
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>
                                    Cerrar sesión
 
                                </a>
                            </li>
 
                        </ul>
 
                    </div>
 
                <?php else: ?>
 
                    <!-- SIN SESION -->
                    <a class="btn btn-outline-light me-2"
                        href="login.html">
 
                        Login
 
                    </a>
 
                    <a class="btn btn-primary"
                        href="register.html">
 
                        Registrarse
 
                    </a>
 
                <?php endif; ?>
 
            </div>
 
        </div>
 
    </div>
 
</nav>
 
<?php if (isset($_SESSION['usuario_id'])): ?>
<script>
const textoMensajesMenu = document.getElementById("textoMensajesMenu");
 
function actualizarContadorMensajes() {
    if (!textoMensajesMenu) {
        return;
    }
 
    fetch("php/contador_mensajes.php")
        .then(res => res.json())
        .then(data => {
            const total = Number(data.total || 0);
            textoMensajesMenu.textContent = total > 0
                ? "Mensajes (" + total + ")"
                : "Mensajes";
        })
        .catch(error => console.error(error));
}
 
const intervaloNavbar = setInterval(actualizarContadorMensajes, 15000);
window.addEventListener("beforeunload", function () {
    clearInterval(intervaloNavbar);
});
</script>
<?php endif; ?>
 