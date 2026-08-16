<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$tipos = mod_tipos();
$tipo = isset($_GET['tipo']) ? mod_tipo_valido($_GET['tipo']) : null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$tipo || $id <= 0) {
    header('Location: moderacion.php');
    exit;
}

$cfg = $tipos[$tipo];
$fila = mod_obtener_contenido($conn, $tipo, $id);

if (!$fila) {
    die('Publicación no encontrada.');
}

// Al abrir el detalle, las denuncias pendientes pasan a "en revisión"
$stmt = $conn->prepare("UPDATE denuncias SET estado = 'en_revision' WHERE tipo_contenido = ? AND id_contenido = ? AND estado = 'pendiente'");
$stmt->bind_param('si', $tipo, $id);
$stmt->execute();

$denuncias = mod_denuncias_contenido($conn, $tipo, $id);
$totalDenuncias = count($denuncias);
$porMotivo = mod_denuncias_por_motivo($conn, $tipo, $id);

$usuarioId = (int)$fila['usuario_id'];
$conteos = mod_conteos_usuario($conn, $usuarioId);

$usuarioObj = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$usuarioObj->bind_param('i', $usuarioId);
$usuarioObj->execute();
$usuario = $usuarioObj->get_result()->fetch_assoc();

$descripcion = $fila['descripcion'] ?? $fila['contenido'] ?? '';
$tituloDetalle = 'Detalle de ' . $cfg['etiqueta'];

$estadoDenunciaUnico = null;
if ($totalDenuncias > 0) {
    $estadosD = array_unique(array_column($denuncias, 'estado'));
    $estadoDenunciaUnico = count($estadosD) === 1 ? $estadosD[0] : null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloDetalle ?> - Moderación SoyArte</title>
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
                <i class="fa-solid <?= $cfg['icono'] ?>"></i> <?= $tituloDetalle ?>
            </h1>
            <div class="mod-subtitulo" style="margin-bottom:0;">
                Publicación #<?= $id ?> · <?= $cfg['plural'] ?>
            </div>
        </div>
        <div>
            <a href="<?= mod_url_publica($tipo, $id) ?>" target="_blank" class="mod-enlace-rapido">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver público
            </a>
            <a href="moderacion_todas.php?tipo=<?= $tipo ?>" class="mod-enlace-rapido">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php if (($fila['estado'] ?? 'publicada') !== 'publicada'): ?>
        <div class="mod-aviso-publicacion">
            <strong>Aviso:</strong> esta publicación está <?= $fila['estado'] === 'oculta' ? 'oculta' : 'eliminada' ?>. No es visible para el público.
        </div>
    <?php endif; ?>

    <!-- ============ INFORMACIÓN DE LA PUBLICACIÓN ============ -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo">
            <i class="fa-solid fa-image"></i> Información de la publicación
        </div>
        <div class="mod-detalle-grid">
            <div>
                <?php $srcImg = mod_imagen_src($tipo, $fila); ?>
                <?php if ($srcImg): ?>
                    <img src="<?= $srcImg ?>" class="mod-detalle-imagen" alt="">
                <?php else: ?>
                    <div class="mod-miniatura-sin-img" style="width:100%; height:260px; font-size:60px;">
                        <i class="fa-solid <?= $cfg['icono'] ?>"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="mod-detalle-info">
                <dl>
                    <dt>Título</dt>
                    <dd><?= mod_titulo($tipo, $fila) ?></dd>

                    <dt>Descripción</dt>
                    <dd style="white-space:pre-wrap;"><?= nl2br(htmlspecialchars($descripcion)) ?></dd>

                    <dt>Artista</dt>
                    <dd><?= htmlspecialchars($fila['nombre_usuario'] ?? $fila['autor'] ?? '—') ?></dd>

                    <dt>Categoría</dt>
                    <dd><?= $cfg['plural'] ?></dd>

                    <dt>Fecha de publicación</dt>
                    <dd><?= mod_fecha($tipo, $fila) ?></dd>

                    <dt>ID de la publicación</dt>
                    <dd>#<?= $id ?></dd>

                    <dt>Estado actual</dt>
                    <dd><?= mod_badge_publicacion($fila['estado'] ?? 'publicada') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- ============ INFORMACIÓN DE DENUNCIAS ============ -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo">
            <i class="fa-solid fa-flag"></i> Información de denuncias
            <span class="mod-etiqueta">Total: <?= $totalDenuncias ?></span>
        </div>

        <?php if ($totalDenuncias > 0): ?>

            <?php if ($estadoDenunciaUnico): ?>
                <p>Estado: <?= mod_badge_denuncia($estadoDenunciaUnico) ?></p>
            <?php else: ?>
                <p>Estado: varias denuncias en distintos estados</p>
            <?php endif; ?>

            <div style="margin:16px 0;">
                <?php foreach ($porMotivo as $m): ?>
                    <div class="mod-motivo-fila">
                        <span><?= htmlspecialchars($m['motivo']) ?></span>
                        <span class="cantidad"><?= (int)$m['cantidad'] ?> denuncia(s)</span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mod-tabla-wrap">
                <table class="mod-tabla" style="min-width:480px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Denunciante</th>
                            <th>Motivo</th>
                            <th>Detalle</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($denuncias as $d): ?>
                            <tr>
                                <td>#<?= (int)$d['id'] ?></td>
                                <td><?= htmlspecialchars($d['denunciante'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($d['motivo']) ?></td>
                                <td><?= htmlspecialchars(mb_substr($d['descripcion'] ?? '', 0, 60)) ?: '—' ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($d['fecha'])) ?></td>
                                <td><?= mod_badge_denuncia($d['estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <p class="text-muted mb-0">Esta publicación no tiene denuncias.</p>
        <?php endif; ?>
    </div>

    <!-- ============ INFORMACIÓN DEL ARTISTA ============ -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo">
            <i class="fa-solid fa-user"></i> Información del artista
        </div>
        <div class="mod-detalle-info">
            <dl style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:4px 24px;">
                <div><dt>Usuario</dt><dd>@<?= htmlspecialchars($usuario['nombre'] ?? '—') ?></dd></div>
                <div><dt>Correo</dt><dd><?= htmlspecialchars($usuario['correo'] ?? '—') ?></dd></div>
                <div><dt>Fecha de registro</dt><dd><?= isset($usuario['fecha_registro']) ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : '—' ?></dd></div>
                <div><dt>Publicaciones</dt><dd><?= $conteos['publicaciones'] ?></dd></div>
                <div><dt>Denuncias recibidas</dt><dd><?= $conteos['denuncias'] ?></dd></div>
                <div><dt>Advertencias</dt><dd><?= $conteos['advertencias'] ?></dd></div>
                <div><dt>Sanciones (suspensiones)</dt><dd><?= $conteos['sanciones'] ?></dd></div>
                <div><dt>Estado de la cuenta</dt><dd><?= mod_badge_usuario($usuario['estado'] ?? 'activo') ?></dd></div>
            </dl>
        </div>
        <a href="moderacion_usuario.php?id=<?= $usuarioId ?>" class="mod-enlace-rapido" style="margin-top:12px;">
            <i class="fa-solid fa-user-gear"></i> Ver perfil administrativo
        </a>
    </div>

    <!-- ============ ACCIONES SOBRE LA PUBLICACIÓN ============ -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo">
            <i class="fa-solid fa-screwdriver-wrench"></i> Acciones sobre la publicación
        </div>
        <div class="mod-acciones-grid">
            <form method="post" action="php/mod_acciones.php">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="accion" value="mantener">
                <input type="hidden" name="tipo" value="<?= $tipo ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="mod-boton mod-boton-exito" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-check"></i> Mantener publicación
                </button>
            </form>
            <form method="post" action="php/mod_acciones.php">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="accion" value="ocultar">
                <input type="hidden" name="tipo" value="<?= $tipo ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="mod-boton mod-boton-aviso" style="width:100%; justify-content:center;" onclick="return confirm('¿Ocultar esta publicación? Dejará de ser visible públicamente.');">
                    <i class="fa-solid fa-eye-slash"></i> Ocultar publicación
                </button>
            </form>
            <form method="post" action="php/mod_acciones.php">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="accion" value="eliminar_publicacion">
                <input type="hidden" name="tipo" value="<?= $tipo ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <button type="submit" class="mod-boton mod-boton-peligro" style="width:100%; justify-content:center;" onclick="return confirm('¿Eliminar esta publicación? Se marcará como eliminada.');">
                    <i class="fa-solid fa-trash"></i> Eliminar publicación
                </button>
            </form>
        </div>
    </div>

    <!-- ============ ACCIONES SOBRE EL ARTISTA ============ -->
    <div class="mod-bloque">
        <div class="mod-bloque-titulo">
            <i class="fa-solid fa-user-shield"></i> Acciones sobre el artista
        </div>
        <div class="mod-acciones-grid">
            <button type="button" class="mod-boton mod-boton-primary" style="width:100%; justify-content:center;" data-bs-toggle="modal" data-bs-target="#modalAdvertencia">
                <i class="fa-solid fa-message"></i> Enviar advertencia
            </button>
            <button type="button" class="mod-boton mod-boton-aviso" style="width:100%; justify-content:center;" data-bs-toggle="modal" data-bs-target="#modalSuspender">
                <i class="fa-solid fa-triangle-exclamation"></i> Suspender usuario
            </button>
            <button type="button" class="mod-boton mod-boton-peligro" style="width:100%; justify-content:center;" data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario">
                <i class="fa-solid fa-user-slash"></i> Eliminar usuario
            </button>
        </div>
    </div>

    <!-- ============ MODAL ADVERTENCIA ============ -->
    <div class="modal fade" id="modalAdvertencia" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="php/mod_acciones.php" class="modal-content">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="accion" value="advertencia">
                <input type="hidden" name="tipo" value="<?= $tipo ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="usuario_id" value="<?= $usuarioId ?>">
                <div class="modal-header" style="background:#eef4fb;">
                    <h5 class="modal-title"><i class="fa-solid fa-message"></i> Enviar advertencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Usuario: <strong>@<?= htmlspecialchars($usuario['nombre'] ?? '') ?></strong></p>
                    <label class="form-label fw-semibold">Motivo</label>
                    <select name="motivo" class="form-select mb-3">
                        <?php foreach (mod_motivos() as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="form-label fw-semibold">Mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="4" placeholder="Escribe el mensaje de la advertencia..." required></textarea>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="enviar_correo" value="1" id="correoAdv">
                        <label class="form-check-label" for="correoAdv">Enviar notificación por correo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="mod-boton mod-boton-secundario" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="mod-boton mod-boton-primary">Enviar advertencia</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============ MODAL SUSPENDER ============ -->
    <div class="modal fade" id="modalSuspender" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="php/mod_acciones.php" class="modal-content">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="accion" value="suspension">
                <input type="hidden" name="tipo" value="<?= $tipo ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="usuario_id" value="<?= $usuarioId ?>">
                <div class="modal-header" style="background:#fff3e0;">
                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation"></i> Suspender usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Usuario: <strong>@<?= htmlspecialchars($usuario['nombre'] ?? '') ?></strong><br>
                        Correo: <?= htmlspecialchars($usuario['correo'] ?? '—') ?>
                    </p>
                    <label class="form-label fw-semibold">Motivo</label>
                    <select name="motivo" class="form-select mb-3">
                        <?php foreach (mod_motivos() as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="form-label fw-semibold">Mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="4" placeholder="Mensaje para el usuario..." required></textarea>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="enviar_correo" value="1" id="correoSusp">
                        <label class="form-check-label" for="correoSusp">Enviar notificación por correo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="mod-boton mod-boton-secundario" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="mod-boton mod-boton-aviso">Suspender y enviar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============ MODAL ELIMINAR USUARIO ============ -->
    <div class="modal fade" id="modalEliminarUsuario" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="post" action="php/mod_acciones.php" class="modal-content">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <input type="hidden" name="accion" value="eliminar_usuario">
                <input type="hidden" name="tipo" value="<?= $tipo ?>">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="usuario_id" value="<?= $usuarioId ?>">
                <div class="modal-header" style="background:#fdecea;">
                    <h5 class="modal-title text-danger"><i class="fa-solid fa-user-slash"></i> Eliminar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Usuario: <strong>@<?= htmlspecialchars($usuario['nombre'] ?? '') ?></strong><br>
                        Correo: <?= htmlspecialchars($usuario['correo'] ?? '—') ?>
                    </p>
                    <div class="alert alert-danger">
                        ⚠️ Esta acción marcará la cuenta como eliminada. Su contenido dejará de verse. ¿Continuar?
                    </div>
                    <label class="form-label fw-semibold">Motivo</label>
                    <select name="motivo" class="form-select mb-3">
                        <?php foreach (mod_motivos() as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="form-label fw-semibold">Mensaje</label>
                    <textarea name="mensaje" class="form-control" rows="4" placeholder="Mensaje para el usuario..." required></textarea>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="enviar_correo" value="1" id="correoElim">
                        <label class="form-check-label" for="correoElim">Enviar notificación por correo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="mod-boton mod-boton-secundario" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="mod-boton mod-boton-peligro">Eliminar usuario</button>
                </div>
            </form>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>