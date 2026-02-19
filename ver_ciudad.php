<?php
require 'db.php';

$idCiudad = isset($_GET['idCiudad']) ? intval($_GET['idCiudad']) : 0;

$sql = "SELECT c.idCiudad, c.nombreCiudad, r.nombreRegion 
        FROM ciudades c
        JOIN regiones r ON c.idRegion = r.idRegion
        WHERE c.idCiudad = ? AND c.vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCiudad);
$stmt->execute();
$result = $stmt->get_result();
$ciudad = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ver Ciudad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Ver Ciudad
                    <a href="gestionar_ciudades.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php if ($ciudad): ?>
                <div class="mb-3">
                    <label><b>Región</b></label>
                    <p class="form-control"><?= htmlspecialchars($ciudad['nombreRegion']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Ciudad</b></label>
                    <p class="form-control"><?= htmlspecialchars($ciudad['nombreCiudad']) ?></p>
                </div>
                <?php else: ?>
                <h5>Ciudad no encontrada</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>