<?php
$ruta_login = 'login.html';
require_once 'php/admin_check.php';
require_once 'php/mod_helpers.php';

$porPagina = 15;
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$q = trim($_GET['q'] ?? '');
$estado = trim($_GET['estado'] ?? '');

$baseUrl = 'moderacion_usuarios.php?';
$queryParams = ['q' => $q, 'estado' => $estado];
function mod_url4($baseUrl, $params, $overrides = []) {
    $p = array_merge($params, $overrides);
    foreach ($p as $k => $v) {
        if ($v === '' || $v === null) unset($p[$k]);
    }
    return $baseUrl . http_build_query($p);
}

$where = [];
$types = '';
$values = [];

$where[] = "(u.estado IN ('advertido', 'suspendido', 'eliminado')
             OR EXISTS (SELECT 1 FROM sanciones s WHERE s.usuario_id = u.id)
             OR EXISTS (SELECT 1 FROM advertencias a WHERE a.usuario_id = u.id))";

if ($q !== '') {
    $where[] = '(u.nombre LIKE ? OR u.correo LIKE ?)';
    $like = '%' . $q . '%';
    $types .= 'ss';
    array_push($values, $like, $like);
}
if ($estado !== '' && in_array($estado, ['advertido', 'suspendido', 'eliminado', 'activo'])) {
    $where[] = 'u.estado = ?';
    $types .= 's';
    $values[] = $estado;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM usuarios u $whereSql");
if ($types !== '') $stmt->bind_param($types, ...$values);
$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['total'];
list($offset, $totalPaginas, $pagina) = mod_paginacion($total, $porPagina, $pagina);

$stmt = $conn->prepare("SELECT u.*,
        (SELECT COUNT(*) FROM advertencias a WHERE a.usuario_id = u.id) AS total_advertencias,
        (SELECT COUNT(*) FROM sanciones s WHERE s.usuario_id = u.id AND s.tipo_sancion = 'suspension' AND s.vigente = 1) AS total_suspensiones,
        (SELECT MAX(fecha) FROM sanciones s2 WHERE s2.usuario_id = u.id) AS ultima_sancion,
        (SELECT MAX(fecha) FROM advertencias a2 WHERE a2.usuario_id = u.id) AS ultima_advertencia
    FROM usuarios u $whereSql
    ORDER BY ultima_sancion DESC, u.id DESC
    LIMIT ? OFFSET ?");
$typesFull = $types . 'ii';
$valuesFull = array_merge($values, [$porPagina, $offset]);
$stmt->bind_param($typesFull, ...$valuesFull);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios sancionados - Moderación SoyArte</title>
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
                <i class="fa-solid fa-users-slash"></i> Usuarios sancionados
            </h1>
            <div class="mod-subtitulo" style="margin-bottom:0;">Cuentas con advertencias, suspensiones o eliminaciones</div>
        </div>
        <a href="moderacion.php" class="mod-enlace-rapido">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>

    <form method="get" class="mod-filtros">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="🔎 Buscar usuario o correo..." class="mod-buscador">
        <select name="estado">
            <option value="">Estado: Todos</option>
            <option value="advertido" <?= $estado === 'advertido' ? 'selected' : '' ?>>🟡 Advertido</option>
            <option value="suspendido" <?= $estado === 'suspendido' ? 'selected' : '' ?>>🟠 Suspendido</option>
            <option value="eliminado" <?= $estado === 'eliminado' ? 'selected' : '' ?>>⚫ Eliminado</option>
            <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>🟢 Activo</option>
        </select>
        <button type="submit" class="mod-boton mod-boton-primary"><i class="fa-solid fa-filter"></i> Filtrar</button>
        <a href="moderacion_usuarios.php" class="mod-boton mod-boton-secundario">Limpiar</a>
    </form>

    <div class="mod-bloque">
        <div class="mod-tabla-wrap">
            <table class="mod-tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Estado</th>
                        <th>Advertencias</th>
                        <th>Suspensiones</th>
                        <th>Última actividad</th>
                        <th>Ver</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($filas): ?>
                    <?php foreach ($filas as $f): ?>
                        <tr>
                            <td>#<?= (int)$f['id'] ?></td>
                            <td>@<?= htmlspecialchars($f['nombre'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($f['correo'] ?? '—') ?></td>
                            <td><?= mod_badge_usuario($f['estado'] ?? 'activo') ?></td>
                            <td><span class="mod-etiqueta"><?= (int)$f['total_advertencias'] ?></span></td>
                            <td><span class="mod-etiqueta"><?= (int)$f['total_suspensiones'] ?></span></td>
                            <td><?= $f['ultima_sancion'] ? date('d/m/Y', strtotime($f['ultima_sancion'])) : ($f['ultima_advertencia'] ? date('d/m/Y', strtotime($f['ultima_advertencia'])) : '—') ?></td>
                            <td>
                                <a href="moderacion_usuario.php?id=<?= (int)$f['id'] ?>" class="mod-boton mod-boton-secundario" style="padding:5px 10px; font-size:0.8rem;">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay usuarios sancionados.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total > 0): ?>
            <div class="mod-info-registros"><?= $total ?> registro(s) — página <?= $pagina ?> de <?= $totalPaginas ?></div>
            <div class="mod-paginacion">
                <?php if ($pagina > 1): ?>
                    <a href="<?= mod_url4($baseUrl, $queryParams, ['p' => $pagina - 1]) ?>">← Anterior</a>
                <?php endif; ?>
                <?php
                $desdePag = max(1, $pagina - 2);
                $hastaPag = min($totalPaginas, $pagina + 2);
                for ($i = $desdePag; $i <= $hastaPag; $i++): ?>
                    <?php if ($i === $pagina): ?>
                        <span class="activa"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= mod_url4($baseUrl, $queryParams, ['p' => $i]) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($pagina < $totalPaginas): ?>
                    <a href="<?= mod_url4($baseUrl, $queryParams, ['p' => $pagina + 1]) ?>">Siguiente →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>