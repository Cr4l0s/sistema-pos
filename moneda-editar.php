<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST o GET
$idMoneda = isset($_POST['idMoneda']) ? intval($_POST['idMoneda']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

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
    <title>Editar Moneda</title>
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
                    <i class="bi bi-pencil-square"></i> Editar Moneda
                </h4>
                <a href="menu.php?page=inicio_moneda.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="acciones-moneda.php">
                    <input type="hidden" name="update_moneda" value="1">
                    <input type="hidden" name="idMoneda" value="<?= $idMoneda ?>">

                    <div class="mb-3">
                        <label class="fw-bold">Código de Moneda</label>
                        <input type="text" class="form-control" name="codMoneda" autocomplete="off"
                            value="<?= htmlspecialchars($moneda['codMoneda']) ?>"
                            maxlength="10" required>
                        <small class="text-muted">Código de la moneda (ej: USD, EUR, JPY)</small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nombre de la Moneda</label>
                        <input type="text" class="form-control" name="nombreMoneda" autocomplete="off"
                            value="<?= htmlspecialchars($moneda['nombreMoneda']) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Símbolo de la Moneda</label>
                        <input type="text" class="form-control" name="simbolo" autocomplete="off"
                            value="<?= htmlspecialchars($moneda['simbolo']) ?>"
                            maxlength="10">
                    </div>

                    <hr>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-save"></i> Actualizar Moneda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>