<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// 🔴 MODIFICADO: Buscar ID en POST (desde ver_comuna.php) o GET
$idComuna = 0;

if (isset($_POST['idComuna'])) {
    $idComuna = intval($_POST['idComuna']);
} elseif (isset($_GET['idComuna'])) {
    $idComuna = intval($_GET['idComuna']);
} else {
    header('Location: gestionar_comunas.php');
    exit;
}

// Obtener datos de la comuna
$sql = "SELECT * FROM comunas WHERE idComuna = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idComuna);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Comuna no encontrada';
    header('Location: gestionar_comunas.php');
    exit;
}

$comuna = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar Comuna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar Comuna</h4>
                <a href="menu.php?page=gestionar_comunas.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="acciones_comuna.php" method="POST">
                    <input type="hidden" name="idComuna" value="<?= $comuna['idComuna'] ?>">
                    <div class="mb-3">
                        <label for="nombreComuna" class="form-label">Nombre de la Comuna</label>
                        <input type="text" class="form-control" name="nombreComuna" id="nombreComuna"
                            value="<?= htmlspecialchars($comuna['nombreComuna']) ?>" required>
                    </div>
                    <button type="submit" name="update_comuna" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>