<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .paginacion {
            margin: 20px 0;
            text-align: center;
        }
        .paginacion a {
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #666;
        }
        .paginacion .actual {
            padding: 5px 10px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
        .paginacion a:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Listado de Productos
                            <a href="menu.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver al Menú</a>                                
                            <a href="producto-crear.php" class="btn btn-primary float-end"><span class="bi bi-plus-circle-fill"></span>&nbsp;Agregar Producto</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código Barras</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>Precio Venta</th>
                                    <th>Stock</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $resultados_por_pagina = 8;
                                    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                    $calculo = ($pagina_actual - 1) * $resultados_por_pagina;
                                    
                                    // Obtener total de registros
                                    $sql_total = "SELECT COUNT(*) as total FROM productos WHERE activo = 1";
                                    $stmt_total = $conn->prepare($sql_total);
                                    $stmt_total->execute();
                                    $resultado_total = $stmt_total->get_result();
                                    $fila_total = $resultado_total->fetch_assoc();
                                    $total_registros = $fila_total['total'];
                                    $stmt_total->close();
                                    
                                    $total_paginas = ceil($total_registros / $resultados_por_pagina);
                                    
                                    // Consulta principal con JOIN y LIMIT
                                    $sql = "SELECT p.*, c.nombre_categoria 
                                            FROM productos p
                                            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                                            WHERE p.activo = 1 
                                            LIMIT ?, ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("ii", $calculo, $resultados_por_pagina);
                                    $stmt->execute();
                                    $productos = $stmt->get_result();
                                    
                                    if($productos->num_rows > 0) {
                                        while($producto = $productos->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $producto['id_producto']; ?></td>
                                    <td><?php echo $producto['codigo_barras'] ?: '—'; ?></td>
                                    <td><?php echo $producto['nombre_producto']; ?></td>
                                    <td><?php echo $producto['nombre_categoria'] ?: '—'; ?></td>
                                    <td>$<?php echo number_format($producto['precio_venta'], 0, ',', '.'); ?></td>
                                    <td><?php echo $producto['stock_actual']; ?></td>
                                    <td>
                                        <a href="producto-ver.php?id=<?php echo $producto['id_producto']; ?>" class="btn btn-secondary btn-sm"><span class="bi bi-eye-fill"></span>&nbsp;Ver</a>
                                        <a href="producto-editar.php?id=<?php echo $producto['id_producto']; ?>" class="btn btn-success btn-sm"><span class="bi bi-pencil-fill"></span>&nbsp;Editar</a>
                                        <form action="producto-acciones.php" method="POST" class="d-inline">
                                            <button onclick="return confirm('¿Confirma la eliminación del producto?')" type="submit" name="borrar_producto" value="<?php echo $producto['id_producto']; ?>" class="btn btn-danger btn-sm">
                                            <span class="bi bi-trash3-fill"></span>&nbsp;Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php 
                                        }
                                        $stmt->close();
                                    } else { 
                                        echo '<tr><td colspan="7" class="text-center">No hay productos registrados</td></tr>';
                                    }    
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    
    <div class="paginacion">
        <?php if($pagina_actual > 1): ?>
            <a href="?pagina=<?php echo ($pagina_actual-1); ?>">Anterior</a>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $total_paginas; $i++): ?>
            <?php if($i == $pagina_actual): ?>
                <span class="actual"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif;?>
        <?php endfor; ?>
        
        <?php if($pagina_actual < $total_paginas): ?>
            <a href="?pagina=<?php echo ($pagina_actual + 1); ?>">Siguiente</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>