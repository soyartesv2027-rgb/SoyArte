<?php
// ============================================
// CONFIGURACIÓN DE CORREO (SMTP)
// ============================================
// Ejemplo con Gmail (requiere "contraseña de aplicación"):
//   SMTP_HOST = 'smtp.gmail.com'
//   SMTP_PORT = 587
//   SMTP_USER = 'tu.correo@gmail.com'
//   SMTP_PASS = 'contraseña de aplicación de 16 caracteres'
//
// Para InfinityFree: el hosting no permite SMTP saliente;
// usa un SMTP externo (Gmail, Outlook, etc.).
// ============================================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu.correo@gmail.com');      // ← CAMBIAR
define('SMTP_PASS', 'tu_contraseña_app');        // ← CAMBIAR
define('SMTP_FROM', 'tu.correo@gmail.com');      // ← CAMBIAR
define('SMTP_FROM_NAME', 'SoyArte');