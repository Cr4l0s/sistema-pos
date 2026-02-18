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
                        <h4>Editar Usuario
                            <a href="inicio.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['idUsuario'])) {
                            $usuario_id = intval($_GET['idUsuario']);
                            
                            $sql = "SELECT * FROM usuarios WHERE idUsuario = ? AND vigente = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $usuario_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $usuario = $result->fetch_assoc();
                                $stmt->close();
                        ?>
                        <form action="acciones-usuario.php" method="POST">
                            <input type="hidden" name="usuario_id" value="<?php echo $usuario['idUsuario']; ?>">

                            <div class="mb-3">
                                <label>Nombres</label>
                                <input type="text" name="nombres" value="<?php echo $usuario['nombres']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Apellido Paterno</label>
                                <input type="text" name="apPaterno" value="<?php echo $usuario['ApPaterno']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Apellido Materno</label>
                                <input type="text" name="apMaterno" value="<?php echo $usuario['ApMaterno']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Nombre de Usuario</label>
                                <input type="text" name="username" value="<?php echo $usuario['NombreUsuario']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Teléfono Fijo</label>
                                <input type="text" name="fonofijo" value="<?php echo $usuario['fonofijo']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Teléfono Celular1</label>
                                <input type="text" name="fonocelular1" value="<?php echo $usuario['fonocelular1']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Teléfono Celular2</label>
                                <input type="text" name="fonocelular2" value="<?php echo $usuario['fonocelular2']; ?>" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="text" class="form-control" name="email" value="<?php echo $usuario['email']; ?>">
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
                                $roles_sql = "SELECT * FROM roles";
                                $roles_result = $conn->query($roles_sql);

                                // Obtener roles actuales del usuario
                                $roles_usuario = [];
                                $roles_actuales_sql = "SELECT id_rol FROM usuarios_roles WHERE idUsuario = ?";
                                $stmt_roles = $conn->prepare($roles_actuales_sql);
                                $stmt_roles->bind_param("i", $usuario_id);
                                $stmt_roles->execute();
                                $roles_actuales_result = $stmt_roles->get_result();
                                while ($row = $roles_actuales_result->fetch_assoc()) {
                                    $roles_usuario[] = $row['id_rol'];
                                }
                                $stmt_roles->close();

                                // Mostrar checkboxes
                                while ($rol = $roles_result->fetch_assoc()) {
                                    $checked = in_array($rol['id_rol'], $roles_usuario) ? 'checked' : '';
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" name="roles[]" value="' . $rol['id_rol'] . '" ' . $checked . '>';
                                    echo '<label class="form-check-label">' . $rol['nombre_rol'] . '</label>';
                                    echo '</div>';
                                }
                                ?>
                            </div>

                            <div class="mb-3">
                                <button type="submit" name="update_usuario" class="btn btn-primary">Actualizar</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>