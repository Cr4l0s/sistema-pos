<?php
session_start();
include 'db.php';

// Capturar datos del POST y guardarlos en sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['idPais'] = intval($_POST['idPais']);
    
    // Obtener nombre del país
    $sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ?";
    $stmt = $conn->prepare($sqlPais);
    $stmt->bind_param("i", $_SESSION['idPais']);
    $stmt->execute();
    $_SESSION['nombrePais'] = $stmt->get_result()->fetch_assoc()['nombrePais'];
    $stmt->close();
}

// Recuperar de sesión
$idPais = $_SESSION['idPais'] ?? null;
$nombrePais = $_SESSION['nombrePais'] ?? null;

// Verificar que exista país
if (!$idPais || !$nombrePais) {
    echo "<h2>No ha seleccionado País</h2>";
    echo "<a href='selector_pais.php' class='btn btn-primary'>Volver a Seleccionar</a>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mantenedor de Regiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .paginacion { margin: 20px 0; text-align: center; }
        .paginacion a { padding: 5px 10px; margin: 0 5px; text-decoration: none; border: 1px solid #ddd; color: #666; }
        .paginacion .actual { padding: 5px 10px; margin: 0 5px; background-color: #007bff; color: white; border: 1px solid #007bff; }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>

        <h3>Regiones del País: [<?= htmlspecialchars($nombrePais) ?>]</h3>

        <div class="card">
            <div class="card-header">
                <h4>Listado de Regiones</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID Región</th>
                            <th>Nombre Región</th>
                            <th>Código</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $resultados_por_pagina = 8;
                        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                        $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                        // Total de registros
                        $sql_total = "SELECT COUNT(*) as total FROM regiones WHERE idPais = ? AND vigente = 1";
                        $stmt_total = $conn->prepare($sql_total);
                        $stmt_total->bind_param("i", $idPais);
                        $stmt_total->execute();
                        $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                        $stmt_total->close();
                        $total_paginas = ceil($total_registros / $resultados_por_pagina);

                        // Consulta paginada
                        $sql = "SELECT * FROM regiones WHERE idPais = ? AND vigente = 1 LIMIT ?, ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iii", $idPais, $calculo, $resultados_por_pagina);
                        $stmt->execute();
                        $regiones = $stmt->get_result();

                        if ($regiones->num_rows > 0) {
                            while ($region = $regiones->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?= $region['idRegion'] ?></td>
                            <td><?= htmlspecialchars($region['nombreRegion']) ?></td>
                            <td><?= htmlspecialchars($region['codRegion']) ?></td>
                            <td>
                                <a href="ver_region.php?idRegion=<?= $region['idRegion'] ?>" class="btn btn-secondary btn-sm">Ver</a>
                                <a href="editar_region.php?idRegion=<?= $region['idRegion'] ?>" class="btn btn-success btn-sm">Editar</a>
                                <form action="eliminar_region.php" method="POST" class="d-inline">
                                    <button onclick="return confirm('¿Confirma la eliminación?')" type="submit" name="idRegion" value="<?= $region['idRegion'] ?>" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                            }
                            $stmt->close();
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No hay regiones registradas</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="paginacion">
            <?php if ($pagina_actual > 1): ?>
                <a href="?pagina=<?= $pagina_actual-1 ?>">Anterior</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if ($i == $pagina_actual): ?>
                    <span class="actual"><?= $i ?></span>
                <?php else: ?>
                    <a href="?pagina=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?= $pagina_actual+1 ?>">Siguiente</a>
            <?php endif; ?>
        </div>

        <!-- Formulario para agregar nueva región -->
        <br>
        <h3>Agregar Nueva Región</h3>
        <form method="POST" action="agregar_region.php">
            <input type="hidden" name="idPais" value="<?= $idPais ?>">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Nombre de la Región:</label>
                    <input type="text" name="nombreRegion" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Código de la Región:</label>
                    <input type="text" name="codRegion" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3 align-self-end">
                    <input type="submit" value="Agregar Región" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>