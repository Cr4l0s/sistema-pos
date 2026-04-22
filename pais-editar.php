<?php
// ACTIVAR VISUALIZACIÓN DE ERRORES (QUITAR EN PRODUCCIÓN)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST o GET
$idPais = isset($_POST['idPais']) ? intval($_POST['idPais']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

if ($idPais == 0) {
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
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .pais-actual-badge {
            background-color: #17a2b8;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Editar País
                    <span class="pais-actual-badge">
                        <?= htmlspecialchars($pais['siglaPais'] ?? '', ENT_QUOTES, 'UTF-8') ?> -
                        <?= htmlspecialchars($pais['nombrePais'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </h4>
                <a href="menu.php?page=inicio_pais.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="acciones-pais.php">
                    <input type="hidden" name="editar_pais" value="1">
                    <input type="hidden" name="idPais" value="<?= $idPais ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold">Sigla del País</label>
                            <input type="text" class="form-control" name="siglaPais" 
                                value="<?= htmlspecialchars($pais['siglaPais'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="3" placeholder="Ej: CL" autocomplete="off" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">Nombre del País</label>
                            <input type="text" class="form-control" name="nombrePais" 
                                value="<?= htmlspecialchars($pais['nombrePais'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ej: Chile" autocomplete="off" required>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-save"></i> Actualizar País
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>