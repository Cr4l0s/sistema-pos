<?php
session_start();
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
} else {
    header('Location: categorias.php');
    exit;
}

// Obtener datos de la categoría
$sql = "SELECT * FROM categorias WHERE id_categoria = ? AND activo = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Categoría no encontrada';
    header('Location: categorias.php');
    exit;
}

$categoria = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar Categoría</h4>
                <a href="categorias.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="categoria-acciones.php" method="POST">
                    <input type="hidden" name="categoria_id" value="<?= $categoria['id_categoria'] ?>">
                    <div class="mb-3">
                        <label>Nombre de la Categoría</label>
                        <input type="text" name="nombre_categoria"
                            value="<?= htmlspecialchars($categoria['nombre_categoria']) ?>" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control"
                            rows="3"><?= htmlspecialchars($categoria['descripcion']) ?></textarea>
                    </div>
                    <button type="submit" name="update_categoria" class="btn btn-primary">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>