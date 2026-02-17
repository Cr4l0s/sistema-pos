<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ver Usuario</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="
        sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    <body>
        <?php include('navbar.php'); ?>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Ver Usuario
                                <a href="inicio.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['idUsuario'])) {
                                $usuario_id = mysqli_real_escape_string($conn, $_GET['idUsuario']);
                                $sql = "SELECT * FROM usuarios WHERE idUsuario ='$usuario_id' AND vigente = 1";
                                $query = mysqli_query($conn, $sql);
                                
                            if(mysqli_num_rows($query)> 0) {
                                $usuario = mysqli_fetch_array($query);
                            ?>
                            <div class="mb-3">
                                <label><b>Nombres</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['nombres']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Apellido Paterno</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['ApPaterno']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Apellido Materno</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['ApMaterno']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Nombre de Usuario</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['NombreUsuario']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Teléfono Fijo</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['fonofijo']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Teléfono Celular 1</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['fonocelular1']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Teléfono Celular 2</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['fonocelular2']; ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label><b>Email</b></label>
                                <p class="form-control">
                                    <?php echo $usuario['email']; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label><b>Fecha de Registro</b></label>
                                <p class="form-control">
                                    <?php echo date('d-m-Y H:i:s', strtotime($usuario['fecha_registro'])); ?>
                                </p>
                            </div>
                            <?php
                            } else {
                            echo '<h5>No se ha encontrado al usuario</h5>';
                            }   
                        } 
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="
        sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
