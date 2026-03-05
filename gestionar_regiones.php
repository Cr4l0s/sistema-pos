<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require_once 'config.php';

// Capturar idPais y nombrePais desde el formulario y guardarlos en la sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPais = intval($_POST['idPais']);
    $nombrePais = trim($_POST['nombrePais']);

    // Guardar en la sesión
    $_SESSION['idPais'] = $idPais;
    $_SESSION['nombrePais'] = $nombrePais;

    if ($idPais <= 0) {
        echo "<h2>No ha seleccionado País</h2>";
        echo "<a href='selector_pais.php' class='btn btn-primary'>Volver a Seleccionar País</a>";
        exit;
    }
} else {
    // Recuperar de la sesión
    $idPais = $_SESSION['idPais'] ?? null;
    $nombrePais = $_SESSION['nombrePais'] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenedor de Regiones</title>
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

        <?php if ($idPais && $nombrePais): ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Regiones del País: <?= htmlspecialchars($nombrePais) ?></h2>
                <a href="menu.php?page=selector_pais.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4>Listado de Regiones</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre Región</th>
                                <th>Código Región</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $resultados_por_pagina = FILASXPAGINA;
                            $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
                            $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                            // Total de registros (prepared statement)
                            $sql_total = "SELECT COUNT(*) as total FROM regiones WHERE idPais = ? AND vigente = 1";
                            $stmt_total = $conn->prepare($sql_total);
                            $stmt_total->bind_param("i", $idPais);
                            $stmt_total->execute();
                            $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                            $stmt_total->close();
                            $total_paginas = ceil($total_registros / $resultados_por_pagina);

                            // Consulta paginada (prepared statement)
                            $sql = "SELECT * FROM regiones WHERE idPais = ? AND vigente = 1 ORDER BY nombreRegion LIMIT ?, ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("iii", $idPais, $calculo, $resultados_por_pagina);
                            $stmt->execute();
                            $regiones = $stmt->get_result();

                            if ($regiones->num_rows > 0) {
                                while ($region = $regiones->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($region['nombreRegion']) ?></td>
                                        <td><?= htmlspecialchars($region['codRegion']) ?></td>
                                        <td>
                                            <!-- VER -->
                                            <form action="menu.php?page=ver_region.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="idRegion" value="<?= $region['idRegion'] ?>">
                                                <button type="submit" class="btn btn-secondary btn-sm">
                                                    <span class="bi bi-eye-fill"></span> Ver
                                                </button>
                                            </form>

                                            <!-- EDITAR -->
                                            <form action="menu.php?page=editar_region.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="idRegion" value="<?= $region['idRegion'] ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <span class="bi bi-pencil-fill"></span> Editar
                                                </button>
                                            </form>

                                            <!-- ELIMINAR -->
                                            <form action="eliminar_region.php" method="POST" style="display:inline;">
                                                <input type="hidden" name="idRegion" value="<?= $region['idRegion'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('¿Eliminar región?')">
                                                    <span class="bi bi-trash3-fill"></span> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $stmt->close();
                            } else {
                                echo '<tr><td colspan="3" class="text-center">No hay regiones registradas</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

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

            <!-- Formulario para agregar nueva región -->
            <br>
            <h3>Agregar Nueva Región</h3>
            <form method="POST" action="agregar_region.php">
                <input type="hidden" name="idPais" value="<?= $idPais ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nombreRegion" class="form-label">Nombre de la Región:</label>
                        <input type="text" class="form-control" name="nombreRegion" id="nombreRegion" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="codRegion" class="form-label">Código de la Región:</label>
                        <input type="text" class="form-control" name="codRegion" id="codRegion" required>
                    </div>
                    <div class="col-md-4 mb-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Agregar Región</button>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <h2>Seleccione un País para gestionar Regiones</h2>
            <a href="selector_pais.php" class="btn btn-primary">Volver a Seleccionar País</a>
        <?php endif; ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>