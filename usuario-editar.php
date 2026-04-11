<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// 🔴 MODIFICADO: Buscar ID en POST (desde usuario-ver.php) o GET
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

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE idUsuario = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Usuario no encontrado';
    header('Location: inicio-usuarios.php');
    exit;
}

$usuario = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar Usuario</h4>
                <a href="menu.php?page=inicio-usuarios.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="acciones-usuario.php" method="POST">
                    <input type="hidden" name="usuario_id" value="<?= $usuario['idUsuario'] ?>">
                    <div class="mb-3">
                        <label>Nombres</label>
                        <input type="text" name="nombres" value="<?= htmlspecialchars($usuario['nombres']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Apellido Paterno</label>
                        <input type="text" name="apPaterno" value="<?= htmlspecialchars($usuario['ApPaterno']) ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Apellido Materno</label>
                        <input type="text" name="apMaterno" value="<?= htmlspecialchars($usuario['ApMaterno']) ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Usuario</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($usuario['NombreUsuario']) ?>"
                            class="form-control" readonly>
                        <small class="text-muted">El nombre de usuario no se puede modificar</small>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>"
                            class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="fonofijo" value="<?= htmlspecialchars($usuario['fonofijo']) ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Celular</label>
                        <input type="text" name="fonocelular1" value="<?= htmlspecialchars($usuario['fonocelular1']) ?>"
                            class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Password (dejar vacío para no cambiar)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <button type="submit" name="update_usuario" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>