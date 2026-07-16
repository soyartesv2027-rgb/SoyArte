<?php
session_start();
require_once __DIR__ . '/../../php/conexion.php';
require_once __DIR__ . '/../funciones_foro.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.html");
    exit();
}

$usuario_id   = (int)$_SESSION['usuario_id'];
$respuesta_id = (int)($_POST['respuesta_id'] ?? 0);
$slug         = $_POST['slug'] ?? '';

if ($respuesta_id <= 0) {
    header("Location: ../foro.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, tema_id, usuario_id FROM foro_respuestas WHERE id=?");
$stmt->bind_param("i", $respuesta_id);
$stmt->execute();
$respuesta = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$respuesta) {
    header("Location: ../foro.php");
    exit();
}

if ($usuario_id !== (int)$respuesta['usuario_id'] && !esAdmin()) {
    header("Location: ../tema.php?slug=" . urlencode($slug));
    exit();
}

$tema_id = $respuesta['tema_id'];

// Eliminar archivo adjunto si existe
$stmt_adj = $conn->prepare("SELECT archivo FROM foro_respuestas WHERE id=?");
$stmt_adj->bind_param("i", $respuesta_id);
$stmt_adj->execute();
$adjunto = $stmt_adj->get_result()->fetch_assoc();
$stmt_adj->close();

if ($adjunto && $adjunto['archivo']) {
    $file_path = __DIR__ . '/../../uploads/foro/' . $adjunto['archivo'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("DELETE FROM foro_respuestas WHERE id=?");
    $stmt->bind_param("i", $respuesta_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE foro_temas SET num_respuestas = GREATEST(num_respuestas - 1, 0) WHERE id=?");
    $stmt->bind_param("i", $tema_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE foro_temas SET ultimo_usuario_id = (SELECT usuario_id FROM foro_respuestas WHERE tema_id=? ORDER BY created_at DESC LIMIT 1), ultima_actividad = (SELECT created_at FROM foro_respuestas WHERE tema_id=? ORDER BY created_at DESC LIMIT 1) WHERE id=?");
    $stmt2 = $conn->prepare("
        UPDATE foro_temas t
        SET t.ultimo_usuario_id = (
            SELECT r.usuario_id FROM foro_respuestas r
            WHERE r.tema_id = ? ORDER BY r.created_at DESC LIMIT 1
        ),
        t.ultima_actividad = (
            SELECT r.created_at FROM foro_respuestas r
            WHERE r.tema_id = ? ORDER BY r.created_at DESC LIMIT 1
        )
        WHERE t.id = ?
    ");
    $stmt2->bind_param("iii", $tema_id, $tema_id, $tema_id);
    $stmt2->execute();
    $stmt2->close();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
}
$conn->close();

header("Location: ../tema.php?slug=" . urlencode($slug) . "&msg=resp_eliminada");
