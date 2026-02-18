<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <a href="inicio.php" class="btn btn-danger float-end">Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="acciones-usuario.php" method="POST">
                            <div class="mb-3">
                                <label>Nombres</label>
                                <input type="text" class="form-control" name="nombres" required>
                            </div>
                            <div class="mb-3">
                                <label>Apellido Paterno</label>
                                <input type="text" class="form-control" name="apPaterno" required>
                            </div>
                            <div class="mb-3">
                                <label>Apellido Materno</label>
                                <input type="text" class="form-control" name="apMaterno" required>
                            </div>
                            <div class="mb-3">
                                <label>Nombre de Usuario</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label>Teléfono Fijo</label>
                                <input type="text" class="form-control" name="fonofijo">
                            </div>
                            <div class="mb-3">
                                <label>Teléfono Celular 1</label>
                                <input type="text" class="form-control" name="fonocelular1">
                            </div>
                            <div class="mb-3">
                                <label>Teléfono Celular 2</label>
                                <input type="text" class="form-control" name="fonocelular2">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <!-- SELECCIÓN MÚLTIPLE DE ROLES -->
                            <div class="mb-3">
                                <label>Roles del usuario</label>
                                <?php
                                $roles = mysqli_query($conn, "SELECT * FROM roles");
                                while($rol = mysqli_fetch_array($roles)) {
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" name="roles[]" value="' . $rol['id_rol'] . '">';
                                    echo '<label class="form-check-label">' . $rol['nombre_rol'] . '</label>';
                                    echo '</div>';
                                }
                                ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>