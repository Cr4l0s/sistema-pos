<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Editar</title>
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
                        <h4>Editar Usuario
                            <a href="inicio.php" class="btn btn-danger float-end"><span
                                    class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['idUsuario'])) {
                            $usuario_id = mysqli_real_escape_string($conn, $_GET['idUsuario']);
                            $sql = "SELECT * FROM usuarios WHERE idUsuario = '$usuario_id' AND vigente = 1";
                            $query = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($query) > 0) {
                                $usuario = mysqli_fetch_array($query);

                                ?>
                                <form action="acciones-usuario.php" method="POST">
                                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['idUsuario'] ?>">

                                    <div class="mb-3">
                                        <label>Nombres</label>
                                        <input type="text" name="nombres" value="<?php echo $usuario['nombres'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Apellido Paterno</label>
                                        <input type="text" name="apPaterno" value="<?php echo $usuario['ApPaterno'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Apellido Materno</label>
                                        <input type="text" name="apMaterno" value="<?php echo $usuario['ApMaterno'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Nombre de Usuario</label>
                                        <input type="text" name="username" value="<?php echo $usuario['NombreUsuario'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Teléfono Fijo</label>
                                        <input type="text" name="fonofijo" value="<?php echo $usuario['fonofijo'] ?>"
                                            class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label>Teléfono Celular1</label>
                                        <input type="text" name="fonocelular1" value="<?php echo $usuario['fonocelular1'] ?>"
                                            class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Teléfono Celular2</label>
                                        <input type="text" name="fonocelular2" value="<?php echo $usuario['fonocelular2'] ?>"
                                            class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label>Email</label>
                                        <input type="text" class="form-control" name="email"
                                            value="<?php echo $usuario['email'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Password</label>
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                    <!-- SELECCIÓN MÚLTIPLE DE ROLES -->
                                    <div class="mb-3">
                                        <label><b>Roles del usuario</b></label>
                                        <?php
                                        // Obtener todos los roles disponibles
                                        $roles = mysqli_query($conn, "SELECT * FROM roles");

                                        // Obtener roles actuales del usuario desde la tabla intermedia
                                        $roles_usuario = [];
                                        $sql_roles_actuales = "SELECT id_rol FROM usuarios_roles WHERE idUsuario = $usuario_id";
                                        $result_roles = mysqli_query($conn, $sql_roles_actuales);
                                        while ($row = mysqli_fetch_array($result_roles)) {
                                            $roles_usuario[] = $row['id_rol'];
                                        }

                                        // Mostrar checkboxes marcando los que ya tiene
                                        while ($rol = mysqli_fetch_array($roles)) {
                                            $checked = in_array($rol['id_rol'], $roles_usuario) ? 'checked' : '';
                                            echo '<div class="form-check">';
                                            echo '<input class="form-check-input" type="checkbox" name="roles[]" value="' . $rol['id_rol'] . '" ' . $checked . '>';
                                            echo '<label class="form-check-label">' . $rol['nombre_rol'] . '</label>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" name="update_usuario" class="btn btn-primary"><span
                                                class="bi bi-database-up"></span>&nbsp;Actualizar</button>
                                    </div>
                                </form>
                                <?php
                            } else {
                                echo "<h5>Usuario no encontrado</h5>";
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