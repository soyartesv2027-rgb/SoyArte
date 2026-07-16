<?php
session_start();
require_once __DIR__ . '/../php/conexion.php';
require_once __DIR__ . '/funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.html");
    exit();
}

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header("Location: foro.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT t.*, u.nombre AS autor_nombre, u.foto_perfil AS autor_foto, u.id AS autor_id,
           c.nombre AS cat_nombre, c.slug AS cat_slug, c.icono AS cat_icono, c.color AS cat_color
    FROM foro_temas t
    JOIN usuarios u ON t.usuario_id = u.id
    JOIN foro_categorias c ON t.categoria_id = c.id
    WHERE t.slug = ?
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$tema = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tema) {
    header("Location: foro.php");
    exit();
}

$usuario_actual = (int)$_SESSION['usuario_id'];

$ya_visto = $conn->prepare("SELECT id FROM foro_visitas WHERE tema_id=? AND usuario_id=?");
$ya_visto->bind_param("ii", $tema['id'], $usuario_actual);
$ya_visto->execute();
if (!$ya_visto->get_result()->fetch_row()) {
    $ya_visto->close();
    $insert_visita = $conn->prepare("INSERT INTO foro_visitas (tema_id, usuario_id) VALUES (?, ?)");
    $insert_visita->bind_param("ii", $tema['id'], $usuario_actual);
    $insert_visita->execute();
    $insert_visita->close();

    $update_vistas = $conn->prepare("UPDATE foro_temas SET vistas = vistas + 1 WHERE id = ?");
    $update_vistas->bind_param("i", $tema['id']);
    $update_vistas->execute();
    $update_vistas->close();
} else {
    $ya_visto->close();
}

$tema_like = usuarioReacciono($conn, $usuario_actual, 'tema', $tema['id']);
$tema_likes = contarReacciones($conn, 'tema', $tema['id']);

$respuestas = $conn->prepare("
    SELECT r.*, u.nombre AS autor_nombre, u.foto_perfil AS autor_foto, u.id AS autor_id
    FROM foro_respuestas r
    JOIN usuarios u ON r.usuario_id = u.id
    WHERE r.tema_id = ?
    ORDER BY r.created_at ASC
");
$respuestas->bind_param("i", $tema['id']);
$respuestas->execute();
$resultado_resp = $respuestas->get_result();
$respuestas->close();

$mensaje = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'vacio') $mensaje = ['tipo' => 'error', 'texto' => 'El contenido o archivo es obligatorio.'];
    if ($_GET['error'] === 'error') $mensaje = ['tipo' => 'error', 'texto' => 'Ocurrió un error al enviar la respuesta.'];
    if ($_GET['error'] === 'formato') $mensaje = ['tipo' => 'error', 'texto' => 'Formato de archivo no permitido. Solo imágenes (jpg, png, gif, webp) y audio (mp3, wav, ogg, aac, m4a).'];
    if ($_GET['error'] === 'peso') $mensaje = ['tipo' => 'error', 'texto' => 'El archivo supera el tamaño máximo de 10 MB.'];
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'resp_eliminada') $mensaje = ['tipo' => 'success', 'texto' => 'Respuesta eliminada.'];
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tema['titulo']); ?> - Comunidad SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../favicon_io/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/comunidad.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include("../components/navbar.php"); ?>

    <div class="foro-header" style="padding:40px 0 30px;">
        <a href="categoria.php?slug=<?php echo urlencode($tema['cat_slug']); ?>" class="foro-btn foro-btn-back" style="margin-bottom:10px;">
            <i class="fa-solid fa-arrow-left"></i> Volver a <?php echo htmlspecialchars($tema['cat_nombre']); ?>
        </a>
        <p style="font-size:0.9rem;margin-bottom:6px;">
            <a href="foro.php" style="color:#9ca3af;text-decoration:none;">Comunidad</a>
            <span style="color:#6b7280;"> / </span>
            <a href="categoria.php?slug=<?php echo urlencode($tema['cat_slug']); ?>" style="color:#d1d5db;text-decoration:none;">
                <i class="fa-solid <?php echo $tema['cat_icono']; ?>" style="color:<?php echo $tema['cat_color']; ?>"></i>
                <?php echo htmlspecialchars($tema['cat_nombre']); ?>
            </a>
        </p>
        <h1 style="font-size:1.8rem;">
            <?php echo htmlspecialchars($tema['titulo']); ?>
            <?php if ($tema['es_fijado']): ?><span style="font-size:0.7rem;background:#fef3c7;color:#92400e;padding:3px 12px;border-radius:50px;margin-left:8px;vertical-align:middle;"><i class="fa-solid fa-thumbtack"></i> Fijado</span><?php endif; ?>
            <?php if ($tema['es_cerrado']): ?><span style="font-size:0.7rem;background:#fee2e2;color:#991b1b;padding:3px 12px;border-radius:50px;margin-left:6px;vertical-align:middle;"><i class="fa-solid fa-lock"></i> Cerrado</span><?php endif; ?>
        </h1>
    </div>

    <div class="foro-container">

        <?php if ($mensaje): ?>
            <div class="foro-alert foro-alert-<?php echo $mensaje['tipo']; ?>">
                <i class="fa-solid fa-<?php echo $mensaje['tipo'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $mensaje['texto']; ?>
            </div>
        <?php endif; ?>

        <!-- POST PRINCIPAL -->
        <div class="tema-post">
            <div class="post-header">
                <img src="<?php echo fotoPerfil($tema['autor_foto']); ?>" alt="">
                <div class="post-autor">
                    <strong><a href="../perfil.php?id=<?php echo $tema['autor_id']; ?>"><?php echo htmlspecialchars($tema['autor_nombre']); ?></a></strong>
                    <span><?php echo tiempoRelativo($tema['created_at']); ?></span>
                </div>
                <?php if (esAdmin()): ?>
                    <div class="admin-actions-post">
                        <form method="POST" action="procesos/fijar_tema.php" style="display:inline;">
                            <input type="hidden" name="tema_id" value="<?php echo $tema['id']; ?>">
                            <input type="hidden" name="slug" value="<?php echo htmlspecialchars($tema['slug']); ?>">
                            <button type="submit" name="accion" value="<?php echo $tema['es_fijado'] ? 'desfijar' : 'fijar'; ?>" class="foro-btn foro-btn-sm foro-btn-outline" title="Fijar/Desfijar">
                                <i class="fa-solid fa-thumbtack"></i>
                            </button>
                        </form>
                        <form method="POST" action="procesos/cerrar_tema.php" style="display:inline;">
                            <input type="hidden" name="tema_id" value="<?php echo $tema['id']; ?>">
                            <input type="hidden" name="slug" value="<?php echo htmlspecialchars($tema['slug']); ?>">
                            <button type="submit" name="accion" value="<?php echo $tema['es_cerrado'] ? 'abrir' : 'cerrar'; ?>" class="foro-btn foro-btn-sm foro-btn-outline" title="Cerrar/Abrir">
                                <i class="fa-solid fa-<?php echo $tema['es_cerrado'] ? 'unlock' : 'lock'; ?>"></i>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <div class="post-body">
                <?php echo nl2br(htmlspecialchars($tema['contenido'])); ?>
            </div>
            <div class="post-footer">
                <button class="btn-like <?php echo $tema_like ? 'liked' : ''; ?>" data-tipo="tema" data-target="<?php echo $tema['id']; ?>">
                    <i class="fa-solid fa-heart"></i>
                    <span class="like-count"><?php echo $tema_likes; ?></span>
                </button>
                <span style="font-size:0.82rem;color:var(--foro-text-light);">
                    <i class="fa-regular fa-eye"></i> <?php echo $tema['vistas']; ?> vistas
                </span>
            </div>
        </div>

        <!-- RESPUESTAS -->
        <div class="respuestas-section" id="respuestas">
            <h3>Respuestas (<?php echo $resultado_resp->num_rows; ?>)</h3>

            <?php if ($resultado_resp->num_rows > 0): ?>
                <?php while ($resp = $resultado_resp->fetch_assoc()):
                    $resp_like = usuarioReacciono($conn, $usuario_actual, 'respuesta', $resp['id']);
                    $resp_likes = contarReacciones($conn, 'respuesta', $resp['id']);
                ?>
                    <div class="respuesta-item">
                        <img src="<?php echo fotoPerfil($resp['autor_foto']); ?>" alt="" class="resp-avatar">
                        <div class="resp-body">
                            <div class="resp-header">
                                <strong><a href="../perfil.php?id=<?php echo $resp['autor_id']; ?>"><?php echo htmlspecialchars($resp['autor_nombre']); ?></a></strong>
                                <span><?php echo tiempoRelativo($resp['created_at']); ?></span>
                                <?php if ($resp['updated_at']): ?>
                                    <span style="font-size:0.75rem;color:#9ca3af;">(editado)</span>
                                <?php endif; ?>
                            </div>
                            <div class="resp-contenido">
                                <?php if ($resp['contenido']): ?>
                                    <?php echo nl2br(htmlspecialchars($resp['contenido'])); ?>
                                <?php endif; ?>
                                <?php if ($resp['tipo'] === 'imagen' && $resp['archivo']): ?>
                                    <div class="resp-archivo-img">
                                        <img src="../uploads/foro/<?php echo htmlspecialchars($resp['archivo']); ?>" alt="Imagen adjunta" loading="lazy" onclick="abrirVisor(this.src)">
                                    </div>
                                <?php elseif ($resp['tipo'] === 'audio' && $resp['archivo']): ?>
                                    <div class="resp-archivo-audio">
                                        <audio controls preload="metadata">
                                            <source src="../uploads/foro/<?php echo htmlspecialchars($resp['archivo']); ?>">
                                        </audio>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="resp-acciones">
                                <button class="btn-like-resp <?php echo $resp_like ? 'liked' : ''; ?>" data-tipo="respuesta" data-target="<?php echo $resp['id']; ?>">
                                    <i class="fa-solid fa-heart"></i>
                                    <span class="like-count"><?php echo $resp_likes; ?></span>
                                </button>
                                <?php if ($usuario_actual === (int)$resp['usuario_id'] || esAdmin()): ?>
                                    <form method="POST" action="procesos/eliminar_respuesta.php" style="display:inline;" onsubmit="return confirm('¿Eliminar esta respuesta?')">
                                        <input type="hidden" name="respuesta_id" value="<?php echo $resp['id']; ?>">
                                        <input type="hidden" name="slug" value="<?php echo htmlspecialchars($tema['slug']); ?>">
                                        <button type="submit" class="btn-eliminar"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="foro-empty" style="padding:30px 20px;">
                    <p>No hay respuestas aún. ¡Sé el primero en responder!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- FORMULARIO DE RESPUESTA -->
        <?php if ($tema['es_cerrado']): ?>
            <div class="tema-cerrado-aviso">
                <i class="fa-solid fa-lock"></i> Este tema está cerrado. No se pueden agregar nuevas respuestas.
            </div>
        <?php else: ?>
            <form class="foro-form" method="POST" action="procesos/responder.php" enctype="multipart/form-data">
                <h4><i class="fa-solid fa-reply"></i> Responder</h4>
                <input type="hidden" name="tema_id" value="<?php echo $tema['id']; ?>">
                <input type="hidden" name="slug" value="<?php echo htmlspecialchars($tema['slug']); ?>">
                <div class="form-group">
                    <textarea name="contenido" rows="5" placeholder="Escribe tu respuesta (opcional si adjuntas archivo)..."></textarea>
                </div>
                <div class="form-group foro-adjuntar">
                    <label class="foro-file-label">
                        <i class="fa-solid fa-paperclip"></i> Adjuntar imagen o audio
                        <input type="file" name="archivo" id="foroArchivo" accept="image/*,audio/*" style="display:none;">
                    </label>
                    <span id="foroFileName" class="foro-file-name"></span>
                    <div id="foroPreview" class="foro-preview" style="display:none;"></div>
                </div>
                <button type="submit" class="foro-btn foro-btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Publicar respuesta
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- VISOR DE IMÁGENES -->
    <div id="visorImagen" class="foro-visor" onclick="this.style.display='none'">
        <span class="foro-visor-cerrar">&times;</span>
        <img id="imagenGrande" src="" alt="Imagen">
    </div>

    <?php include("../components/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/script.js"></script>
    <script src="../JavaScript/comunidad.js?v=<?php echo time(); ?>"></script>
    <script>
    function abrirVisor(src) {
        document.getElementById('imagenGrande').src = src;
        document.getElementById('visorImagen').style.display = 'flex';
    }

    document.getElementById('foroArchivo')?.addEventListener('change', function() {
        var preview = document.getElementById('foroPreview');
        var fileName = document.getElementById('foroFileName');
        var file = this.files[0];
        if (file) {
            fileName.textContent = file.name;
            var reader = new FileReader();
            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    preview.style.display = 'block';
                } else {
                    preview.innerHTML = '<i class="fa-solid fa-music"></i> Archivo de audio: ' + file.name;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        } else {
            fileName.textContent = '';
            preview.style.display = 'none';
        }
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>
