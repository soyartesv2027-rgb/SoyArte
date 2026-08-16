<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$tipos = mod_tipos();
$tipo = isset($_GET['tipo']) ? mod_tipo_valido($_GET['tipo']) : null;

if (!$tipo) {
    header('Location: moderacion.php');
    exit;
}

$cfg = $tipos[$tipo];
$tabla = $cfg['tabla'];
$idCol = $cfg['id_col'];
$tituloCol = $cfg['titulo'];
$autorCol = $cfg['autor'];
$fechaCol = $cfg['fecha'];
$usuarioCol = $cfg['usuario'];

$porPagina = 15;
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;

$q = trim($_GET['q'] ?? '');
$estado = trim($_GET['estado'] ?? '');
$denuncias = trim($_GET['denuncias'] ?? '');
$orden = trim($_GET['orden'] ?? 'recientes');

$baseUrl = 'moderacion_todas.php?tipo=' . $tipo . '&';
$queryParams = ['tipo' => $tipo, 'q' => $q, 'estado' => $estado, 'denuncias' => $denuncias, 'orden' => $orden];
function mod_url2($baseUrl, $params, $overrides = []) {
    $p = array_merge($params, $overrides);
    foreach ($p as $k => $v) {
        if ($v === '' || $v === null) unset($p[$k]);
    }
    $qs = http_build_query($p);
    return $baseUrl . $qs;
}

// --- Filtros dinámicos ---
$where = [];
$types = '';
$values = [];

if ($estado !== '' && in_array($estado, ['publicada', 'oculta', 'eliminada'])) {
    $where[] = "c.estado = ?";
    $types .= 's'; $values[] = $estado;
}
if ($denuncias === 'con') {
    $where[] = "EXISTS (SELECT 1 FROM denuncias d WHERE d.tipo_contenido = ? AND d.id_contenido = c.`$idCol`)";
    $types .= 's'; $values[] = $tipo;
} elseif ($denuncias === 'sin') {
    $where[] = "NOT EXISTS (SELECT 1 FROM denuncias d WHERE d.tipo_contenido = ? AND d.id_contenido = c.`$idCol`)";
    $types .= 's'; $values[] = $tipo;
}
if ($q !== '') {
    $where[] = "(c.`$tituloCol` LIKE ? OR u.nombre LIKE ? OR c.`$autorCol` LIKE ?)";
    $like = '%' . $q . '%';
    $types .= 'sss'; $values[] = $like; $values[] = $like; $values[] = $like;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

switch ($orden) {
    case 'antiguos': $orderSql = "c.`$fechaCol` ASC"; break;
    case 'titulo':   $orderSql = "c.`$tituloCol` ASC"; break;
    case 'denuncias':$orderSql = "num_denuncias DESC, c.`$idCol` DESC"; break;
    default:         $orderSql = "c.`$fechaCol` DESC, c.`$idCol` DESC"; break;
}

// --- Total y datos ---
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM `$tabla` c LEFT JOIN usuarios u ON u.id = c.`$usuarioCol` $whereSql");
if ($types !== '') $stmt->bind_param($types, ...$values);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['total'];
list($offset, $totalPaginas, $pagina) = mod_paginacion($total, $porPagina, $pagina);

$stmt = $conn->prepare("SELECT c.*, u.nombre AS nombre_usuario,
    (SELECT COUNT(*) FROM denuncias d WHERE d.tipo_contenido = ? AND d.id_contenido = c.`$idCol`) AS num_denuncias
    FROM `$tabla` c LEFT JOIN usuarios u ON u.id = c.`$usuarioCol` $whereSql
    ORDER BY $orderSql LIMIT ? OFFSET ?");
$typesFull = 's' . $types . 'ii';
$valuesFull = array_merge([$tipo], $values, [$porPagina, $offset]);
$stmt->bind_param($typesFull, ...$valuesFull);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las <?= strtolower($cfg['plural']) ?> - Moderación SoyArte</title>
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
                <i class="fa-solid fa-table-list"></i> Todas las <?= strtolower($cfg['plural']) ?>
            </h1>
            <div class="mod-subtitulo" style="margin-bottom:0;">
                <?= $cfg['etiqueta'] ?> · <?= $total ?> publicación(es)
            </div>
        </div>
        <a href="moderacion_categoria.php?tipo=<?= $tipo ?>" class="mod-enlace-rapido">
            <i class="fa-solid fa-arrow-left"></i> Volver a <?= $cfg['plural'] ?>
        </a>
    </div>

    <!-- FILTROS -->
    <form method="get" class="mod-filtros">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($tipo) ?>">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="🔎 Buscar <?= strtolower($cfg['etiqueta']) ?>..." class="mod-buscador">

        <select name="estado">
            <option value="">Estado: Todos</option>
            <option value="publicada" <?= $estado === 'publicada' ? 'selected' : '' ?>>🟢 Publicada</option>
            <option value="oculta" <?= $estado === 'oculta' ? 'selected' : '' ?>>🟡 Oculta</option>
            <option value="eliminada" <?= $estado === 'eliminada' ? 'selected' : '' ?>>🔴 Eliminada</option>
        </select>

        <select name="denuncias">
            <option value="">Denuncias: Todas</option>
            <option value="con" <?= $denuncias === 'con' ? 'selected' : '' ?>>Con denuncias</option>
            <option value="sin" <?= $denuncias === 'sin' ? 'selected' : '' ?>>Sin denuncias</option>
        </select>

        <select name="orden">
            <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Ordenar: Más recientes</option>
            <option value="antiguos" <?= $orden === 'antiguos' ? 'selected' : '' ?>>Ordenar: Más antiguos</option>
            <option value="titulo" <?= $orden === 'titulo' ? 'selected' : '' ?>>Ordenar: Título A-Z</option>
            <option value="denuncias" <?= $orden === 'denuncias' ? 'selected' : '' ?>>Ordenar: Más denunciadas</option>
        </select>

        <button type="submit" class="mod-boton mod-boton-primary">
            <i class="fa-solid fa-filter"></i> Filtrar
        </button>
        <a href="moderacion_todas.php?tipo=<?= $tipo ?>" class="mod-boton mod-boton-secundario">Limpiar</a>
    </form>

    <!-- TABLA -->
    <div class="mod-bloque">
        <div class="mod-tabla-wrap">
            <table class="mod-tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>IMG</th>
                        <th>TÍTULO</th>
                        <th>ARTISTA</th>
                        <th>FECHA</th>
                        <th>ESTADO</th>
                        <th>DENUNCIAS</th>
                        <th>VER</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($filas): ?>
                    <?php foreach ($filas as $f): ?>
                        <?php $idContenido = (int)$f[$idCol]; ?>
                        <tr>
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
                            <td><?= mod_fecha($tipo, $f) ?></td>
                            <td><?= mod_badge_publicacion($f['estado'] ?? 'publicada') ?></td>
                            <td>
                                <?php $n = (int)($f['num_denuncias'] ?? 0); ?>
                                <span class="mod-etiqueta" style="<?= $n > 0 ? 'background:#fdecea; color:#c62828;' : '' ?>">
                                    🚨 <?= $n ?>
                                </span>
                            </td>
                            <td>
                                <a href="moderacion_detalle.php?tipo=<?= $tipo ?>&id=<?= $idContenido ?>" class="mod-boton mod-boton-secundario" style="padding:5px 10px; font-size:0.8rem;">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No se encontraron publicaciones.
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
                    <a href="<?= mod_url2($baseUrl, $queryParams, ['p' => $pagina - 1]) ?>">← Anterior</a>
                <?php endif; ?>

                <?php
                $desdePag = max(1, $pagina - 2);
                $hastaPag = min($totalPaginas, $pagina + 2);
                for ($i = $desdePag; $i <= $hastaPag; $i++): ?>
                    <?php if ($i === $pagina): ?>
                        <span class="activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= mod_url2($baseUrl, $queryParams, ['p' => $i]) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= mod_url2($baseUrl, $queryParams, ['p' => $pagina + 1]) ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>