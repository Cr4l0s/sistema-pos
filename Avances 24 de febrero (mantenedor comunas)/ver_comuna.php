<?php
require 'db.php';

$idComuna = isset($_GET['idComuna']) ? intval($_GET['idComuna']) : 0;

$sql = "SELECT com.*, ciu.nombreCiudad, reg.nombreRegion, pai.nombrePais
        FROM comunas com
        JOIN ciudades ciu ON com.idCiudad = ciu.idCiudad
        JOIN regiones reg ON ciu.idRegion = reg.idRegion
        JOIN paises pai ON reg.idPais = pai.idPais
        WHERE com.idComuna = ? AND com.vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idComuna);
$stmt->execute();
$result = $stmt->get_result();
$comuna = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ver Comuna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Ver Comuna
                    <a href="gestionar_comunas.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php if ($comuna): ?>
                <div class="mb-3">
                    <label><b>ID Comuna</b></label>
                    <p class="form-control"><?= $comuna['idComuna'] ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Nombre</b></label>
                    <p class="form-control"><?= htmlspecialchars($comuna['nomComuna']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Ciudad</b></label>
                    <p class="form-control"><?= htmlspecialchars($comuna['nombreCiudad']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Región</b></label>
                    <p class="form-control"><?= htmlspecialchars($comuna['nombreRegion']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>País</b></label>
                    <p class="form-control"><?= htmlspecialchars($comuna['nombrePais']) ?></p>
                </div>
                <?php else: ?>
                <h5>Comuna no encontrada</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>