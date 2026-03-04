<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idComuna'])) {
    header('Location: gestionar_comunas.php');
    exit;
}

$idComuna = intval($_POST['idComuna']);

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

if (!$comuna) {
    $_SESSION['mensaje'] = 'Comuna no encontrada';
    header('Location: gestionar_comunas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Comuna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Comuna</h4>
                <a href="menu.php?page=gestionar_comunas.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><b>Nombre</b></label>
                    <p class="form-control"><?= htmlspecialchars($comuna['nombreComuna']) ?></p>
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
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>