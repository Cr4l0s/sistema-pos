<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
?>
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
                                <input type="text" class="form-control" name="nombre_producto" id="nombre_producto"
                                    autocomplete="off" required>
                                <small class="text-muted">Solo letras, números y espacios</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Código de Barras:</label>
                                <?php
                                $input_name = 'codigo_barras_' . uniqid();
                                ?>
                                <input type="text" name="<?= $input_name ?>" class="form-control" autocomplete="off">
                                <small class="text-muted">Código alfanumérico, máximo 100 caracteres</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Código de Producto:</label>
                                <?php
                                $input_codigo = 'codigo_producto_' . uniqid();
                                ?>
                                <input type="text" name="<?= $input_codigo ?>" class="form-control" autocomplete="off">
                                <small class="text-muted">Código interno del producto (opcional)</small>
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
                                            echo '<option value="' . $cat['id_categoria'] . '">' . htmlspecialchars($cat['nombre_categoria']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Precio de Costo</label>
                                    <input type="number" step="0.01" class="form-control" name="precio_compras"
                                        id="precio_compras" value="0.00">
                                </div>
                            </div>

                            <!-- Fila 4: Precio de Venta y Mostrar en Tienda -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Precio de Venta <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" name="precio_venta"
                                        id="precio_venta" required>
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
                                    <input type="number" class="form-control" name="stock_actual" id="stock_actual"
                                        value="0" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Stock Mínimo</label>
                                    <input type="number" class="form-control" name="stock_minimo" id="stock_minimo"
                                        value="0" min="0">
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

<script src="validaciones.js"></script>
<script>
    function validarFormularioProducto() {
        const nombre = document.getElementById('nombre_producto')?.value;
        const codigo = document.getElementById('codigo_barras')?.value;
        const precioVenta = document.getElementById('precio_venta')?.value;
        const stockActual = document.getElementById('stock_actual')?.value;
        const stockMinimo = document.getElementById('stock_minimo')?.value;

        if (!Validaciones.required(nombre)) {
            alert('El nombre del producto es obligatorio');
            return false;
        }

        // 🔴 MODIFICADO: Validación de código de barras simplificada
        if (codigo && codigo.length > 100) {
            alert('El código de barras es demasiado largo (máximo 100 caracteres)');
            return false;
        }

        if (!Validaciones.required(precioVenta) || parseFloat(precioVenta) <= 0) {
            alert('El precio de venta debe ser mayor a 0');
            return false;
        }

        if (stockActual && !Validaciones.validarStock(stockActual)) {
            alert('El stock actual debe ser un número entero positivo');
            return false;
        }

        if (stockMinimo && !Validaciones.validarStock(stockMinimo)) {
            alert('El stock mínimo debe ser un número entero positivo');
            return false;
        }

        return true;
    }
</script>