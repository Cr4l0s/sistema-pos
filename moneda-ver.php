<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
$idMoneda = isset($_POST['idMoneda']) ? intval($_POST['idMoneda']) : 0;

if ($idMoneda == 0) {
    $_SESSION['mensaje'] = 'ID de moneda no válido';
    header('Location: menu.php?page=inicio_moneda.php');
    exit;
}

// Obtener datos de la moneda
$sql = "SELECT * FROM monedas WHERE idMoneda = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idMoneda);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Moneda no encontrada';
    header('Location: menu.php?page=inicio_moneda.php');
    exit;
}

$moneda = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver Moneda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-eye"></i> Ver Moneda
                </h4>
                <a href="menu.php?page=inicio_moneda.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Código de Moneda</th>
                        <td><?= htmlspecialchars($moneda['codMoneda']) ?></td>
                    </tr>
                    <tr>
                        <th>Nombre de la Moneda</th>
                        <td><?= htmlspecialchars($moneda['nombreMoneda']) ?></td>
                    </tr>
                    <tr>
                        <th>Símbolo de la Moneda</th>
                        <td><?= htmlspecialchars($moneda['simbolo']) ?></td>
                    </tr>
                </table>
                <div class="text-center mt-3">
                    <a href="menu.php?page=moneda-editar.php&id=<?= $idMoneda ?>" class="btn btn-success">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="menu.php?page=inicio_moneda.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>