<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// 🔴 NUEVO: Si llega por GET, redirigir por POST para ocultar el ID
if (isset($_GET['id']) && !isset($_POST['idUsuario'])) {
    $id = intval($_GET['id']);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Redirigiendo...</title>
    </head>
    <body>
        <form id="redirectForm" action="menu.php" method="POST">
            <input type="hidden" name="page" value="usuario-ver.php">
            <input type="hidden" name="idUsuario" value="<?= $id ?>">
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
$usuario_id = 0;

if (isset($_POST['idUsuario'])) {
    $usuario_id = intval($_POST['idUsuario']);
} elseif (isset($_GET['idUsuario'])) {
    $usuario_id = intval($_GET['idUsuario']);
} elseif (isset($_GET['id'])) {
    $usuario_id = intval($_GET['id']);
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
        <?php include('mensaje.php'); ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Usuario</h4>
                <div>
                    <!-- 🔴 CORREGIDO: Formulario POST en lugar de enlace -->
                    <form action="menu.php" method="POST" style="display: inline;">
                        <input type="hidden" name="page" value="usuario-editar.php">
                        <input type="hidden" name="idUsuario" value="<?= $usuario_id ?>">
                        <button type="submit" class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </form>
                    <a href="menu.php?page=inicio-usuarios.php" class="btn btn-danger">
                        <span class="bi bi-arrow-left"></span> Volver
                    </a>
                </div>
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