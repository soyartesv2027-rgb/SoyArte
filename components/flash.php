<?php
// ============================================
// COMPONENTE: MENSAJES FLASH DE MODERACIÓN
// Uso: include("components/flash.php");
// ============================================
?>
<link rel="stylesheet" href="styles/denunciar.css">

<?php if (isset($_SESSION['mod_mensaje'])): ?>
    <div class="mod-flash-ok" style="max-width:none;"><?= htmlspecialchars($_SESSION['mod_mensaje']) ?></div>
    <?php unset($_SESSION['mod_mensaje']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['mod_error'])): ?>
    <div class="mod-flash-error" style="max-width:none;"><?= htmlspecialchars($_SESSION['mod_error']) ?></div>
    <?php unset($_SESSION['mod_error']); ?>
<?php endif; ?>