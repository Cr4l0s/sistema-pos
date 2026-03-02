<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_producto'])) {
    $id = intval($_POST['id_producto']);
} else {
    header('Location: productos.php');
    exit;
}

$sql = "SELECT p.*, c.nombre_categoria 
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        WHERE p.id_producto = ? AND p.activo = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

if (!$producto) {
    $_SESSION['mensaje'] = 'Producto no encontrado';
    header('Location: productos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ver Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ver Producto</h4>
                <a href="productos.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Código de Barras</b></label>
                        <p class="form-control"><?= htmlspecialchars($producto['codigo_barras'] ?: '—') ?></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label><b>Nombre</b></label>
                    <p class="form-control"><?= htmlspecialchars($producto['nombre_producto']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Descripción</b></label>
                    <p class="form-control"><?= htmlspecialchars($producto['descripcion'] ?: '—') ?></p>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Categoría</b></label>
                        <p class="form-control"><?= htmlspecialchars($producto['nombre_categoria'] ?: '—') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><b>Precio de Venta</b></label>
                        <p class="form-control">$<?= number_format($producto['precio_venta'], 0, ',', '.') ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Stock Actual</b></label>
                        <p class="form-control"><?= $producto['stock_actual'] ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><b>Stock Mínimo</b></label>
                        <p class="form-control"><?= $producto['stock_minimo'] ?></p>
                    </div>
                </div>
                <div class="mb-3">
                    <label><b>Fecha de Creación</b></label>
                    <p class="form-control"><?= date('d-m-Y H:i:s', strtotime($producto['fecha_creacion'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>