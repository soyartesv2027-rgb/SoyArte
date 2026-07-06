<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contáctanos</title>

    <link rel="stylesheet" href="styles/contacto.css?v=<?php echo time(); ?>">
    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container py-5">

        <div class="container mt-3">
            <div class="botones-superiores">

                    <a href="javascript:history.back()" class="btn-volver">
                        ← Volver
                    </a>

                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>

                        <a href="admin/mensaje.php" class="btn-admin">
                            📩 Ver mensajes
                        </a>

                    <?php endif; ?>

            </div>

        </div>

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card shadow">

                <div class="card-header bg-dark text-white">
                    <h3 class="mb-0">Contáctanos</h3>
                </div>

                <div class="card-body">

                    <form id="formContacto">

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input
                                type="text"
                                name="nombre"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Correo</label>
                            <input
                                type="email"
                                name="correo"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Asunto</label>
                            <input
                                type="text"
                                name="asunto"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Mensaje</label>
                            <textarea
                                name="mensaje"
                                rows="6"
                                class="form-control"
                                required></textarea>
                        </div>

                        <button class="btn btn-success w-100">
                            Enviar mensaje
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

const formulario = document.getElementById("formContacto");

formulario.addEventListener("submit", function(e){

    e.preventDefault();

    let datos = new FormData(this);

    fetch("php/guardar_contacto.php",{

        method:"POST",
        body:datos

    })
    .then(res=>res.text())
    .then(res=>{

        if(res=="ok"){

            Swal.fire({
                icon:"success",
                title:"Mensaje enviado",
                text:"Gracias por contactarnos."
            });

            formulario.reset();

        }else{

            Swal.fire({
                icon:"error",
                title:"Error",
                text:res
            });

        }

    });

});

</script>

</body>
</html>