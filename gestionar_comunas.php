<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require_once 'config.php';

// Capturar datos del POST y guardarlos en sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['idPais'] = intval($_POST['idPais']);
    $_SESSION['idRegion'] = intval($_POST['idRegion']);
    $_SESSION['idCiudad'] = intval($_POST['idCiudad']);

    // Obtener nombres
    $sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ?";
    $stmt = $conn->prepare($sqlPais);
    $stmt->bind_param("i", $_SESSION['idPais']);
    $stmt->execute();
    $_SESSION['nombrePais'] = $stmt->get_result()->fetch_assoc()['nombrePais'] ?? 'Desconocido';
    $stmt->close();

    $sqlRegion = "SELECT nombreRegion FROM regiones WHERE idRegion = ?";
    $stmt = $conn->prepare($sqlRegion);
    $stmt->bind_param("i", $_SESSION['idRegion']);
    $stmt->execute();
    $_SESSION['nombreRegion'] = $stmt->get_result()->fetch_assoc()['nombreRegion'] ?? 'Desconocido';
    $stmt->close();

    $sqlCiudad = "SELECT nombreCiudad FROM ciudades WHERE idCiudad = ?";
    $stmt = $conn->prepare($sqlCiudad);
    $stmt->bind_param("i", $_SESSION['idCiudad']);
    $stmt->execute();
    $_SESSION['nombreCiudad'] = $stmt->get_result()->fetch_assoc()['nombreCiudad'] ?? 'Desconocido';
    $stmt->close();
}

// Recuperar de sesión
$idPais = $_SESSION['idPais'] ?? null;
$nombrePais = $_SESSION['nombrePais'] ?? null;
$idRegion = $_SESSION['idRegion'] ?? null;
$nombreRegion = $_SESSION['nombreRegion'] ?? null;
$idCiudad = $_SESSION['idCiudad'] ?? null;
$nombreCiudad = $_SESSION['nombreCiudad'] ?? null;

// Validar que todos los datos necesarios estén presentes
if (!$idPais || !$idRegion || !$idCiudad) {
    echo "<h2>No ha seleccionado País, Región y Ciudad correctamente.</h2>";
    echo "<a href='selector_pais_region_ciudad.php' class='btn btn-primary'>Volver a Seleccionar</a>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mantenedor de Comunas</title>
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Comunas de la Ciudad: [<?= htmlspecialchars($nombreCiudad) ?>] en
                [<?= htmlspecialchars($nombreRegion) ?>, <?= htmlspecialchars($nombrePais) ?>]</h3>

            <!-- BOTÓN VOLVER -->
            <a href="menu.php?page=selector_pais_region_ciudad.php" class="btn btn-danger">
                <span class="bi bi-arrow-left"></span> Volver
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Listado de Comunas</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre Comuna</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultados_por_pagina = FILASXPAGINA;
                        $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
                        $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                        // Total de registros
                        $sql_total = "SELECT COUNT(*) as total FROM comunas WHERE idCiudad = ? AND vigente = 1";
                        $stmt_total = $conn->prepare($sql_total);
                        $stmt_total->bind_param("i", $idCiudad);
                        $stmt_total->execute();
                        $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                        $stmt_total->close();
                        $total_paginas = ceil($total_registros / $resultados_por_pagina);

                        // Consulta paginada
                        $sql = "SELECT * FROM comunas WHERE idCiudad = ? AND vigente = 1 ORDER BY nombreComuna LIMIT ?, ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iii", $idCiudad, $calculo, $resultados_por_pagina);
                        $stmt->execute();
                        $comunas = $stmt->get_result();

                        if ($comunas->num_rows > 0) {
                            while ($comuna = $comunas->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($comuna['nombreComuna']) ?></td>
                                    <td>
                                        <!-- VER -->
                                        <form action="menu.php?page=ver_comuna.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idComuna" value="<?= $comuna['idComuna'] ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <span class="bi bi-eye-fill"></span> Ver
                                            </button>
                                        </form>
                                        <!-- EDITAR -->
                                        <form action="menu.php?page=editar_comuna.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idComuna" value="<?= $comuna['idComuna'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <span class="bi bi-pencil-fill"></span> Editar
                                            </button>
                                        </form>
                                        <!-- ELIMINAR -->
                                        <form action="eliminar_comuna.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idComuna" value="<?= $comuna['idComuna'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Eliminar comuna?')">
                                                <span class="bi bi-trash3-fill"></span> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            $stmt->close();
                        } else {
                            echo '<tr><td colspan="2" class="text-center">No hay comunas registradas para esta ciudad.</td></tr>';
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

        <!-- Formulario para agregar nueva comuna -->
        <br>
        <h3>Agregar Nueva Comuna</h3>
        <form method="POST" action="agregar_comuna.php">
            <input type="hidden" name="idCiudad" value="<?= $idCiudad ?>">
            <div class="row">
                <div class="col-md-8">
                    <label for="nombreComuna" class="form-label">Nombre de la Comuna:</label>
                    <input type="text" class="form-control" name="nombreComuna" id="nombreComuna" required>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Agregar Comuna</button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>