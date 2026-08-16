<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$tipos = mod_tipos();
$usuarioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($usuarioId <= 0) {
    header('Location: moderacion_usuarios.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if (!$usuario) {
    die('Usuario no encontrado.');
}

$conteos = mod_conteos_usuario($conn, $usuarioId);

// Publicaciones del usuario (últimas 10)
$publicaciones = [];
foreach ($tipos as $tk => $tc) {
    $sql = "SELECT `{$tc['id_col']}` AS id_contenido, `{$tc['titulo']}`, estado, `{$tc['fecha']}` AS fecha
            FROM `{$tc['tabla']}`
            WHERE `{$tc['usuario']}` = ?
            ORDER BY `{$tc['fecha']}` DESC
            LIMIT 10";
    $st = $conn->prepare($sql);
    $st->bind_param('i', $usuarioId);
    $st->execute();
    foreach ($st->get_result()->fetch_all(MYSQLI_ASSOC) as $f) {
        $f['tipo'] = $tk;
        $publicaciones[] = $f;
    }
}
usort($publicaciones, fn($a, $b) => strcmp($b['fecha'] ?? '', $a['fecha'] ?? ''));

// Denuncias recibidas
$sql = "SELECT d.*, COALESCE(p.nombre_pintura, m.nombre_cancion, o.titulo, ma.nombre) AS titulo_contenido,
        COALESCE(p.autor, m.nombre_cantante, o.autor, ma.autor) AS artista_contenido
        FROM denuncias d
        LEFT JOIN pinturas p ON d.tipo_contenido = 'pintura' AND p.ID = d.id_contenido AND p.id_usuario = ?
        LEFT JOIN musica m ON d.tipo_contenido = 'musica' AND m.musica_id = d.id_contenido AND m.usuario_id = ?
        LEFT JOIN obras o ON d.tipo_contenido = 'poesia' AND o.id = d.id_contenido AND o.usuario_id = ?
        LEFT JOIN manualidades ma ON d.tipo_contenido = 'manualidad' AND ma.id = d.id_contenido AND ma.usuario_id = ?
        WHERE (d.tipo_contenido, d.id_contenido) IN (
            SELECT 'pintura', ID FROM pinturas WHERE id_usuario = ?
            UNION ALL SELECT 'musica', musica_id FROM musica WHERE usuario_id = ?
            UNION ALL SELECT 'poesia', id FROM obras WHERE usuario_id = ?
            UNION ALL SELECT 'manualidad', id FROM manualidades WHERE usuario_id = ?
        )
        ORDER BY d.fecha DESC LIMIT 15";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiiiiiii', $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId, $usuarioId);
$stmt->execute();
$denunciasRecibidas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Advertencias
$stmt = $conn->prepare("SELECT a.*, u.nombre AS admin_nombre FROM advertencias a LEFT JOIN usuarios u ON u.id = a.admin_id WHERE a.usuario_id = ? ORDER BY a.fecha DESC");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$advertencias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Sanciones
$stmt = $conn->prepare("SELECT s.*, u.nombre AS admin_nombre FROM sanciones s LEFT JOIN usuarios u ON u.id = s.admin_id WHERE s.usuario_id = ? ORDER BY s.fecha DESC");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$sanciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Historial de moderación del usuario
$stmt = $conn->prepare("SELECT h.*, ua.nombre AS admin_nombre FROM moderacion_historial h LEFT JOIN usuarios ua ON ua.id = h.admin_id WHERE h.usuario_id = ? ORDER BY h.fecha DESC LIMIT 30");
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$historial = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil administrativo - Moderación SoyArte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styles/moderacion.css">
    <link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
</head>
<body>

<?php $seccion = 'general'; include("components/navbar-unificado.php"); ?>

<main class="mod-contenido">

    <div class="mod-encabezado-acciones">
        <div>
            <h1 class="mod-titulo-seccion" style="margin-bottom:0;">
                <i class="fa-solid fa-user-gear"></i> Perfil administrativo
            </h1>
            <div class="mod-subtitulo" style="margin-bottom:0;">@<?= htmlspecialchars($usuario['nombre'] ?? '—') ?> · #<?= $usuarioId ?></div>
        </div>
        <a href="moderacion_usuarios.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-arrow-left"></i> Volver a sancionados
        </a>
    </div>

    <!-- DATOS DEL USUARIO -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo"><i class="fa-solid fa-user"></i> Información de la cuenta</div>
        <div class="mod-detalle-info">
            <dl style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:4px 24px;">
                <div><dt>Usuario</dt><dd>@<?= htmlspecialchars($usuario['nombre'] ?? '—') ?></dd></div>
                <div><dt>Correo</dt><dd><?= htmlspecialchars($usuario['correo'] ?? '—') ?></dd></div>
                <div><dt>Fecha de registro</dt><dd><?= isset($usuario['fecha_registro']) ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : '—' ?></dd></div>
                <div><dt>Estado</dt><dd><?= mod_badge_usuario($usuario['estado'] ?? 'activo') ?></dd></div>
                <div><dt>Publicaciones</dt><dd><?= $conteos['publicaciones'] ?></dd></div>
                <div><dt>Denuncias recibidas</dt><dd><?= $conteos['denuncias'] ?></dd></div>
                <div><dt>Advertencias</dt><dd><?= $conteos['advertencias'] ?></dd></div>
                <div><dt>Suspensiones</dt><dd><?= $conteos['sanciones'] ?></dd></div>
            </dl>
        </div>
        <p style="margin-top:12px;">
            <?php foreach ($conteos['por_tipo'] as $tk => $cant): ?>
                <span class="mod-etiqueta" style="margin-right:6px;">
                    <i class="fa-solid <?= $tipos[$tk]['icono'] ?>"></i> <?= $tipos[$tk]['plural'] ?>: <?= $cant ?>
                </span>
            <?php endforeach; ?>
        </p>
    </div>

    <!-- PUBLICACIONES -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo"><i class="fa-solid fa-images"></i> Publicaciones recientes</div>
        <?php if ($publicaciones): ?>
            <div class="mod-tabla-wrap">
                <table class="mod-tabla" style="min-width:480px;">
                    <thead><tr><th>ID</th><th>Tipo</th><th>Título</th><th>Fecha</th><th>Estado</th><th>Ver</th></tr></thead>
                    <tbody>
                    <?php foreach ($publicaciones as $pub): ?>
                        <tr>
                            <td>#<?= (int)$pub['id_contenido'] ?></td>
                            <td><span class="mod-etiqueta"><?= $tipos[$pub['tipo']]['plural'] ?></span></td>
                            <td><?= htmlspecialchars($pub[$tipos[$pub['tipo']]['titulo']]) ?></td>
                            <td><?= date('d/m/Y', strtotime($pub['fecha'] ?? 'now')) ?></td>
                            <td><?= mod_badge_publicacion($pub['estado'] ?? 'publicada') ?></td>
                            <td><a href="moderacion_detalle.php?tipo=<?= $pub['tipo'] ?>&id=<?= (int)$pub['id_contenido'] ?>" class="mod-boton mod-boton-secundario" style="padding:4px 10px; font-size:0.8rem;"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Este usuario no tiene publicaciones.</p>
        <?php endif; ?>
    </div>

    <!-- DENUNCIAS RECIBIDAS -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo"><i class="fa-solid fa-flag"></i> Denuncias recibidas</div>
        <?php if ($denunciasRecibidas): ?>
            <div class="mod-tabla-wrap">
                <table class="mod-tabla" style="min-width:480px;">
                    <thead><tr><th>#</th><th>Contenido</th><th>Motivo</th><th>Fecha</th><th>Estado</th><th>Ver</th></tr></thead>
                    <tbody>
                    <?php foreach ($denunciasRecibidas as $d): ?>
                        <tr>
                            <td>#<?= (int)$d['id'] ?></td>
                            <td><?= htmlspecialchars(mb_substr($d['titulo_contenido'] ?? '—', 0, 35)) ?></td>
                            <td><?= htmlspecialchars($d['motivo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>
                            <td><?= mod_badge_denuncia($d['estado']) ?></td>
                            <td><a href="moderacion_detalle.php?tipo=<?= $d['tipo_contenido'] ?>&id=<?= (int)$d['id_contenido'] ?>" class="mod-boton mod-boton-secundario" style="padding:4px 10px; font-size:0.8rem;"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Este usuario no ha recibido denuncias.</p>
        <?php endif; ?>
    </div>

    <!-- ADVERTENCIAS -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo"><i class="fa-solid fa-message"></i> Advertencias</div>
        <?php if ($advertencias): ?>
            <?php foreach ($advertencias as $a): ?>
                <div class="mod-actividad">
                    <div class="mod-actividad-icono">💬</div>
                    <div>
                        <div class="mod-actividad-texto">
                            <strong><?= htmlspecialchars($a['admin_nombre'] ?? 'Admin') ?></strong> · <?= htmlspecialchars($a['motivo']) ?>
                            <span class="mod-etiqueta" style="margin-left:6px;"><?= date('d/m/Y', strtotime($a['fecha'])) ?></span>
                        </div>
                        <div style="font-size:0.85rem; color:#374151;"><?= nl2br(htmlspecialchars($a['mensaje'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted mb-0">Sin advertencias.</p>
        <?php endif; ?>
    </div>

    <!-- SANCIONES -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo"><i class="fa-solid fa-triangle-exclamation"></i> Sanciones</div>
        <?php if ($sanciones): ?>
            <div class="mod-tabla-wrap">
                <table class="mod-tabla" style="min-width:480px;">
                    <thead><tr><th>#</th><th>Administrador</th><th>Tipo</th><th>Motivo</th><th>Fecha</th><th>Correo</th><th>Vigente</th></tr></thead>
                    <tbody>
                    <?php foreach ($sanciones as $s): ?>
                        <tr>
                            <td>#<?= (int)$s['id'] ?></td>
                            <td><?= htmlspecialchars($s['admin_nombre'] ?? '—') ?></td>
                            <td>
                                <?php if ($s['tipo_sancion'] === 'suspension'): ?>
                                    <span class="badge text-bg-warning">🟠 Suspensión</span>
                                <?php else: ?>
                                    <span class="badge text-bg-dark">⚫ Eliminación</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($s['motivo']) ?></td>
                            <td><?= date('d/m/Y', strtotime($s['fecha'])) ?></td>
                            <td><?= $s['correo_enviado'] ? '📧 Sí' : '—' ?></td>
                            <td><?= $s['vigente'] ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">Sin sanciones.</p>
        <?php endif; ?>
    </div>

    <!-- HISTORIAL -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo"><i class="fa-solid fa-clock-rotate-left"></i> Historial de moderación</div>
        <?php if ($historial): ?>
            <?php foreach ($historial as $h): ?>
                <div class="mod-actividad">
                    <div class="mod-actividad-icono"><?= mod_icono_accion($h['accion']) ?></div>
                    <div>
                        <div class="mod-actividad-texto">
                            <strong><?= htmlspecialchars($h['admin_nombre'] ?? 'Admin') ?></strong>
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $h['accion']))) ?>
                            <?php if (!empty($h['tipo_contenido']) && !empty($h['id_contenido'])): ?>
                                en <a href="moderacion_detalle.php?tipo=<?= $h['tipo_contenido'] ?>&id=<?= (int)$h['id_contenido'] ?>">
                                    <?= $tipos[$h['tipo_contenido']]['etiqueta'] ?> #<?= (int)$h['id_contenido'] ?>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($h['motivo'])): ?>
                                <span class="mod-etiqueta"><?= htmlspecialchars($h['motivo']) ?></span>
                            <?php endif; ?>
                            <?php if ($h['correo_enviado']): ?>
                                <span class="mod-etiqueta">📧 Correo enviado</span>
                            <?php endif; ?>
                        </div>
                        <div class="mod-actividad-fecha"><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></div>
                        <?php if (!empty($h['mensaje'])): ?>
                            <div style="font-size:0.85rem; color:#374151;"><?= nl2br(htmlspecialchars($h['mensaje'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted mb-0">Sin historial de moderación.</p>
        <?php endif; ?>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>