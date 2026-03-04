<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idPais'])) {
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

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'País no encontrado';
    header('Location: inicio_pais.php');
    exit;
}

$pais = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar País</h4>
                <a href="menu.php?page=inicio_pais.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="acciones-pais.php" method="POST">
                    <input type="hidden" name="idPais" value="<?= $pais['idPais'] ?>">
                    <div class="mb-3">
                        <label>Sigla del País</label>
                        <input type="text" name="siglaPais" value="<?= htmlspecialchars($pais['siglaPais']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Código de la Moneda</label>
                        <input type="text" name="codMoneda" value="<?= htmlspecialchars($pais['codMoneda']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nombre del País</label>
                        <input type="text" name="nombrePais" value="<?= htmlspecialchars($pais['nombrePais']) ?>"
                            class="form-control" required>
                    </div>
                    <button type="submit" name="update_pais" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>