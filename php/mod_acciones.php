<?php
// ============================================
// ACCIONES ADMINISTRATIVAS DE MODERACIÓN
// Toda acción verifica en el servidor:
//   ¿Autenticado? → ¿Es administrador? → ¿CSRF válido? → ¿Datos válidos?
// ============================================

$ruta_login = '../login.html';
require_once __DIR__ . '/admin_check.php';
require_once __DIR__ . '/mod_helpers.php';
require_once __DIR__ . '/mod_correo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../moderacion.php');
    exit;
}

csrf_check();

$adminId = (int)$_SESSION['usuario_id'];
$accion  = $_POST['accion'] ?? '';
$tipo    = isset($_POST['tipo']) ? mod_tipo_valido($_POST['tipo']) : null;
$id      = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$usuarioId = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : 0;
$motivo  = trim($_POST['motivo'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');
$enviarCorreo = isset($_POST['enviar_correo']) && $_POST['enviar_correo'] == 1;

$motivoValido = ($motivo === '' || in_array($motivo, mod_motivos()));

$accionesPublicacion = ['mantener', 'ocultar', 'eliminar_publicacion'];
$accionesUsuario = ['advertencia', 'suspension', 'eliminar_usuario'];

if (!$motivoValido) {
    die('Motivo no válido.');
}

$redirigir = 'moderacion_detalle.php';

// ---------- Acciones sobre PUBLICACIONES ----------
if (in_array($accion, $accionesPublicacion)) {

    if (!$tipo || $id <= 0) {
        die('Parámetros no válidos.');
    }

    $cfg = mod_tipos()[$tipo];
    $tabla = $cfg['tabla'];
    $idCol = $cfg['id_col'];
    $usuarioCol = $cfg['usuario'];

    // Verificar que la publicación existe y obtener su autor
    $stmt = $conn->prepare("SELECT `$usuarioCol` AS usuario_id, `$idCol` FROM `$tabla` WHERE `$idCol` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    if (!$fila) {
        die('Publicación no encontrada.');
    }
    $autorPublicacion = (int)$fila['usuario_id'];

    if ($accion === 'mantener') {
        $nuevoEstado = 'publicada';
    } elseif ($accion === 'ocultar') {
        $nuevoEstado = 'oculta';
    } else {
        $nuevoEstado = 'eliminada';
    }

    $stmt = $conn->prepare("UPDATE `$tabla` SET estado = ? WHERE `$idCol` = ?");
    $stmt->bind_param('si', $nuevoEstado, $id);
    if (!$stmt->execute()) {
        die('Error al actualizar la publicación.');
    }

    // Resolver las denuncias de esta publicación
    $stmt = $conn->prepare("UPDATE denuncias SET estado = 'resuelta' WHERE tipo_contenido = ? AND id_contenido = ? AND estado IN ('pendiente', 'en_revision')");
    $stmt->bind_param('si', $tipo, $id);
    $stmt->execute();

    mod_historial_agregar($conn, $adminId, $accion, $motivo !== '' ? $motivo : null, $autorPublicacion, $tipo, $id, null, 0);

    $_SESSION['mod_mensaje'] = 'Publicación #' . $id . ' marcada como ' . $nuevoEstado . '.';
    header('Location: ../' . $redirigir . '?tipo=' . $tipo . '&id=' . $id);
    exit;
}

// ---------- Acciones sobre USUARIOS ----------
if (in_array($accion, $accionesUsuario)) {

    if ($usuarioId <= 0) {
        die('Parámetros no válidos.');
    }

    if ($motivo === '' || $mensaje === '') {
        $_SESSION['mod_error'] = 'Motivo y mensaje son obligatorios.';
        header('Location: ../' . ($tipo && $id > 0 ? $redirigir . '?tipo=' . $tipo . '&id=' . $id : 'moderacion_usuarios.php'));
        exit;
    }

    if ($usuarioId === $adminId) {
        die('No puedes aplicar esta acción a tu propia cuenta.');
    }

    // Verificar que el usuario existe
    $stmt = $conn->prepare("SELECT nombre, correo, estado FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
    if (!$usuario) {
        die('Usuario no encontrado.');
    }

    $correoEnviado = 0;
    $nombreAdmin = $_SESSION['nombre'] ?? 'Administrador';

    if ($accion === 'advertencia') {
        // Insertar advertencia
        $stmt = $conn->prepare("INSERT INTO advertencias (admin_id, usuario_id, motivo, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiss', $adminId, $usuarioId, $motivo, $mensaje);
        $stmt->execute();

        // Subir estado: solo si está activo
        if ($usuario['estado'] === 'activo') {
            $stmt = $conn->prepare("UPDATE usuarios SET estado = 'advertido' WHERE id = ?");
            $stmt->bind_param('i', $usuarioId);
            $stmt->execute();
        }

        if ($enviarCorreo && !empty($usuario['correo'])) {
            $asunto = 'Advertencia - SoyArte';
            $cuerpo = mod_correo_plantilla(
                '💬 Advertencia de moderación',
                'Hola ' . htmlspecialchars($usuario['nombre']) . ',',
                'Has recibido una advertencia en SoyArte por: <strong>' . htmlspecialchars($motivo) . '</strong>.',
                $mensaje
            );
            list($ok, $err) = mod_enviar_correo($usuario['correo'], $asunto, $cuerpo);
            $correoEnviado = $ok ? 1 : 0;
        }

        mod_historial_agregar($conn, $adminId, 'advertencia', $motivo, $usuarioId, $tipo, $id > 0 ? $id : null, $mensaje, $correoEnviado);

        $mensajeFlash = 'Advertencia enviada a @' . $usuario['nombre'] . '.';

    } elseif ($accion === 'suspension') {
        // Insertar sanción
        $stmt = $conn->prepare("INSERT INTO sanciones (admin_id, usuario_id, tipo_sancion, motivo, mensaje, correo_enviado) VALUES (?, ?, 'suspension', ?, ?, 0)");
        $stmt->bind_param('iiss', $adminId, $usuarioId, $motivo, $mensaje);
        $stmt->execute();
        $sancionId = $stmt->insert_id;

        $stmt = $conn->prepare("UPDATE usuarios SET estado = 'suspendido' WHERE id = ?");
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();

        if ($enviarCorreo && !empty($usuario['correo'])) {
            $asunto = 'Cuenta suspendida - SoyArte';
            $cuerpo = mod_correo_plantilla(
                '⚠️ Tu cuenta ha sido suspendida',
                'Hola ' . htmlspecialchars($usuario['nombre']) . ',',
                'Tu cuenta de SoyArte ha sido suspendida por: <strong>' . htmlspecialchars($motivo) . '</strong>.',
                $mensaje
            );
            list($ok, $err) = mod_enviar_correo($usuario['correo'], $asunto, $cuerpo);
            $correoEnviado = $ok ? 1 : 0;
            if ($correoEnviado) {
                $conn->query("UPDATE sanciones SET correo_enviado = 1 WHERE id = $sancionId");
            }
        }

        mod_historial_agregar($conn, $adminId, 'suspension', $motivo, $usuarioId, $tipo, $id > 0 ? $id : null, $mensaje, $correoEnviado);

        $mensajeFlash = 'Usuario @' . $usuario['nombre'] . ' suspendido.' . ($correoEnviado ? ' Correo enviado.' : '');

    } else { // eliminar_usuario
        $stmt = $conn->prepare("INSERT INTO sanciones (admin_id, usuario_id, tipo_sancion, motivo, mensaje, correo_enviado) VALUES (?, ?, 'eliminacion', ?, ?, 0)");
        $stmt->bind_param('iiss', $adminId, $usuarioId, $motivo, $mensaje);
        $stmt->execute();
        $sancionId = $stmt->insert_id;

        $stmt = $conn->prepare("UPDATE usuarios SET estado = 'eliminado' WHERE id = ?");
        $stmt->bind_param('i', $usuarioId);
        $stmt->execute();

        if ($enviarCorreo && !empty($usuario['correo'])) {
            $asunto = 'Cuenta eliminada - SoyArte';
            $cuerpo = mod_correo_plantilla(
                '🗑️ Tu cuenta ha sido eliminada',
                'Hola ' . htmlspecialchars($usuario['nombre']) . ',',
                'Tu cuenta de SoyArte ha sido eliminada por: <strong>' . htmlspecialchars($motivo) . '</strong>.',
                $mensaje
            );
            list($ok, $err) = mod_enviar_correo($usuario['correo'], $asunto, $cuerpo);
            $correoEnviado = $ok ? 1 : 0;
            if ($correoEnviado) {
                $conn->query("UPDATE sanciones SET correo_enviado = 1 WHERE id = $sancionId");
            }
        }

        mod_historial_agregar($conn, $adminId, 'eliminar_usuario', $motivo, $usuarioId, $tipo, $id > 0 ? $id : null, $mensaje, $correoEnviado);

        $mensajeFlash = 'Usuario @' . $usuario['nombre'] . ' eliminado.' . ($correoEnviado ? ' Correo enviado.' : '');
    }

    $_SESSION['mod_mensaje'] = $mensajeFlash;
    header('Location: ../' . ($tipo && $id > 0 ? $redirigir . '?tipo=' . $tipo . '&id=' . $id : 'moderacion_usuarios.php'));
    exit;
}

die('Acción no válida.');

// ---------- Plantilla de correo ----------
function mod_correo_plantilla($titulo, $saludo, $lineaPrincipal, $mensajeAdmin) {
    $mensajeTexto = nl2br(htmlspecialchars($mensajeAdmin));
    return "
    <div style='font-family:Arial,sans-serif; max-width:600px; margin:0 auto; border:1px solid #d8e8f6; border-radius:12px; overflow:hidden;'>
        <div style='background:#2c4e7e; color:#fff; padding:18px 24px;'>
            <h2 style='margin:0; font-size:18px;'>$titulo</h2>
            <div style='opacity:.85; font-size:13px;'>SoyArte · Moderación</div>
        </div>
        <div style='padding:24px; color:#111827; font-size:14px; line-height:1.6;'>
            <p>$saludo</p>
            <p>$lineaPrincipal</p>
            <div style='background:#f0f6ff; border-left:4px solid #2c4e7e; padding:12px 16px; border-radius:8px; margin:16px 0;'>
                $mensajeTexto
            </div>
            <p style='color:#6b7280; font-size:12px;'>Este correo fue enviado por el equipo de moderación de SoyArte.</p>
        </div>
    </div>";
}