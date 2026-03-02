<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Capturar datos del POST y guardarlos en sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['idPais'] = intval($_POST['idPais']);
    $_SESSION['idRegion'] = intval($_POST['idRegion']);

    // Obtener nombres
    $sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ?";
    $stmt = $conn->prepare($sqlPais);
    $stmt->bind_param("i", $_SESSION['idPais']);
    $stmt->execute();
    $_SESSION['nombrePais'] = $stmt->get_result()->fetch_assoc()['nombrePais'];
    $stmt->close();

    $sqlRegion = "SELECT nombreRegion FROM regiones WHERE idRegion = ?";
    $stmt = $conn->prepare($sqlRegion);
    $stmt->bind_param("i", $_SESSION['idRegion']);
    $stmt->execute();
    $_SESSION['nombreRegion'] = $stmt->get_result()->fetch_assoc()['nombreRegion'];
    $stmt->close();
}

// Recuperar de sesión
$idPais = $_SESSION['idPais'] ?? null;
$nombrePais = $_SESSION['nombrePais'] ?? null;
$idRegion = $_SESSION['idRegion'] ?? null;
$nombreRegion = $_SESSION['nombreRegion'] ?? null;

// Verificar que existan país y región
if (!$idPais || !$nombrePais || !$idRegion || !$nombreRegion) {
    echo "<h2>No ha seleccionado País y Región</h2>";
    echo "<a href='selector_pais_y_region.php' class='btn btn-primary'>Volver a Seleccionar</a>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mantenedor de Ciudades</title>
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
            <h3>Ciudades para la Región: [<?= htmlspecialchars($nombreRegion) ?>] del País:
                [<?= htmlspecialchars($nombrePais) ?>]</h3>
            <a href="selector_pais_y_region.php" class="btn btn-danger">
                <span class="bi bi-arrow-left"></span> Volver
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Listado de Ciudades</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nombre Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultados_por_pagina = 8;
                        $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
                        $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                        // Total de registros
                        $sql_total = "SELECT COUNT(*) as total FROM ciudades WHERE idRegion = ? AND vigente = 1";
                        $stmt_total = $conn->prepare($sql_total);
                        $stmt_total->bind_param("i", $idRegion);
                        $stmt_total->execute();
                        $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                        $stmt_total->close();
                        $total_paginas = ceil($total_registros / $resultados_por_pagina);

                        // Consulta paginada
                        $sql = "SELECT * FROM ciudades WHERE idRegion = ? AND vigente = 1 ORDER BY nombreCiudad LIMIT ?, ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iii", $idRegion, $calculo, $resultados_por_pagina);
                        $stmt->execute();
                        $ciudades = $stmt->get_result();

                        if ($ciudades->num_rows > 0) {
                            while ($ciudad = $ciudades->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($ciudad['nombreCiudad']) ?></td>
                                    <td>
                                        <!-- VER -->
                                        <form action="ver_ciudad.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idCiudad" value="<?= $ciudad['idCiudad'] ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">
                                                <span class="bi bi-eye-fill"></span> Ver
                                            </button>
                                        </form>
                                        <!-- EDITAR -->
                                        <form action="editar_ciudad.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idCiudad" value="<?= $ciudad['idCiudad'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <span class="bi bi-pencil-fill"></span> Editar
                                            </button>
                                        </form>
                                        <!-- ELIMINAR -->
                                        <form action="eliminar_ciudad.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="idCiudad" value="<?= $ciudad['idCiudad'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('¿Confirma la eliminación?')">
                                                <span class="bi bi-trash3-fill"></span> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            $stmt->close();
                        } else {
                            echo '<tr><td colspan="2" class="text-center">No hay ciudades registradas</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="paginacion">
            <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=<?= $pagina_actual - 1 ?>">Anterior</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if ($i == $pagina_actual): ?>
                    <span class="actual"><?= $i ?></span>
                <?php else: ?>
                    <a href="?pagina=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_actual + 1 ?>">Siguiente</a>
            <?php endif; ?>
        </div>

        <!-- Formulario para agregar nueva ciudad -->
        <br>
        <h3>Agregar Nueva Ciudad</h3>
        <form method="POST" action="agregar_ciudad.php">
            <input type="hidden" name="idRegion" value="<?= $idRegion ?>">
            <div class="row">
                <div class="col-md-8">
                    <label for="nombreCiudad" class="form-label">Nombre de la Ciudad:</label>
                    <input type="text" class="form-control" name="nombreCiudad" id="nombreCiudad" required>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Agregar Ciudad</button>
                </div>
            </div>
        </form>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>