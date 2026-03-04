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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Países</title>
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
                <h4>Listado de Países</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sigla</th>
                            <th>Código Moneda</th>
                            <th>Nombre</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultados_por_pagina = FILASXPAGINA;
                        $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
                        $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                        $sql_total = "SELECT COUNT(*) as total FROM paises WHERE vigente = 1";
                        $stmt_total = $conn->prepare($sql_total);
                        $stmt_total->execute();
                        $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                        $stmt_total->close();
                        $total_paginas = ceil($total_registros / $resultados_por_pagina);

                        $sql = "SELECT * FROM paises WHERE vigente = 1 ORDER BY nombrePais LIMIT ?, ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ii", $calculo, $resultados_por_pagina);
                        $stmt->execute();
                        $paises = $stmt->get_result();

                        if ($paises->num_rows > 0) {
                            while ($pais = $paises->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($pais['siglaPais']) ?></td>
                                    <td><?= htmlspecialchars($pais['codMoneda']) ?></td>
                                    <td><?= htmlspecialchars($pais['nombrePais']) ?></td>
                                    <td>
                                        <!-- VER -->
                                        <form action="menu.php?page=pais-ver.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idPais" value="<?= $pais['idPais'] ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <span class="bi bi-eye-fill"></span> Ver
                                            </button>
                                        </form>

                                        <!-- EDITAR -->
                                        <form action="menu.php?page=pais-editar.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idPais" value="<?= $pais['idPais'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <span class="bi bi-pencil-fill"></span> Editar
                                            </button>
                                        </form>

                                        <!-- ELIMINAR -->
                                        <form action="eliminar_pais.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idPais" value="<?= $pais['idPais'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar país?')">
                                                <span class="bi bi-trash3-fill"></span> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            $stmt->close();
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No hay países</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <!-- PAGINACIÓN MEJORADA (con queryString) -->
                <?php
                $params = $_GET;
                unset($params['pagina']);
                $queryString = http_build_query($params);
                ?>
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

                <!-- BOTÓN AGREGAR DEBAJO DE LA TABLA (consistente con otros mantenedores) -->
                <div class="mt-3 text-center">
                    <a href="pais-crear.php" class="btn btn-primary">
                        <span class="bi bi-plus-circle-fill"></span> Agregar País
                    </a>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>