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

// Obtener datos del producto
$sql = "SELECT * FROM productos WHERE id_producto = ? AND activo = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Producto no encontrado';
    header('Location: productos.php');
    exit;
}

$producto = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar Producto</h4>
                <a href="menu.php?page=productos.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <form action="producto-acciones.php" method="POST">
                    <input type="hidden" name="producto_id" value="<?= $producto['id_producto'] ?>">

                    <!-- Fila 1: Nombre y Código -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nombre del Producto</label>
                            <input type="text" name="nombre_producto"
                                value="<?= htmlspecialchars($producto['nombre_producto']) ?>" class="form-control"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Código de Barras</label>
                            <input type="text" name="codigo_barras"
                                value="<?= htmlspecialchars($producto['codigo_barras'] ?? '') ?>" class="form-control">
                        </div>
                    </div>

                    <!-- Fila 2: Descripción -->
                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control"
                            rows="3"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <!-- Fila 3: Categoría y Precio de Costo -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Categoría</label>
                            <select class="form-control" name="id_categoria">
                                <option value="">Seleccionar categoría</option>
                                <?php
                                $cat_sql = "SELECT * FROM categorias WHERE activo = 1";
                                $cat_result = $conn->query($cat_sql);
                                while ($cat = $cat_result->fetch_assoc()) {
                                    $selected = ($cat['id_categoria'] == $producto['id_categoria']) ? 'selected' : '';
                                    echo '<option value="' . $cat['id_categoria'] . '" ' . $selected . '>' . htmlspecialchars($cat['nombre_categoria']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Precio de Costo</label>
                            <input type="number" step="0.01" name="precio_compras"
                                value="<?= number_format($producto['precio_compras'] ?? 0, 2, '.', '') ?>"
                                class="form-control" required>
                        </div>
                    </div>

                    <!-- Fila 4: Precio de Venta y Mostrar en Tienda -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Precio de Venta</label>
                            <input type="number" step="0.01" name="precio_venta"
                                value="<?= $producto['precio_venta'] ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Mostrar en Tienda Virtual</label>
                            <select class="form-control" name="mostrar_en_tienda">
                                <option value="1" <?= ($producto['mostrar_en_tienda'] ?? 1) == 1 ? 'selected' : '' ?>>Sí</option>
                                <option value="0" <?= ($producto['mostrar_en_tienda'] ?? 1) == 0 ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 5: Stock Actual y Stock Mínimo -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Stock Actual</label>
                            <input type="number" name="stock_actual" value="<?= $producto['stock_actual'] ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Stock Mínimo</label>
                            <input type="number" name="stock_minimo" value="<?= $producto['stock_minimo'] ?>"
                                class="form-control">
                        </div>
                    </div>

                    <!-- Botón Actualizar -->
                    <div class="mb-3">
                        <button type="submit" name="update_producto" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>