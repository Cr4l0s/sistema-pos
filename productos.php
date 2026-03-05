<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require_once 'config.php';
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
                        <h4>Listado de Productos</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
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
                                $resultados_por_pagina = FILASXPAGINA;
                                $pagina_actual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
                                $calculo = ($pagina_actual - 1) * $resultados_por_pagina;

                                // Total de registros
                                $sql_total = "SELECT COUNT(*) as total FROM productos WHERE activo = 1";
                                $stmt_total = $conn->prepare($sql_total);
                                $stmt_total->execute();
                                $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                                $stmt_total->close();
                                $total_paginas = ceil($total_registros / $resultados_por_pagina);

                                // Consulta paginada
                                $sql = "SELECT p.*, c.nombre_categoria 
                                        FROM productos p
                                        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                                        WHERE p.activo = 1 
                                        ORDER BY p.nombre_producto
                                        LIMIT ?, ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ii", $calculo, $resultados_por_pagina);
                                $stmt->execute();
                                $productos = $stmt->get_result();

                                if ($productos->num_rows > 0) {
                                    while ($producto = $productos->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($producto['codigo_barras'] ?: '—') ?></td>
                                            <td><?= htmlspecialchars($producto['nombre_producto']) ?></td>
                                            <td><?= htmlspecialchars($producto['nombre_categoria'] ?: '—') ?></td>
                                            <td>$<?= number_format($producto['precio_venta'], 0, ',', '.') ?></td>
                                            <td><?= $producto['stock_actual'] ?></td>
                                            <td>
                                                <!-- VER -->
                                                <form action="menu.php?page=producto-ver.php" method="POST"
                                                    style="display:inline;">
                                                    <input type="hidden" name="id_producto"
                                                        value="<?= $producto['id_producto'] ?>">
                                                    <button type="submit" class="btn btn-secondary btn-sm">
                                                        <span class="bi bi-eye-fill"></span> Ver
                                                    </button>
                                                </form>
                                                <!-- EDITAR -->
                                                <form action="menu.php?page=producto-editar.php" method="POST"
                                                    style="display:inline;">
                                                    <input type="hidden" name="id_producto"
                                                        value="<?= $producto['id_producto'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <span class="bi bi-pencil-fill"></span> Editar
                                                    </button>
                                                </form>
                                                <!-- ELIMINAR -->
                                                <form action="eliminar_producto.php" method="POST" style="display:inline;">
                                                    <input type="hidden" name="id_producto"
                                                        value="<?= $producto['id_producto'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('¿Eliminar producto?')">
                                                        <span class="bi bi-trash3-fill"></span> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    $stmt->close();
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">No hay productos</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- PAGINACIÓN MEJORADA (con queryString) -->
                        <!-- Antes de la paginación -->
                        <?php
                        $page_param = isset($_GET['page']) ? $_GET['page'] : basename($_SERVER['PHP_SELF']);

                        $params = $_GET;
                        unset($params['pagina']);
                        $queryString = http_build_query($params);

                        if (!isset($params['page']) && $page_param) {
                            $queryString = http_build_query(array_merge($params, ['page' => $page_param]));
                        }
                        ?>

                        <!-- Enlaces de paginación -->
                        <div class="paginacion">

                            <?php if ($pagina_actual > 1): ?>
                                <a href="?<?php echo $queryString; ?>&pagina=<?php echo ($pagina_actual - 1); ?>">
                                    Anterior
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                <?php if ($i == $pagina_actual): ?>
                                    <span class="actual"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="?<?php echo $queryString; ?>&pagina=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($pagina_actual < $total_paginas): ?>
                                <a href="?<?php echo $queryString; ?>&pagina=<?php echo ($pagina_actual + 1); ?>">
                                    Siguiente
                                </a>
                            <?php endif; ?>

                        </div>

                        <!-- BOTÓN AGREGAR DEBAJO DE LA TABLA -->
                        <div class="mt-3 text-center">
                            <a href="producto-crear.php" class="btn btn-primary">
                                <span class="bi bi-plus-circle-fill"></span> Agregar Producto
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>