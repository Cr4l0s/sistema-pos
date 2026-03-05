<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .paginacion {
            margin: 20px 0;
            text-align: center;
        }

        .paginacion a {
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #666;
        }

        .paginacion .actual {
            padding: 5px 10px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>
        <div class="card">
            <div class="card-header">
                <h4>Listado de Usuarios</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultados_por_pagina = FILASXPAGINA;
                        $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
                        $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                        $sql_total = "SELECT COUNT(*) as total FROM usuarios WHERE vigente = 1";
                        $stmt_total = $conn->prepare($sql_total);
                        $stmt_total->execute();
                        $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                        $stmt_total->close();
                        $total_paginas = ceil($total_registros / $resultados_por_pagina);

                        $sql = "SELECT * FROM usuarios WHERE vigente = 1 ORDER BY nombres LIMIT ?, ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $calculo, $resultados_por_pagina);
                        $stmt->execute();
                        $usuarios = $stmt->get_result();

                        if ($usuarios->num_rows > 0) {
                            while ($usuario = $usuarios->fetch_assoc()) {
                                $apellidos = trim(($usuario['ApPaterno'] ?? '') . ' ' . ($usuario['ApMaterno'] ?? ''));
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['nombres']) ?></td>
                                    <td><?= htmlspecialchars($apellidos) ?></td>
                                    <td><?= htmlspecialchars($usuario['NombreUsuario']) ?></td>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td>
                                        <!-- VER -->
                                        <form action="menu.php?page=usuario-ver.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idUsuario" value="<?= $usuario['idUsuario'] ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <span class="bi bi-eye-fill"></span> Ver
                                            </button>
                                        </form>

                                        <!-- EDITAR -->
                                        <form action="menu.php?page=usuario-editar.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idUsuario" value="<?= $usuario['idUsuario'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <span class="bi bi-pencil-fill"></span> Editar
                                            </button>
                                        </form>

                                        <!-- ELIMINAR -->
                                        <form action="acciones-usuario.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="borrar_usuario" value="<?= $usuario['idUsuario'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar usuario?')">
                                                <span class="bi bi-trash3-fill"></span> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            $stmt->close();
                        } else {
                            echo '<tr><td colspan="5" class="text-center">No hay usuarios</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <!-- PAGINACIÓN MEJORADA (con queryString) -->
                <!-- Antes de la paginación -->
                <?php
                $page_param = isset($_GET['page']) ? $_GET['page'] : basename($_SERVER['PHP_SELF']);

                $params = $_GET;
                unset($params['pagina']);
                $queryString = http_build_query($params);

                if (!isset($params['page']) && $page_param) {
                    $queryString = http_build_query(array_merge($params, ['page' => $page_param]));
                }
                ?>

                <!-- Enlaces de paginación -->
                <div class="paginacion">

                    <?php if ($pagina_actual > 1): ?>
                        <a href="?<?php echo $queryString; ?>&pagina=<?php echo ($pagina_actual - 1); ?>">
                            Anterior
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <?php if ($i == $pagina_actual): ?>
                            <span class="actual"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="?<?php echo $queryString; ?>&pagina=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?<?php echo $queryString; ?>&pagina=<?php echo ($pagina_actual + 1); ?>">
                            Siguiente
                        </a>
                    <?php endif; ?>

                </div>

                <!-- BOTÓN AGREGAR DEBAJO DE LA TABLA -->
                <div class="mt-3 text-center">
                    <a href="usuario-crear.php" class="btn btn-primary">
                        <span class="bi bi-plus-circle-fill"></span> Agregar Usuario
                    </a>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>