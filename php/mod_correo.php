<?php
// ============================================
// ENVÍO DE CORREOS ADMINISTRATIVOS (PHPMailer + SMTP)
// ============================================

require_once __DIR__ . '/config_correo.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP as PHPMailerSMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

function mod_enviar_correo($destinatario, $asunto, $mensajeHtml) {
    // Configuración no completada
    if (SMTP_USER === 'tu.correo@gmail.com' || SMTP_PASS === 'tu_contraseña_app') {
        return [false, 'SMTP no configurado. Revisa php/config_correo.php'];
    }

    require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/../vendor/PHPMailer/SMTP.php';
    require_once __DIR__ . '/../vendor/PHPMailer/Exception.php';

    try {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensajeHtml;

        $mail->send();
        return [true, null];
    } catch (PHPMailerException $e) {
        return [false, $mail->ErrorInfo];
    }
}