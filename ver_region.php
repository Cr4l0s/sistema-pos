<?php
require 'db.php';

$idRegion = isset($_GET['idRegion']) ? intval($_GET['idRegion']) : 0;

$sql = "SELECT r.idRegion, r.nombreRegion, r.codRegion, p.nombrePais 
        FROM regiones r
        JOIN paises p ON r.idPais = p.idPais
        WHERE r.idRegion = ? AND r.vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegion);
$stmt->execute();
$result = $stmt->get_result();
$region = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ver Región</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Ver Región
                    <a href="gestionar_regiones.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php if ($region): ?>
                <div class="mb-3">
                    <label><b>País</b></label>
                    <p class="form-control"><?= htmlspecialchars($region['nombrePais']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Región</b></label>
                    <p class="form-control"><?= htmlspecialchars($region['nombreRegion']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Código</b></label>
                    <p class="form-control"><?= htmlspecialchars($region['codRegion']) ?></p>
                </div>
                <?php else: ?>
                <h5>Región no encontrada</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>