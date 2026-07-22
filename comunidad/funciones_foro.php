<?php

function generarSlug($texto) {
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $texto = str_replace(
        ['á','é','í','ó','ú','ü','ñ','Á','É','Í','Ó','Ú','Ü','Ñ'],
        ['a','e','i','o','u','u','n','a','e','i','o','u','u','n'],
        $texto
    );
    $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
    $texto = trim($texto, '-');
    return $texto;
}

function tiempoRelativo($fecha) {
    if (!$fecha) return '';
    $now = new DateTime();
    $then = new DateTime($fecha);
    $diff = $now->getTimestamp() - $then->getTimestamp();

    if ($diff < 60) return 'justo ahora';
    if ($diff < 3600) return 'hace ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'hace ' . floor($diff / 3600) . ' h';
    if ($diff < 2592000) return 'hace ' . floor($diff / 86400) . ' días';
    return $then->format('d/m/Y');
}

function fotoPerfil($foto) {
    $ruta = __DIR__ . '/../uploads/perfiles/' . $foto;
    if ($foto && file_exists($ruta)) {
        return 'uploads/perfiles/' . $foto;
    }
    return 'images/default-avatar.png';
}

function usuarioReacciono($conn, $usuario_id, $tipo, $target_id) {
    $stmt = $conn->prepare("SELECT id FROM foro_reacciones WHERE usuario_id=? AND tipo=? AND target_id=?");
    $stmt->bind_param("isi", $usuario_id, $tipo, $target_id);
    $stmt->execute();
    $existe = $stmt->get_result()->fetch_row();
    $stmt->close();
    return $existe ? true : false;
}

function contarReacciones($conn, $tipo, $target_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM foro_reacciones WHERE tipo=? AND target_id=?");
    $stmt->bind_param("si", $tipo, $target_id);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
    return $count;
}

function esAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}
