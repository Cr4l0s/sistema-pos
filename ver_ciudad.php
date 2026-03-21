<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// 🔴 NUEVO: Si llega por GET, redirigir por POST para ocultar el ID
if (isset($_GET['idCiudad']) && !isset($_POST['idCiudad'])) {
    $id = intval($_GET['idCiudad']);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Redirigiendo...</title>
    </head>
    <body>
        <form id="redirectForm" action="menu.php" method="POST">
            <input type="hidden" name="page" value="ver_ciudad.php">
            <input type="hidden" name="idCiudad" value="<?= $id ?>">
        </form>
        <script>
            document.getElementById('redirectForm').submit();
        </script>
    </body>
    </html>
    <?php
    exit;
}

// Recibir ID por POST
$idCiudad = 0;

if (isset($_POST['idCiudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
} elseif (isset($_GET['idCiudad'])) {
    $idCiudad = intval($_GET['idCiudad']);
} else {
    header('Location: gestionar_ciudades.php');
    exit;
}

$sql = "SELECT c.idCiudad, c.nombreCiudad, r.nombreRegion 
        FROM ciudades c
        JOIN regiones r ON c.idRegion = r.idRegion
        WHERE c.idCiudad = ? AND c.vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCiudad);
$stmt->execute();
$result = $stmt->get_result();
$ciudad = $result->fetch_assoc();
$stmt->close();

if (!$ciudad) {
    $_SESSION['mensaje'] = 'Ciudad no encontrada';
    header('Location: gestionar_ciudades.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Ciudad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Ciudad</h4>
                <div>
                    <!-- 🔴 CORREGIDO: Formulario POST en lugar de enlace -->
                    <form action="menu.php" method="POST" style="display: inline;">
                        <input type="hidden" name="page" value="editar_ciudad.php">
                        <input type="hidden" name="idCiudad" value="<?= $idCiudad ?>">
                        <button type="submit" class="btn btn-warning me-2">
                            <span class="bi bi-pencil"></span> Editar
                        </button>
                    </form>
                    <a href="menu.php?page=gestionar_ciudades.php" class="btn btn-danger">
                        <span class="bi bi-arrow-left"></span> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><b>Región</b></label>
                    <p class="form-control"><?= htmlspecialchars($ciudad['nombreRegion']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Ciudad</b></label>
                    <p class="form-control"><?= htmlspecialchars($ciudad['nombreCiudad']) ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>