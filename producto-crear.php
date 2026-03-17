<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Agregar Producto
                            <a href="menu.php?page=productos.php" class="btn btn-danger float-end">
                                <span class="bi bi-arrow-left"></span>&nbsp;Volver
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="producto-acciones.php" method="POST" onsubmit="return validarFormularioProducto()">
                            <!-- Fila 1: Nombre y Código -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombre_producto" id="nombre_producto" required>
                                    <small class="text-muted">Solo letras, números y espacios</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Código de Barras</label>
                                    <input type="text" class="form-control" name="codigo_barras" id="codigo_barras" maxlength="13">
                                    <small class="text-muted">13 dígitos numéricos</small>
                                </div>
                            </div>

                            <!-- Fila 2: Descripción -->
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"></textarea>
                            </div>

                            <!-- Fila 3: Categoría y Precio de Costo -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Categoría</label>
                                    <select class="form-control" name="id_categoria">
                                        <option value="">Seleccionar categoría</option>
                                        <?php
                                        $categorias = mysqli_query($conn, "SELECT * FROM categorias WHERE activo = 1");
                                        while ($cat = mysqli_fetch_array($categorias)) {
                                            echo '<option value="' . $cat['id_categoria'] . '">' . $cat['nombre_categoria'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Precio de Costo</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_compras" id="precio_compras" value="0.00">
                                </div>
                            </div>

                            <!-- Fila 4: Precio de Venta y Mostrar en Tienda -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Precio de Venta <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="precio_venta" id="precio_venta" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Mostrar en Tienda Virtual</label>
                                    <select class="form-control" name="mostrar_en_tienda">
                                        <option value="1">Sí</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Fila 5: Stock Actual y Stock Mínimo -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Stock Actual</label>
                                    <input type="number" class="form-control" name="stock_actual" id="stock_actual" value="0" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Stock Mínimo</label>
                                    <input type="number" class="form-control" name="stock_minimo" id="stock_minimo" value="0" min="0">
                                </div>
                            </div>

                            <!-- Botón Grabar -->
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
    <script src="validaciones.js"></script>
    <script>
        // Permitir solo números en el campo código de barras
        document.getElementById('codigo_barras').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>

</html>