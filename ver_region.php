<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idRegion'])) {
    header('Location: gestionar_regiones.php');
    exit;
}

$idRegion = intval($_POST['idRegion']);

// Obtener datos de la región
$sql = "SELECT idRegion, idPais, nombreRegion, codRegion 
        FROM regiones 
        WHERE idRegion = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegion);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['mensaje'] = 'Región no encontrada';
    header('Location: gestionar_regiones.php');
    exit;
}

$region = $result->fetch_assoc();
$stmt->close();

// Obtener nombre del país
$sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ? AND vigente = 1";
$stmtPais = $conn->prepare($sqlPais);
$stmtPais->bind_param("i", $region['idPais']);
$stmtPais->execute();
$pais = $stmtPais->get_result()->fetch_assoc();
$stmtPais->close();

$nombrePais = $pais['nombrePais'] ?? 'País no encontrado';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Región</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Región</h4>
                <a href="menu.php?page=gestionar_regiones.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><b>País</b></label>
                    <p class="form-control"><?= htmlspecialchars($nombrePais) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Región</b></label>
                    <p class="form-control"><?= htmlspecialchars($region['nombreRegion']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Código</b></label>
                    <p class="form-control"><?= htmlspecialchars($region['codRegion']) ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>