<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuarios</title>
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
                            <h4>Agregar Usuario
                                <a href="inicio.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="acciones-usuario.php" method="POST" onsubmit="return validarFormulario();">
                                <div class="mb-3">
                                    <label>Nombres</label>
                                    <input type="text" class="form-control" name="nombres" pattern=".{3,}" title="El nombre debe contener 3 caracteres o más."" required>
                                </div>
                                <div class="mb-3">
                                    <label>Apellido Paterno</label>
                                    <input type="text" class="form-control" name="apPaterno" pattern=".{3,}" title="El apellido debe contener 3 caracteres o más."" required>
                                </div>
                                <div class="mb-3">
                                    <label>Apellido Materno</label>
                                    <input type="text" class="form-control" name="apMaterno" pattern=".{3,}" title="El apellido debe contener 3 caracteres o más."" required>
                                </div>
                                <div class="mb-3">
                                    <label>Nombre de Usuario</label>
                                    <input type="text" class="form-control" name="username" pattern=".{3,}" title="El nombre de usuario debe contener 3 caracteres o más."" required>
                                </div>
                                <div class="mb-3">
                                    <label>Teléfono Fijo</label>
                                    <input type="text" class="form-control" name="fonofijo" title="" >
                                </div>
                                <div class="mb-3">
                                    <label>Teléfono Celular 1</label>
                                    <input type="text" class="form-control" name="fonocelular1" title="" >
                                </div>
                                <div class="mb-3">
                                    <label>Teléfono Celular 2</label>
                                    <input type="text" class="form-control" name="fonocelular2" title="" >
                                </div>
                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" id="correo" class="form-control" name="email" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title ="Ingrese un email válido." required>
                                </div>
                                <div class="mb-3">
                                    <label>Password</label>
                                    <input type="password" class="form-control" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Debe contener como mínimo un número, una letra mayúscula y una minúscula y un largo de 8 caracteres como mínimo." required>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" name="create_usuario" class="btn btn-primary">Grabar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<?php
			function validarFormulario() {
				echo correo;
				if(filter_var(correo.value, FILTER_VALIDATE_EMAIL)) {
					return true;
				} else
					return false;
			}
		?>
		
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="
        sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
