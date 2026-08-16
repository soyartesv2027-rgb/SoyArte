<?php
// ============================================
// FUNCIONES AUXILIARES DE MODERACIÓN
// ============================================

require_once __DIR__ . '/funciones-poesia.php';

// --- Configuración de los 4 tipos de contenido ---
function mod_tipos() {
    return [
        'pintura' => [
            'tabla'    => 'pinturas',
            'id_col'   => 'ID',
            'titulo'   => 'nombre_pintura',
            'autor'    => 'autor',
            'imagen'   => 'imagen',
            'fecha'    => 'fecha',
            'usuario'  => 'id_usuario',
            'etiqueta' => 'Pintura',
            'plural'   => 'Pinturas',
            'icono'    => 'fa-paintbrush',
            'url'      => 'ver_pintura.php',
        ],
        'musica' => [
            'tabla'    => 'musica',
            'id_col'   => 'musica_id',
            'titulo'   => 'nombre_cancion',
            'autor'    => 'nombre_cantante',
            'imagen'   => 'portada',
            'fecha'    => 'fecha',
            'usuario'  => 'usuario_id',
            'etiqueta' => 'Música',
            'plural'   => 'Música',
            'icono'    => 'fa-music',
            'url'      => 'ver_musica.php',
        ],
        'poesia' => [
            'tabla'    => 'obras',
            'id_col'   => 'id',
            'titulo'   => 'titulo',
            'autor'    => 'autor',
            'imagen'   => 'imagen',
            'fecha'    => 'fecha_publicacion',
            'usuario'  => 'usuario_id',
            'etiqueta' => 'Poesía',
            'plural'   => 'Poesía',
            'icono'    => 'fa-feather-pointed',
            'url'      => 'detalle.php',
        ],
        'manualidad' => [
            'tabla'    => 'manualidades',
            'id_col'   => 'id',
            'titulo'   => 'nombre',
            'autor'    => 'autor',
            'imagen'   => 'imagen',
            'fecha'    => 'fecha',
            'usuario'  => 'usuario_id',
            'etiqueta' => 'Manualidad',
            'plural'   => 'Manualidades',
            'icono'    => 'fa-cube',
            'url'      => 'ver_manualidad.php',
        ],
    ];
}

function mod_tipo_valido($tipo) {
    $tipos = mod_tipos();
    return isset($tipos[$tipo]) ? $tipo : null;
}

// --- Motivos permitidos de denuncia/advertencia ---
function mod_motivos() {
    return [
        'Contenido inapropiado',
        'Violencia',
        'Acoso',
        'Odio o discriminación',
        'Amenazas',
        'Contenido peligroso',
        'Spam',
        'Otro',
    ];
}

// --- Obtener una publicación con datos del autor ---
function mod_obtener_contenido($conn, $tipo, $id) {
    $cfg = mod_tipos()[$tipo];
    $idCol = $cfg['id_col'];
    $usuarioCol = $cfg['usuario'];
    $tabla = $cfg['tabla'];
    $sql = "SELECT c.*, u.nombre AS nombre_usuario, u.correo AS correo_usuario
            FROM `$tabla` c
            LEFT JOIN usuarios u ON u.id = c.`$usuarioCol`
            WHERE c.`$idCol` = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// --- URL de la imagen (BLOB en poesía, ruta en el resto) ---
function mod_imagen_src($tipo, $fila) {
    $cfg = mod_tipos()[$tipo];
    $imagen = $fila[$cfg['imagen']] ?? null;
    if (empty($imagen)) {
        return null;
    }
    if ($tipo === 'poesia') {
        return imagenSrc($imagen);
    }
    if ($tipo === 'musica') {
        if (strpos($imagen, 'uploads/') === 0) {
            return htmlspecialchars($imagen);
        }
        return 'uploads/musica/' . htmlspecialchars($imagen);
    }
    return htmlspecialchars($imagen);
}

// --- Título de la publicación ---
function mod_titulo($tipo, $fila) {
    $cfg = mod_tipos()[$tipo];
    return htmlspecialchars($fila[$cfg['titulo']] ?? 'Sin título');
}

// --- Fecha legible ---
function mod_fecha($tipo, $fila) {
    $cfg = mod_tipos()[$tipo];
    $fecha = $fila[$cfg['fecha']] ?? null;
    if (!$fecha) {
        return '—';
    }
    $ts = strtotime($fecha);
    return $ts ? date('d/m/Y H:i', $ts) : htmlspecialchars($fecha);
}

// --- URL pública de la publicación ---
function mod_url_publica($tipo, $id) {
    $cfg = mod_tipos()[$tipo];
    return $cfg['url'] . '?id=' . (int)$id;
}

// --- Denuncias de una publicación ---
function mod_denuncias_contenido($conn, $tipo, $id) {
    $sql = "SELECT d.*, u.nombre AS denunciante
            FROM denuncias d
            LEFT JOIN usuarios u ON u.id = d.id_denunciante
            WHERE d.tipo_contenido = ? AND d.id_contenido = ?
            ORDER BY d.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $tipo, $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function mod_total_denuncias($conn, $tipo, $id) {
    $sql = "SELECT COUNT(*) AS total FROM denuncias WHERE tipo_contenido = ? AND id_contenido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $tipo, $id);
    $stmt->execute();
    return (int)$stmt->get_result()->fetch_assoc()['total'];
}

function mod_denuncias_por_motivo($conn, $tipo, $id) {
    $sql = "SELECT motivo, COUNT(*) AS cantidad
            FROM denuncias
            WHERE tipo_contenido = ? AND id_contenido = ?
            GROUP BY motivo
            ORDER BY cantidad DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $tipo, $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// --- Conteos de un usuario ---
function mod_conteos_usuario($conn, $usuarioId) {
    $resultado = ['publicaciones' => 0, 'denuncias' => 0, 'advertencias' => 0, 'sanciones' => 0, 'por_tipo' => []];

    $porTipo = [
        'pintura'     => 'pinturas',
        'musica'      => 'musica',
        'poesia'      => 'obras',
        'manualidad'  => 'manualidades',
    ];

    foreach ($porTipo as $tipo => $tabla) {
        $usuarioCol = mod_tipos()[$tipo]['usuario'];
        $sql = "SELECT COUNT(*) AS total FROM `$tabla` WHERE `$usuarioCol` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();
        $cant = (int)$stmt->get_result()->fetch_assoc()['total'];
        $resultado['por_tipo'][$tipo] = $cant;
        $resultado['publicaciones'] += $cant;
    }

    // Denuncias recibidas: las denuncias apuntan a contenido del usuario
    $sql = "SELECT COUNT(*) AS total FROM denuncias d
            WHERE (d.tipo_contenido, d.id_contenido) IN (
                SELECT 'pintura', ID FROM pinturas WHERE id_usuario = ?
                UNION ALL SELECT 'musica', musica_id FROM musica WHERE usuario_id = ?
                UNION ALL SELECT 'poesia', id FROM obras WHERE usuario_id = ?
                UNION ALL SELECT 'manualidad', id FROM manualidades WHERE usuario_id = ?
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $usuarioId, $usuarioId, $usuarioId, $usuarioId);
    $stmt->execute();
    $resultado['denuncias'] = (int)$stmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT COUNT(*) AS total FROM advertencias WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $resultado['advertencias'] = (int)$stmt->get_result()->fetch_assoc()['total'];

    $sql = "SELECT COUNT(*) AS total FROM sanciones WHERE usuario_id = ? AND tipo_sancion = 'suspension' AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $resultado['sanciones'] = (int)$stmt->get_result()->fetch_assoc()['total'];

    return $resultado;
}

// --- Registrar acción en el historial ---
function mod_historial_agregar($conn, $adminId, $accion, $motivo = null, $usuarioId = null, $tipoContenido = null, $idContenido = null, $mensaje = null, $correoEnviado = 0) {
    // Tolerar usuarios huérfanos (contenido cuyo autor ya no existe en la BD)
    if ($usuarioId !== null && $usuarioId > 0) {
        $chk = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
        $chk->bind_param('i', $usuarioId);
        $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            $usuarioId = null;
        }
    }
    $sql = "INSERT INTO moderacion_historial
            (admin_id, usuario_id, tipo_contenido, id_contenido, accion, motivo, mensaje, correo_enviado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iisisssi', $adminId, $usuarioId, $tipoContenido, $idContenido, $accion, $motivo, $mensaje, $correoEnviado);
    return $stmt->execute();
}

// --- Insignias de estado ---
function mod_badge_publicacion($estado) {
    switch ($estado) {
        case 'oculta':    return '<span class="badge bg-warning text-dark">🟡 Oculta</span>';
        case 'eliminada': return '<span class="badge bg-danger">🔴 Eliminada</span>';
        default:          return '<span class="badge bg-success">🟢 Publicada</span>';
    }
}

function mod_badge_usuario($estado) {
    switch ($estado) {
        case 'advertido':  return '<span class="badge bg-warning text-dark">🟡 Advertido</span>';
        case 'suspendido': return '<span class="badge bg-danger" style="background:#e67e22!important;">🟠 Suspendido</span>';
        case 'eliminado':  return '<span class="badge bg-dark">⚫ Eliminado</span>';
        default:           return '<span class="badge bg-success">🟢 Activo</span>';
    }
}

function mod_badge_denuncia($estado) {
    switch ($estado) {
        case 'en_revision': return '<span class="badge bg-warning text-dark">🟡 En revisión</span>';
        case 'resuelta':    return '<span class="badge bg-success">🟢 Resuelta</span>';
        default:            return '<span class="badge bg-danger">🔴 Pendiente</span>';
    }
}

// --- Icono del estado de la denuncia ---
function mod_icono_accion($accion) {
    switch ($accion) {
        case 'mantener':          return '✅';
        case 'ocultar':           return '👁️';
        case 'eliminar_publicacion': return '🗑️';
        case 'advertencia':       return '💬';
        case 'suspension':        return '⚠️';
        case 'eliminar_usuario':  return '🗑️';
        case 'denuncia_resuelta': return '✅';
        default:                  return '📌';
    }
}

// --- Calcular paginación ---
function mod_paginacion($total, $porPagina, $pagina) {
    $totalPaginas = max(1, (int)ceil($total / $porPagina));
    $pagina = max(1, min((int)$pagina, $totalPaginas));
    $offset = ($pagina - 1) * $porPagina;
    return [$offset, $totalPaginas, $pagina];
}
