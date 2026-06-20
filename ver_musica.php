<?php
session_start();
require_once 'php/conexion.php';

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM musica WHERE musica_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$musica = $stmt->get_result()->fetch_assoc();

if(!$musica){
    die("Canción no encontrada");
}

$esPropietario = false;

if(isset($_SESSION['usuario_id'])){

    if($_SESSION['usuario_id'] == $musica['usuario_id']){
        $esPropietario = true;
    }

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
<?php echo htmlspecialchars($musica['nombre_cancion']); ?>
</title>

<link rel="stylesheet" href="styles/ver_musica.css">
</head>
<body>

<div class="contenedor">

    <a href="musica.php" class="btn-volver">
        ← Volver a Música
    </a>

    <div class="detalle-musica">

        <img
            src="uploads/musica/<?php echo htmlspecialchars($musica['portada']); ?>"
            alt="Portada"
            class="portada"
        >

        <div class="info">

            <h1>
                <?php echo htmlspecialchars($musica['nombre_cancion']); ?>
            </h1>

            <h2>
                🎤 <?php echo htmlspecialchars($musica['nombre_cantante']); ?>
            </h2>

        </div>

    </div>

    <div class="audio-container">

    <audio controls style="width:100%;">

        <source
            src="uploads/musica/<?php echo $musica['audio']; ?>">

        Tu navegador no soporta audio.

    </audio>

</div>

    <div class="descripcion">

        <h3>📝 Descripción</h3>

        <p>
            <?php echo nl2br(htmlspecialchars($musica['descripcion'])); ?>
        </p>

    </div>

   <?php if($esPropietario): ?>

<div class="acciones">

    <a
        href="editar_musica.php?id=<?php echo $musica['musica_id']; ?>"
        class="btn-editar"
    >
        ✏️ Editar
    </a>

    <a
        href="eliminar_musica.php?id=<?php echo $musica['musica_id']; ?>"
        class="btn-eliminar"
        onclick="return confirm('¿Seguro que deseas eliminar esta publicación?');"
    >
        🗑️ Eliminar
    </a>

</div>

<?php endif; ?>

</div>

</body>
</html>