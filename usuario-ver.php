<?php
session_start();
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idUsuario'])) {
    $usuario_id = intval($_POST['idUsuario']);
} else {
    header('Location: inicio-usuarios.php');
    exit;
}

$sql = "SELECT * FROM usuarios WHERE idUsuario = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    $_SESSION['mensaje'] = 'Usuario no encontrado';
    header('Location: inicio-usuarios.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Usuario</h4>
                <a href="inicio-usuarios.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><b>Nombres</b></label>
                    <p class="form-control"><?= htmlspecialchars($usuario['nombres']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Apellido Paterno</b></label>
                    <p class="form-control"><?= htmlspecialchars($usuario['ApPaterno']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Apellido Materno</b></label>
                    <p class="form-control"><?= htmlspecialchars($usuario['ApMaterno']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Usuario</b></label>
                    <p class="form-control"><?= htmlspecialchars($usuario['NombreUsuario']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Email</b></label>
                    <p class="form-control"><?= htmlspecialchars($usuario['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Teléfono</b></label>
                    <p class="form-control">
                        <?= htmlspecialchars($usuario['fonofijo'] ?: $usuario['fonocelular1'] ?: '—') ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>