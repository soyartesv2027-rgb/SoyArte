<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$tipos = mod_tipos();
$tipo = isset($_GET['tipo']) ? mod_tipo_valido($_GET['tipo']) : null;
$esCombinado = ($tipo === null);

$porPagina = 15;
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;

$q = trim($_GET['q'] ?? '');
$motivo = trim($_GET['motivo'] ?? '');
$estado = trim($_GET['estado'] ?? '');
$categoria = isset($_GET['categoria']) ? mod_tipo_valido($_GET['categoria']) : null;
$desde = trim($_GET['desde'] ?? '');
$hasta = trim($_GET['hasta'] ?? '');

$baseUrl = 'moderacion_denuncias.php' . ($esCombinado ? '' : '?tipo=' . $tipo . '&') ;
$queryParams = [
    'q' => $q, 'motivo' => $motivo, 'estado' => $estado,
    'desde' => $desde, 'hasta' => $hasta,
];
if (!$esCombinado) {
    $queryParams = ['tipo' => $tipo] + $queryParams;
}
function mod_url($baseUrl, $params, $overrides = []) {
    $p = array_merge($params, $overrides);
    foreach ($p as $k => $v) {
        if ($v === '' || $v === null) unset($p[$k]);
    }
    $qs = http_build_query($p);
    return $baseUrl . ($qs !== '' ? (strpos($baseUrl, '?') !== false ? '' : '?') . $qs : '');
}

// ================= MODOS =================
if ($esCombinado) {
    // ---------- TODAS LAS DENUNCIAS (por denuncia) ----------
    $where = [];
    $types = '';
    $values = [];

    if ($categoria) { $where[] = 'd.tipo_contenido = ?'; $types .= 's'; $values[] = $categoria; }
    if ($motivo !== '' && in_array($motivo, mod_motivos())) { $where[] = 'd.motivo = ?'; $types .= 's'; $values[] = $motivo; }
    if ($estado !== '' && in_array($estado, ['pendiente', 'en_revision', 'resuelta'])) { $where[] = 'd.estado = ?'; $types .= 's'; $values[] = $estado; }
    if ($desde !== '') { $where[] = 'd.fecha >= ?'; $types .= 's'; $values[] = $desde . ' 00:00:00'; }
    if ($hasta !== '') { $where[] = 'd.fecha <= ?'; $types .= 's'; $values[] = $hasta . ' 23:59:59'; }
    if ($q !== '') { $where[] = '(COALESCE(p.nombre_pintura, m.nombre_cancion, o.titulo, ma.nombre) LIKE ? OR COALESCE(p.autor, m.nombre_cantante, o.autor, ma.autor) LIKE ?)'; $like = '%' . $q . '%'; $types .= 'ss'; $values[] = $like; $values[] = $like; }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sqlBase = "FROM denuncias d
        LEFT JOIN usuarios u ON u.id = d.id_denunciante
        LEFT JOIN pinturas p ON d.tipo_contenido = 'pintura' AND p.ID = d.id_contenido
        LEFT JOIN musica m ON d.tipo_contenido = 'musica' AND m.musica_id = d.id_contenido
        LEFT JOIN obras o ON d.tipo_contenido = 'poesia' AND o.id = d.id_contenido
        LEFT JOIN manualidades ma ON d.tipo_contenido = 'manualidad' AND ma.id = d.id_contenido
        $whereSql";

    $stmt = $conn->prepare("SELECT COUNT(*) AS total $sqlBase");
    if ($types !== '') $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $total = (int)$stmt->get_result()->fetch_assoc()['total'];
    list($offset, $totalPaginas, $pagina) = mod_paginacion($total, $porPagina, $pagina);

    $stmt = $conn->prepare("SELECT d.*, u.nombre AS denunciante,
        COALESCE(p.nombre_pintura, m.nombre_cancion, o.titulo, ma.nombre) AS titulo,
        COALESCE(p.autor, m.nombre_cantante, o.autor, ma.autor) AS artista
        $sqlBase ORDER BY d.fecha DESC LIMIT ? OFFSET ?");
    $typesFull = $types . 'ii';
    $valuesFull = array_merge($values, [$porPagina, $offset]);
    $stmt->bind_param($typesFull, ...$valuesFull);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} else {
    // ---------- DENUNCIADAS DE UNA CATEGORÍA (agrupadas por publicación) ----------
    $cfg = $tipos[$tipo];
    $tabla = $cfg['tabla'];
    $idCol = $cfg['id_col'];
    $tituloCol = $cfg['titulo'];
    $autorCol = $cfg['autor'];
    $fechaCol = $cfg['fecha'];
    $usuarioCol = $cfg['usuario'];

    $where = [];
    $types = '';
    $values = [];

    $where[] = "EXISTS (SELECT 1 FROM denuncias d WHERE d.tipo_contenido = ? AND d.id_contenido = c.`$idCol`)";
    $types .= 's'; $values[] = $tipo;

    if ($motivo !== '' && in_array($motivo, mod_motivos())) {
        $where[] = "EXISTS (SELECT 1 FROM denuncias d2 WHERE d2.tipo_contenido = ? AND d2.id_contenido = c.`$idCol` AND d2.motivo = ?)";
        $types .= 'ss'; $values[] = $tipo; $values[] = $motivo;
    }
    if ($estado !== '' && in_array($estado, ['pendiente', 'en_revision', 'resuelta'])) {
        $where[] = "EXISTS (SELECT 1 FROM denuncias d3 WHERE d3.tipo_contenido = ? AND d3.id_contenido = c.`$idCol` AND d3.estado = ?)";
        $types .= 'ss'; $values[] = $tipo; $values[] = $estado;
    }
    if ($q !== '') {
        $where[] = "(c.`$tituloCol` LIKE ? OR u.nombre LIKE ? OR c.`$autorCol` LIKE ?)";
        $like = '%' . $q . '%';
        $types .= 'sss'; $values[] = $like; $values[] = $like; $values[] = $like;
    }
    if ($desde !== '') { $where[] = "c.`$fechaCol` >= ?"; $types .= 's'; $values[] = $desde . ' 00:00:00'; }
    if ($hasta !== '') { $where[] = "c.`$fechaCol` <= ?"; $types .= 's'; $values[] = $hasta . ' 23:59:59'; }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM `$tabla` c LEFT JOIN usuarios u ON u.id = c.`$usuarioCol` $whereSql");
    if ($types !== '') $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $total = (int)$stmt->get_result()->fetch_assoc()['total'];
    list($offset, $totalPaginas, $pagina) = mod_paginacion($total, $porPagina, $pagina);

    $stmt = $conn->prepare("SELECT c.*, u.nombre AS nombre_usuario,
        (SELECT COUNT(*) FROM denuncias d WHERE d.tipo_contenido = ? AND d.id_contenido = c.`$idCol`) AS num_denuncias
        FROM `$tabla` c LEFT JOIN usuarios u ON u.id = c.`$usuarioCol` $whereSql
        ORDER BY num_denuncias DESC, c.`$idCol` DESC LIMIT ? OFFSET ?");
    $typesFull = 's' . $types . 'ii';
    $valuesFull = array_merge([$tipo], $values, [$porPagina, $offset]);
    $stmt->bind_param($typesFull, ...$valuesFull);
    $stmt->execute();
    $filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Motivos por publicación (una sola consulta)
    $motivosPorId = [];
    if ($filas) {
        $ids = array_column($filas, $idCol);
        $in = implode(',', array_map('intval', $ids));
        $stmt = $conn->prepare("SELECT id_contenido, motivo, COUNT(*) AS cantidad FROM denuncias WHERE tipo_contenido = ? AND id_contenido IN ($in) GROUP BY id_contenido, motivo ORDER BY cantidad DESC");
        $stmt->bind_param('s', $tipo);
        $stmt->execute();
        foreach ($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $m) {
            $motivosPorId[(int)$m['id_contenido']][] = $m;
        }
    }
}

$tituloPagina = $esCombinado ? 'Todas las denuncias' : ($tipos[$tipo]['plural'] . ' denunciadas');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina) ?> - Moderación SoyArte</title>
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
                <i class="fa-solid fa-flag"></i> <?= htmlspecialchars($tituloPagina) ?>
            </h1>
            <div class="mod-subtitulo" style="margin-bottom:0;">
                <?= $esCombinado ? 'Denuncias de todas las categorías' : $tipos[$tipo]['etiqueta'] ?>
            </div>
        </div>
        <a href="<?= $esCombinado ? 'moderacion.php' : 'moderacion_categoria.php?tipo=' . $tipo ?>" class="mod-enlace-rapido">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <!-- FILTROS -->
    <form method="get" class="mod-filtros">
        <?php if ($esCombinado): ?>
            <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria ?? '') ?>">
        <?php else: ?>
            <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
        <?php endif; ?>

        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="🔎 Buscar..." class="mod-buscador">

        <?php if ($esCombinado): ?>
            <select name="categoria" onchange="this.form.submit()">
                <option value="">Categoría: Todas</option>
                <?php foreach ($tipos as $tk => $tc): ?>
                    <option value="<?= $tk ?>" <?= $categoria === $tk ? 'selected' : '' ?>><?= $tc['plural'] ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <select name="motivo">
            <option value="">Motivo: Todos</option>
            <?php foreach (mod_motivos() as $m): ?>
                <option value="<?= htmlspecialchars($m) ?>" <?= $motivo === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
            <?php endforeach; ?>
        </select>

        <select name="estado">
            <option value="">Estado: Todos</option>
            <option value="pendiente" <?= $estado === 'pendiente' ? 'selected' : '' ?>>🔴 Pendiente</option>
            <option value="en_revision" <?= $estado === 'en_revision' ? 'selected' : '' ?>>🟡 En revisión</option>
            <option value="resuelta" <?= $estado === 'resuelta' ? 'selected' : '' ?>>🟢 Resuelta</option>
        </select>

        <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>" title="Desde">
        <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>" title="Hasta">

        <button type="submit" class="mod-boton mod-boton-primary">
            <i class="fa-solid fa-filter"></i> Filtrar
        </button>
        <a href="<?= $esCombinado ? 'moderacion_denuncias.php' : 'moderacion_denuncias.php?tipo=' . $tipo ?>" class="mod-boton mod-boton-secundario">
            Limpiar
        </a>
    </form>

    <!-- TABLA -->
    <div class="mod-bloque">
        <div class="mod-tabla-wrap">
            <table class="mod-tabla">
                <thead>
                    <tr>
                        <?php if ($esCombinado): ?>
                            <th>ID</th>
                            <th>Contenido</th>
                            <th>Tipo</th>
                            <th>Artista</th>
                            <th>Motivo</th>
                            <th>Denunciante</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Ver</th>
                        <?php else: ?>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Artista</th>
                            <th>Motivo</th>
                            <th>Denuncias</th>
                            <th>Fecha</th>
                            <th>Ver</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if ($filas): ?>
                    <?php foreach ($filas as $f): ?>
                        <?php
                        if ($esCombinado) {
                            $tipoFila = $f['tipo_contenido'];
                            $idContenido = (int)$f['id_contenido'];
                            $detalleUrl = 'moderacion_detalle.php?tipo=' . $tipoFila . '&id=' . $idContenido;
                        } else {
                            $idContenido = (int)$f[$idCol];
                            $detalleUrl = 'moderacion_detalle.php?tipo=' . $tipo . '&id=' . $idContenido;
                        }
                        ?>
                        <tr>
                            <?php if ($esCombinado): ?>
                                <td>#<?= (int)$f['id'] ?></td>
                                <td><?= htmlspecialchars(mb_substr($f['titulo'] ?? '—', 0, 40)) ?></td>
                                <td><span class="mod-etiqueta"><i class="fa-solid <?= $tipos[$tipoFila]['icono'] ?>"></i> <?= $tipos[$tipoFila]['plural'] ?></span></td>
                                <td><?= htmlspecialchars($f['artista'] ?? $f['denunciante'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($f['motivo']) ?></td>
                                <td><?= htmlspecialchars($f['denunciante'] ?? '—') ?></td>
                                <td><?= date('d/m/Y', strtotime($f['fecha'])) ?></td>
                                <td><?= mod_badge_denuncia($f['estado']) ?></td>
                            <?php else: ?>
                                <td>#<?= $idContenido ?></td>
                                <td>
                                    <?php $srcImg = mod_imagen_src($tipo, $f); ?>
                                    <?php if ($srcImg): ?>
                                        <img src="<?= $srcImg ?>" class="mod-miniatura" alt="">
                                    <?php else: ?>
                                        <div class="mod-miniatura-sin-img"><i class="fa-solid <?= $cfg['icono'] ?>"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= mod_titulo($tipo, $f) ?></td>
                                <td><?= htmlspecialchars($f['nombre_usuario'] ?? $f[$autorCol] ?? '—') ?></td>
                                <td>
                                    <?php foreach (($motivosPorId[$idContenido] ?? []) as $m): ?>
                                        <div><?= htmlspecialchars($m['motivo']) ?> <span class="mod-etiqueta">×<?= (int)$m['cantidad'] ?></span></div>
                                    <?php endforeach; ?>
                                </td>
                                <td><span class="mod-etiqueta"><?= (int)$f['num_denuncias'] ?></span></td>
                                <td><?= mod_fecha($tipo, $f) ?></td>
                            <?php endif; ?>
                            <td>
                                <a href="<?= $detalleUrl ?>" class="mod-boton mod-boton-secundario" style="padding:5px 10px; font-size:0.8rem;">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
                            No se encontraron resultados.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total > 0): ?>
            <div class="mod-info-registros">
                <?= $total ?> registro(s) — página <?= $pagina ?> de <?= $totalPaginas ?>
            </div>
            <div class="mod-paginacion">
                <?php if ($pagina > 1): ?>
                    <a href="<?= mod_url($baseUrl, $queryParams, ['p' => $pagina - 1]) ?>">← Anterior</a>
                <?php endif; ?>

                <?php
                $desdePag = max(1, $pagina - 2);
                $hastaPag = min($totalPaginas, $pagina + 2);
                for ($i = $desdePag; $i <= $hastaPag; $i++): ?>
                    <?php if ($i === $pagina): ?>
                        <span class="activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= mod_url($baseUrl, $queryParams, ['p' => $i]) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= mod_url($baseUrl, $queryParams, ['p' => $pagina + 1]) ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>