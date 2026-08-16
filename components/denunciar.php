<?php
// ============================================
// COMPONENTE: BOTÓN + FORMULARIO DE DENUNCIA
// Uso:
//   $mod_tipo = 'pintura'; $mod_id = $id;
//   include("components/denunciar.php");
// ============================================

$mod_tipo = $mod_tipo ?? null;
$mod_id = (int)($mod_id ?? 0);

require_once __DIR__ . '/../php/mod_helpers.php';
?>
<link rel="stylesheet" href="styles/denunciar.css">

<?php if ($mod_tipo && $mod_id > 0 && isset($_SESSION['usuario_id'])): ?>
<details class="denunciar-detalles">
    <summary>
        <i class="fa-solid fa-flag"></i> Denunciar esta publicación
    </summary>
    <form method="post" action="php/denunciar.php" class="denunciar-form">
        <input type="hidden" name="tipo" value="<?= htmlspecialchars($mod_tipo) ?>">
        <input type="hidden" name="id" value="<?= $mod_id ?>">
        <label>Motivo</label>
        <select name="motivo" required>
            <?php foreach (mod_motivos() as $m): ?>
                <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Detalle (opcional)</label>
        <textarea name="descripcion" rows="3" placeholder="Explica brevemente por qué denuncias este contenido..."></textarea>
        <button type="submit">
            <i class="fa-solid fa-paper-plane"></i> Enviar denuncia
        </button>
    </form>
</details>
<?php endif; ?>