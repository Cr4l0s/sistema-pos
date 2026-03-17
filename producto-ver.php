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
<html lang="es">

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
                <div>
                    <a href="menu.php?page=producto-editar.php&id=<?= $producto['id_producto'] ?>"
                        class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="menu.php?page=productos.php" class="btn btn-danger">
                        <span class="bi bi-arrow-left"></span> Volver
                    </a>
                </div>
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
                        <label><b>Precio de Costo</b></label>
                        <p class="form-control">$<?= number_format($producto['precio_compras'] ?? 0, 0, ',', '.') ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Precio de Venta</b></label>
                        <p class="form-control">$<?= number_format($producto['precio_venta'], 0, ',', '.') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><b>Mostrar en Tienda Virtual</b></label>
                        <p class="form-control">
                            <?= ($producto['mostrar_en_tienda'] ?? 1) ? '✅ Sí' : '❌ No' ?>
                        </p>
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

                <!-- ===== FECHA DE CREACIÓN CON DETECCIÓN DE ENTORNO ===== -->
                <div class="mb-3">
                    <label><b>Fecha de Creación</b></label>
                    <?php
                    $fecha_bd = $producto['fecha_creacion'];

                    if ($fecha_bd && $fecha_bd != '0000-00-00 00:00:00') {
                        // Detectar si estamos en localhost o en el servidor
                        $es_localhost = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1');
                        
                        if ($es_localhost) {
                            // En LOCALHOST: la BD guarda hora Chile directamente
                            $fecha = new DateTime($fecha_bd);
                        } else {
                            // En SERVIDOR: la BD guarda hora Chicago, hay que convertir a Chile
                            $fecha = new DateTime($fecha_bd, new DateTimeZone('America/Chicago'));
                            $fecha->setTimezone(new DateTimeZone('America/Santiago'));
                        }

                        // Formatear la fecha
                        $fecha_formateada = $fecha->format('d-m-Y, H:i:s');

                        // Calcular hace cuánto tiempo fue
                        $ahora = new DateTime();
                        $diferencia = $ahora->getTimestamp() - $fecha->getTimestamp();

                        if ($diferencia < 0) $diferencia = 0;

                        $hace_texto = '';
                        if ($diferencia < 60) {
                            $hace_texto = 'hace unos segundos';
                        } elseif ($diferencia < 3600) {
                            $minutos = floor($diferencia / 60);
                            $hace_texto = "hace $minutos minuto" . ($minutos != 1 ? 's' : '');
                        } elseif ($diferencia < 86400) {
                            $horas = floor($diferencia / 3600);
                            $hace_texto = "hace $horas hora" . ($horas != 1 ? 's' : '');
                        } else {
                            $dias = floor($diferencia / 86400);
                            $hace_texto = "hace $dias día" . ($dias != 1 ? 's' : '');
                        }
                    } else {
                        $fecha_formateada = 'No disponible';
                        $hace_texto = '';
                    }
                    ?>
                    <p class="form-control" style="font-weight: bold; font-size: 1.1em;">
                        <?= $fecha_formateada ?>
                    </p>
                    <small class="text-muted">
                        Hora Chile (UTC-3)
                        <?php if (!empty($hace_texto) && $fecha_formateada != 'No disponible'): ?>
                            - <?= $hace_texto ?>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>