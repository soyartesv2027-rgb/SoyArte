<?php
session_start();
require_once 'php/conexion.php';

$sql = "SELECT * FROM realidad_virtual ORDER BY fecha_creacion DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Realidad Virtual</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="styles/vr.css">
<link rel="stylesheet" href="style.css">

</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="contenedor">

    <div class="encabezado-vr">

        <h1>🥽 Realidad Virtual</h1>

        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>

            <a href="admin/dashboard.php" class="btn-panel-admin">
                ⚙️ Administrar VR
            </a>

        <?php endif; ?>

    </div>

    <div class="grid-vr">

        <?php while($vr = $resultado->fetch_assoc()): ?>

            <a
                href="ver_vr.php?id=<?php echo $vr['id']; ?>"
                class="card-vr"
            >

                <img
                    src="uploads/vr/portadas/<?php echo htmlspecialchars($vr['portada']); ?>"
                    alt="<?php echo htmlspecialchars($vr['titulo']); ?>"
                >

                <div class="card-info">

                    <h3>
                        <?php echo htmlspecialchars($vr['titulo']); ?>
                    </h3>

                    <span>👁 Explorar experiencia</span>

                </div>

            </a>

        <?php endwhile; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
 <script>
    window.addEventListener("scroll", () => {
      const section = document.querySelector(".info-soyarte");
      if (section) {
        const position = section.getBoundingClientRect().top;
        const screen = window.innerHeight;
        if (position < screen - 100) {
          section.classList.add("visible");
        }
      }
    });
  </script>
<script src="JavaScript/script.js"></script>

</body>
</html>