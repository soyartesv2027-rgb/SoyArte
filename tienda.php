<?php
session_start();
?>

<?php
require_once 'php/conexion.php';

$buscar = $_GET['buscar'] ?? '';

if(!empty($buscar)){

    $buscar = $conn->real_escape_string($buscar);

    $sql = "
        SELECT *
        FROM productos
        WHERE nombre LIKE '%$buscar%'
        OR descripcion LIKE '%$buscar%'
        OR categoria LIKE '%$buscar%'
    ";

}else{

    $sql = "SELECT * FROM productos";

}

$resultado = $conn->query($sql);
if (!$resultado) {
    die("Error SQL: " . $conn->error);
}



?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles/tienda.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="shortcut icon" href="favicon_io/favicon.ico" type="image/x-icon">
<title>Tienda SoyArte</title>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<section class="encabezado-tienda">

    <h1>🎨 Tienda SoyArte</h1>

    <form method="GET" class="buscador">

        <input
            type="text"
            name="buscar"
            placeholder="Buscar obras, crochet, pinturas..."
            value="<?php echo htmlspecialchars($buscar); ?>"
        >

        <button type="submit">
            Buscar
        </button>

    </form>

</section>

<section class="productos">

<?php if($resultado->num_rows > 0): ?>

    <?php while($producto = $resultado->fetch_assoc()): ?>

    <a
    href="producto.php?id=<?php echo $producto['id']; ?>"
    class="card-link">
    <div class="card">
          <img
                src="uploads/<?php echo htmlspecialchars($producto['imagen']); ?>"
                alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
            >

            <div class="card-body">

                <span class="categoria">
                    <?php echo htmlspecialchars($producto['categoria']); ?>
                </span>

                <h3>
                    <?php echo htmlspecialchars($producto['nombre']); ?>
                </h3>

                <p>
                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                </p>

                <div class="precio">
                    $<?php echo number_format($producto['precio'], 2); ?>
                </div>

                <button class="btn-comprar">
                    Comprar
                </button>

            </div>
        </div>
        </div>
    </a>
    </div>

    <?php endwhile; ?>

<?php else: ?>

    <h2>No se encontraron productos.</h2>

<?php endif; ?>

</section>

<?php if(isset($_SESSION['usuario_id'])): ?>

 <a href="publicar_producto.php" class="añadir-boton">
    <button class="boton-plus">
      <i class="fa-solid fa-plus"></i>
    </button>
  </a>



<?php endif; ?>


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