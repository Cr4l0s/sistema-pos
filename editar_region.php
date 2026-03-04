<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idRegion'])) {
    $idRegion = intval($_POST['idRegion']);
} else {
    header('Location: gestionar_regiones.php');
    exit;
}

// Obtener datos de la región
$sql = "SELECT * FROM regiones WHERE idRegion = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegion);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Región no encontrada';
    header('Location: gestionar_regiones.php');
    exit;
}

$region = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Región</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar Región</h4>
                <a href="menu.php?page=gestionar_regiones.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="acciones_region.php" method="POST">
                    <input type="hidden" name="idRegion" value="<?= $region['idRegion'] ?>">
                    <div class="mb-3">
                        <label for="nombreRegion" class="form-label">Nombre de la Región</label>
                        <input type="text" class="form-control" name="nombreRegion" id="nombreRegion" 
                               value="<?= htmlspecialchars($region['nombreRegion']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="codRegion" class="form-label">Código de la Región</label>
                        <input type="text" class="form-control" name="codRegion" id="codRegion" 
                               value="<?= htmlspecialchars($region['codRegion']) ?>" required>
                    </div>
                    <button type="submit" name="update_region" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>