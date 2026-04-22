<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require 'validaciones.php';

date_default_timezone_set('America/Santiago');
$fecha_actual = date('Y-m-d H:i:s');
$idUsuario = $_SESSION['usuario_id'] ?? 1;

// Función para obtener el código de barras desde un nombre dinámico
function obtenerCodigoBarras() {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'codigo_barras_') === 0) {
            return !empty(trim($value)) ? limpiarInput(trim($value)) : null;
        }
    }
    // Si no se encuentra, usar el nombre tradicional
    return isset($_POST['codigo_barras']) ? limpiarInput(trim($_POST['codigo_barras'])) : null;
}
// ============================================
// CREAR PRODUCTO
// ============================================
if (isset($_POST['create_producto'])) {

    $codigo_barras = obtenerCodigoBarras();
    $nombre = limpiarInput(trim($_POST['nombre_producto']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_compras = !empty($_POST['precio_compras']) ? floatval($_POST['precio_compras']) : 0;
    $precio_venta = !empty($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    $stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
    $stock_minimo = !empty($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $mostrar_en_tienda = isset($_POST['mostrar_en_tienda']) ? intval($_POST['mostrar_en_tienda']) : 1;

    // Validaciones
    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre del producto contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    // 🔴 MODIFICADO: Código de barras - validación eliminada o simplificada
    // Solo validar que no tenga caracteres prohibidos si se desea
    if ($codigo_barras && strlen($codigo_barras) > 100) {
        $_SESSION['mensaje'] = 'El código de barras es demasiado largo (máximo 100 caracteres)';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    if (!validarPrecio($_POST['precio_venta']) || $precio_venta <= 0) {
        $_SESSION['mensaje'] = 'El precio de venta no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    if ($precio_compras > 0 && !validarPrecio($_POST['precio_compras'])) {
        $_SESSION['mensaje'] = 'El precio de costo no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    if (!validarStock($_POST['stock_actual'])) {
        $_SESSION['mensaje'] = 'El stock actual debe ser un número entero positivo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    if (!validarStock($_POST['stock_minimo'])) {
        $_SESSION['mensaje'] = 'El stock mínimo debe ser un número entero positivo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    if ($precio_venta <= $precio_compras) {
        $_SESSION['mensaje'] = 'El precio de venta debe ser mayor al precio de costo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=producto-crear.php');
        exit;
    }

    // Insertar
// En CREATE PRODUCTO
    $sql = "INSERT INTO productos (codigo_barras, nombre_producto, id_categoria, precio_compras, precio_venta, stock_actual, stock_minimo, mostrar_en_tienda, activo, idUsuario) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiddiiii", $codigo_barras, $nombre, $id_categoria, $precio_compras, $precio_venta, $stock_actual, $stock_minimo, $mostrar_en_tienda, $idUsuario);

    if (!$stmt) {
        error_log("Error prepare CREATE: " . $conn->error);
        $_SESSION['mensaje'] = 'Error al preparar la consulta';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=productos.php');
        exit;
    }

    if ($stmt->execute()) {
        $id_producto = $stmt->insert_id;
        $_SESSION['mensaje'] = "Producto '$nombre' creado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Redirigiendo...</title>
        </head>

        <body>
            <form id="redirectForm" action="menu.php" method="POST">
                <input type="hidden" name="page" value="producto-ver.php">
                <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>
        </body>

        </html>
        <?php
        exit;
    } else {
        if ($stmt->errno == 1062) {
            if (strpos($stmt->error, 'codigo_barras') !== false) {
                $_SESSION['mensaje'] = "El código de barras '$codigo_barras' ya está registrado en otro producto.";
            } else {
                $_SESSION['mensaje'] = "Ya existe un producto con estos datos.";
            }
            $_SESSION['tipo_mensaje'] = 'danger';
            error_log("Error duplicado CREATE: " . $stmt->error);
            $stmt->close();
            header('Location: menu.php?page=producto-crear.php');
            exit;
        } else {
            error_log("Error execute CREATE: " . $stmt->error);
            $_SESSION['mensaje'] = 'Error al crear producto: ' . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';
            $stmt->close();
            header('Location: menu.php?page=producto-crear.php');
            exit;
        }
    }
}

// ============================================
// ACTUALIZAR PRODUCTO
// ============================================
if (isset($_POST['update_producto'])) {

    $id = intval($_POST['producto_id']);

    if (!validarID($id)) {
        $_SESSION['mensaje'] = 'ID de producto no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=productos.php');
        exit;
    }

    $sql_nombre = "SELECT nombre_producto FROM productos WHERE id_producto = ? AND activo = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $id);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $producto_actual = $result_nombre->fetch_assoc();
    $nombre_original = $producto_actual ? $producto_actual['nombre_producto'] : 'desconocido';
    $stmt_nombre->close();

    $codigo_barras = obtenerCodigoBarras();
    $nombre = limpiarInput(trim($_POST['nombre_producto']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_compras = !empty($_POST['precio_compras']) ? floatval($_POST['precio_compras']) : 0;
    $precio_venta = !empty($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    $stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
    $stock_minimo = !empty($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $mostrar_en_tienda = isset($_POST['mostrar_en_tienda']) ? intval($_POST['mostrar_en_tienda']) : 1;

    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre del producto contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=producto-editar.php&id=$id");
        exit;
    }

    // 🔴 MODIFICADO: Validación de código de barras simplificada
    if ($codigo_barras && strlen($codigo_barras) > 100) {
        $_SESSION['mensaje'] = 'El código de barras es demasiado largo (máximo 100 caracteres)';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=producto-editar.php&id=$id");
        exit;
    }

    if (!validarPrecio($_POST['precio_venta']) || $precio_venta <= 0) {
        $_SESSION['mensaje'] = 'El precio de venta no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=producto-editar.php&id=$id");
        exit;
    }

    if (!validarStock($_POST['stock_actual'])) {
        $_SESSION['mensaje'] = 'El stock actual debe ser un número entero positivo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=producto-editar.php&id=$id");
        exit;
    }

    if ($precio_venta <= $precio_compras) {
        $_SESSION['mensaje'] = 'El precio de venta debe ser mayor al precio de costo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=producto-editar.php&id=$id");
        exit;
    }

    $sql = "UPDATE productos SET 
            codigo_barras = ?,
            nombre_producto = ?,
            descripcion = ?,
            id_categoria = ?,
            precio_compras = ?,
            precio_venta = ?,
            stock_actual = ?,
            stock_minimo = ?,
            mostrar_en_tienda = ?
            WHERE id_producto = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error prepare UPDATE: " . $conn->error);
        $_SESSION['mensaje'] = 'Error al preparar la consulta';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=productos.php');
        exit;
    }

    $stmt->bind_param(
        "sssiddiiii",
        $codigo_barras,
        $nombre,
        $descripcion,
        $id_categoria,
        $precio_compras,
        $precio_venta,
        $stock_actual,
        $stock_minimo,
        $mostrar_en_tienda,
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Producto '$nombre' actualizado correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Redirigiendo...</title>
        </head>

        <body>
            <form id="redirectForm" action="menu.php" method="POST">
                <input type="hidden" name="page" value="producto-ver.php">
                <input type="hidden" name="id_producto" value="<?= $id ?>">
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>
        </body>

        </html>
        <?php
        exit;
    } else {
        error_log("Error execute UPDATE: " . $stmt->error);
        $_SESSION['mensaje'] = "Error al actualizar producto '$nombre_original': " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header("Location: menu.php?page=producto-editar.php&id=$id");
        exit;
    }
}

header('Location: menu.php?page=productos.php');
exit;
?>