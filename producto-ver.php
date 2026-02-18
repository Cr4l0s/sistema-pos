<?php
require 'db.php';
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
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Ver Producto
                            <a href="productos.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                            
                            $sql = "SELECT p.*, c.nombre_categoria 
                                    FROM productos p
                                    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                                    WHERE p.id_producto = ? AND p.activo = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $p = $result->fetch_assoc();
                                $stmt->close();
                        ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>ID</b></label>
                                <p class="form-control"><?php echo $p['id_producto']; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>Código de Barras</b></label>
                                <p class="form-control"><?php echo $p['codigo_barras'] ?: '—'; ?></p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label><b>Nombre</b></label>
                            <p class="form-control"><?php echo $p['nombre_producto']; ?></p>
                        </div>

                        <div class="mb-3">
                            <label><b>Descripción</b></label>
                            <p class="form-control"><?php echo $p['descripcion'] ?: '—'; ?></p>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>Categoría</b></label>
                                <p class="form-control"><?php echo $p['nombre_categoria'] ?: '—'; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>Precio de Venta</b></label>
                                <p class="form-control">$<?php echo number_format($p['precio_venta'], 0, ',', '.'); ?></p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label><b>Stock Actual</b></label>
                                <p class="form-control"><?php echo $p['stock_actual']; ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label><b>Stock Mínimo</b></label>
                                <p class="form-control"><?php echo $p['stock_minimo']; ?></p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label><b>Fecha de Creación</b></label>
                            <p class="form-control"><?php echo date('d-m-Y H:i:s', strtotime($p['fecha_creacion'])); ?></p>
                        </div>
                        <?php
                            } else {
                                echo '<h5>Producto no encontrado</h5>';
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