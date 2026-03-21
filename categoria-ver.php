<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST o GET
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    header('Location: categorias.php');
    exit;
}

$sql = "SELECT * FROM categorias WHERE id_categoria = ? AND activo = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$categoria = $result->fetch_assoc();
$stmt->close();

if (!$categoria) {
    $_SESSION['mensaje'] = 'Categoría no encontrada';
    header('Location: categorias.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Categoría</h4>
                <div>
                    <form action="menu.php" method="POST" style="display: inline;">
                        <input type="hidden" name="page" value="categoria-editar.php">
                        <input type="hidden" name="id_categoria" value="<?= $categoria['id_categoria'] ?>">
                        <button type="submit" class="btn btn-warning me-2">
                            <span class="bi bi-pencil"></span> Editar
                        </button>
                    </form>
                    <a href="menu.php?page=categorias.php" class="btn btn-danger">
                        <span class="bi bi-arrow-left"></span> Volver
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label><b>Nombre</b></label>
                    <p class="form-control"><?= htmlspecialchars($categoria['nombre_categoria']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Descripción</b></label>
                    <p class="form-control"><?= htmlspecialchars($categoria['descripcion'] ?: '—') ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>