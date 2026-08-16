<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$tipos = mod_tipos();

$porPagina = 20;
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;

$q = trim($_GET['q'] ?? '');
$accion = trim($_GET['accion'] ?? '');
$desde = trim($_GET['desde'] ?? '');
$hasta = trim($_GET['hasta'] ?? '');

$baseUrl = 'moderacion_historial.php?';
$queryParams = ['q' => $q, 'accion' => $accion, 'desde' => $desde, 'hasta' => $hasta];
function mod_url3($baseUrl, $params, $overrides = []) {
    $p = array_merge($params, $overrides);
    foreach ($p as $k => $v) {
        if ($v === '' || $v === null) unset($p[$k]);
    }
    return $baseUrl . http_build_query($p);
}

$where = [];
$types = '';
$values = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $where[] = '(ua.nombre LIKE ? OR uu.nombre LIKE ? OR h.accion LIKE ? OR h.motivo LIKE ?)';
    $types .= 'ssss';
    array_push($values, $like, $like, $like, $like);
}
if ($accion !== '') {
    $where[] = 'h.accion = ?';
    $types .= 's';
    $values[] = $accion;
}
if ($desde !== '') { $where[] = 'h.fecha >= ?'; $types .= 's'; $values[] = $desde . ' 00:00:00'; }
if ($hasta !== '') { $where[] = 'h.fecha <= ?'; $types .= 's'; $values[] = $hasta . ' 23:59:59'; }

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $conn->prepare("SELECT COUNT(*) AS total
    FROM moderacion_historial h
    LEFT JOIN usuarios ua ON ua.id = h.admin_id
    LEFT JOIN usuarios uu ON uu.id = h.usuario_id
    $whereSql");
if ($types !== '') $stmt->bind_param($types, ...$values);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['total'];
list($offset, $totalPaginas, $pagina) = mod_paginacion($total, $porPagina, $pagina);

$stmt = $conn->prepare("SELECT h.*, ua.nombre AS admin_nombre, uu.nombre AS usuario_nombre
    FROM moderacion_historial h
    LEFT JOIN usuarios ua ON ua.id = h.admin_id
    LEFT JOIN usuarios uu ON uu.id = h.usuario_id
    $whereSql
    ORDER BY h.fecha DESC
    LIMIT ? OFFSET ?");
$typesFull = $types . 'ii';
$valuesFull = array_merge($values, [$porPagina, $offset]);
$stmt->bind_param($typesFull, ...$valuesFull);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$accionesPosibles = $conn->query("SELECT DISTINCT accion FROM moderacion_historial ORDER BY accion")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de moderación - SoyArte</title>
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
                <i class="fa-solid fa-clock-rotate-left"></i> Historial de moderación
            </h1>
            <div class="mod-subtitulo" style="margin-bottom:0;">Todas las acciones administrativas</div>
        </div>
        <a href="moderacion.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="get" class="mod-filtros">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="🔎 Buscar admin, usuario, acción, motivo..." class="mod-buscador">
        <select name="accion">
            <option value="">Acción: Todas</option>
            <?php foreach ($accionesPosibles as $a): ?>
                <option value="<?= htmlspecialchars($a['accion']) ?>" <?= $accion === $a['accion'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $a['accion']))) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>" title="Desde">
        <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>" title="Hasta">
        <button type="submit" class="mod-boton mod-boton-primary"><i class="fa-solid fa-filter"></i> Filtrar</button>
        <a href="moderacion_historial.php" class="mod-boton mod-boton-secundario">Limpiar</a>
    </form>

    <div class="mod-bloque">
        <div class="mod-tabla-wrap">
            <table class="mod-tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Administrador</th>
                        <th>Acción</th>
                        <th>Publicación</th>
                        <th>Usuario afectado</th>
                        <th>Motivo</th>
                        <th>Correo</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($filas): ?>
                    <?php foreach ($filas as $f): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($f['fecha'])) ?></td>
                            <td><?= htmlspecialchars($f['admin_nombre'] ?? '—') ?></td>
                            <td>
                                <?= mod_icono_accion($f['accion']) ?>
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $f['accion']))) ?>
                            </td>
                            <td>
                                <?php if (!empty($f['tipo_contenido']) && !empty($f['id_contenido'])): ?>
                                    <a href="moderacion_detalle.php?tipo=<?= $f['tipo_contenido'] ?>&id=<?= (int)$f['id_contenido'] ?>">
                                        <?= $tipos[$f['tipo_contenido']]['etiqueta'] ?> #<?= (int)$f['id_contenido'] ?>
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($f['usuario_nombre'])): ?>
                                    <a href="moderacion_usuario.php?id=<?= (int)$f['usuario_id'] ?>">
                                        @<?= htmlspecialchars($f['usuario_nombre']) ?>
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($f['motivo'] ?? '—') ?></td>
                            <td><?= $f['correo_enviado'] ? '📧 Sí' : '—' ?></td>
                            <td>
                                <?php if (!empty($f['usuario_id'])): ?>
                                    <a href="moderacion_usuario.php?id=<?= (int)$f['usuario_id'] ?>" class="mod-boton mod-boton-secundario" style="padding:4px 10px; font-size:0.8rem;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay registros.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total > 0): ?>
            <div class="mod-info-registros"><?= $total ?> registro(s) — página <?= $pagina ?> de <?= $totalPaginas ?></div>
            <div class="mod-paginacion">
                <?php if ($pagina > 1): ?>
                    <a href="<?= mod_url3($baseUrl, $queryParams, ['p' => $pagina - 1]) ?>">← Anterior</a>
                <?php endif; ?>
                <?php
                $desdePag = max(1, $pagina - 2);
                $hastaPag = min($totalPaginas, $pagina + 2);
                for ($i = $desdePag; $i <= $hastaPag; $i++): ?>
                    <?php if ($i === $pagina): ?>
                        <span class="activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= mod_url3($baseUrl, $queryParams, ['p' => $i]) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= mod_url3($baseUrl, $queryParams, ['p' => $pagina + 1]) ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>