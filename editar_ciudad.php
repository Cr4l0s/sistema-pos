<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST (NO por GET)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idCiudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
} else {
    header('Location: gestionar_ciudades.php');
    exit;
}

// Obtener datos de la ciudad
$sql = "SELECT * FROM ciudades WHERE idCiudad = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCiudad);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Ciudad no encontrada';
    header('Location: gestionar_ciudades.php');
    exit;
}

$ciudad = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar Ciudad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar Ciudad</h4>
                <a href="gestionar_ciudades.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="acciones_ciudad.php" method="POST">
                    <input type="hidden" name="idCiudad" value="<?= $ciudad['idCiudad'] ?>">

                    <div class="mb-3">
                        <label for="nombreCiudad" class="form-label">Nombre de la Ciudad</label>
                        <input type="text" class="form-control" name="nombreCiudad" id="nombreCiudad"
                            value="<?= htmlspecialchars($ciudad['nombreCiudad']) ?>" required>
                    </div>
                    <button type="submit" name="update_ciudad" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>