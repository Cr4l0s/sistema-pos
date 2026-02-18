<?php
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Usuario</title>
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
                        <h4>Ver Usuario
                            <a href="inicio.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['idUsuario'])) {
                            $usuario_id = intval($_GET['idUsuario']);
                            
                            // Consulta principal del usuario
                            $sql = "SELECT * FROM usuarios WHERE idUsuario = ? AND vigente = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $usuario_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            
                            if ($result->num_rows > 0) {
                                $usuario = $result->fetch_assoc();
                                $stmt->close();
                        ?>
                        <div class="mb-3">
                            <label><b>Nombres</b></label>
                            <p class="form-control"><?php echo $usuario['nombres']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Apellido Paterno</b></label>
                            <p class="form-control"><?php echo $usuario['ApPaterno']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Apellido Materno</b></label>
                            <p class="form-control"><?php echo $usuario['ApMaterno']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Nombre de Usuario</b></label>
                            <p class="form-control"><?php echo $usuario['NombreUsuario']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Teléfono Fijo</b></label>
                            <p class="form-control"><?php echo $usuario['fonofijo']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Teléfono Celular 1</b></label>
                            <p class="form-control"><?php echo $usuario['fonocelular1']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Teléfono Celular 2</b></label>
                            <p class="form-control"><?php echo $usuario['fonocelular2']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Email</b></label>
                            <p class="form-control"><?php echo $usuario['email']; ?></p>
                        </div>

                        <!-- SECCIÓN: MOSTRAR ROLES -->
                        <div class="mb-3">
                            <label><b>Roles asignados</b></label>
                            <p class="form-control">
                                <?php
                                $sql_roles = "SELECT r.nombre_rol 
                                              FROM usuarios_roles ur 
                                              JOIN roles r ON ur.id_rol = r.id_rol 
                                              WHERE ur.idUsuario = ?";
                                $stmt_roles = $conn->prepare($sql_roles);
                                $stmt_roles->bind_param("i", $usuario_id);
                                $stmt_roles->execute();
                                $roles_result = $stmt_roles->get_result();

                                if ($roles_result->num_rows > 0) {
                                    $lista_roles = [];
                                    while ($rol = $roles_result->fetch_assoc()) {
                                        $lista_roles[] = $rol['nombre_rol'];
                                    }
                                    echo implode(', ', $lista_roles);
                                } else {
                                    echo '<span class="text-muted">Sin roles asignados</span>';
                                }
                                $stmt_roles->close();
                                ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>