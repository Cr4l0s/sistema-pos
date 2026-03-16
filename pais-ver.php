<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por GET (desde la URL) o por POST
if (isset($_GET['id'])) {
    $idPais = intval($_GET['id']);
} elseif (isset($_POST['idPais'])) {
    $idPais = intval($_POST['idPais']);
} else {
    header('Location: inicio_pais.php');
    exit;
}

// Obtener datos del país
$sql = "SELECT * FROM paises WHERE idPais = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPais);
$stmt->execute();
$result = $stmt->get_result();
$pais = $result->fetch_assoc();
$stmt->close();

if (!$pais) {
    $_SESSION['mensaje'] = 'País no encontrado';
    header('Location: inicio_pais.php');
    exit;
}

// Obtener monedas del país
$monedas_sql = "SELECT m.*, pm.es_principal 
                FROM monedas m 
                JOIN paises_monedas pm ON m.idMoneda = pm.idMoneda 
                WHERE pm.idPais = ? 
                ORDER BY pm.es_principal DESC";
$monedas_stmt = $conn->prepare($monedas_sql);
$monedas_stmt->bind_param("i", $idPais);
$monedas_stmt->execute();
$monedas_result = $monedas_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ver País</title>
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
                    <i class="bi bi-eye"></i> Ver País
                    <span class="badge bg-info"><?= htmlspecialchars($pais['siglaPais']) ?></span>
                </h4>
                <div>
                    <a href="menu.php?page=pais-editar.php&id=<?= $idPais ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="menu.php?page=inicio_pais.php" class="btn btn-danger">
                        <span class="bi bi-arrow-left"></span> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Sigla</th>
                                <td><?= htmlspecialchars($pais['siglaPais']) ?></td>
                            </tr>
                            <tr>
                                <th>Nombre</th>
                                <td><?= htmlspecialchars($pais['nombrePais']) ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">Monedas del País</h5>
                        <?php if ($monedas_result->num_rows > 0): ?>
                            <div class="list-group">
                                <?php while($moneda = $monedas_result->fetch_assoc()): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($moneda['codMoneda']) ?></strong>
                                            - <?= htmlspecialchars($moneda['nombreMoneda']) ?>
                                            <span class="ms-2"><?= htmlspecialchars($moneda['simbolo']) ?></span>
                                        </div>
                                        <?php if($moneda['es_principal']): ?>
                                            <span class="badge bg-success">Principal</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Este país no tiene monedas asignadas</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>