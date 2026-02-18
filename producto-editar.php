<?php
session_start();
require 'db.php';
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Producto
                            <a href="productos.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                            
                            $sql = "SELECT * FROM productos WHERE id_producto = ? AND activo = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $p = $result->fetch_assoc();
                                $stmt->close();
                        ?>
                        <form action="producto-acciones.php" method="POST">
                            <input type="hidden" name="producto_id" value="<?php echo $p['id_producto']; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nombre del Producto</label>
                                    <input type="text" name="nombre_producto" value="<?php echo $p['nombre_producto']; ?>" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Código de Barras</label>
                                    <input type="text" name="codigo_barras" value="<?php echo $p['codigo_barras']; ?>" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"><?php echo $p['descripcion']; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Categoría</label>
                                    <select class="form-control" name="id_categoria">
                                        <option value="">Seleccionar categoría</option>
                                        <?php
                                        $cat_sql = "SELECT * FROM categorias WHERE activo = 1";
                                        $cat_result = $conn->query($cat_sql);
                                        while ($cat = $cat_result->fetch_assoc()) {
                                            $selected = ($cat['id_categoria'] == $p['id_categoria']) ? 'selected' : '';
                                            echo '<option value="' . $cat['id_categoria'] . '" ' . $selected . '>' . $cat['nombre_categoria'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Precio de Venta</label>
                                    <input type="number" step="0.01" name="precio_venta" value="<?php echo $p['precio_venta']; ?>" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Stock Actual</label>
                                    <input type="number" name="stock_actual" value="<?php echo $p['stock_actual']; ?>" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Stock Mínimo</label>
                                    <input type="number" name="stock_minimo" value="<?php echo $p['stock_minimo']; ?>" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" name="update_producto" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                        <?php
                            } else {
                                echo "<h5>Producto no encontrado</h5>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>