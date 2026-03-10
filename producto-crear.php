<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
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
                        <h4>Agregar Producto
                            <!-- ✅ CORREGIDO: Botón Volver a través de menu.php -->
                            <a href="menu.php?page=productos.php" class="btn btn-danger float-end">
                                <span class="bi bi-arrow-left"></span>&nbsp;Volver
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="producto-acciones.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nombre del Producto</label>
                                    <input type="text" class="form-control" name="nombre_producto" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Código de Barras</label>
                                    <input type="text" class="form-control" name="codigo_barras">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Categoría</label>
                                    <select class="form-control" name="id_categoria">
                                        <option value="">Seleccionar categoría</option>
                                        <?php
                                        $categorias = mysqli_query($conn, "SELECT * FROM categorias WHERE activo = 1");
                                        while($cat = mysqli_fetch_array($categorias)) {
                                            echo '<option value="' . $cat['id_categoria'] . '">' . $cat['nombre_categoria'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Precio de Venta</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_venta" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Stock Actual</label>
                                    <input type="number" class="form-control" name="stock_actual" value="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Stock Mínimo</label>
                                    <input type="number" class="form-control" name="stock_minimo" value="0">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <button type="submit" name="create_producto" class="btn btn-primary">Grabar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>