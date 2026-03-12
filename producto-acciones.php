<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// CREAR PRODUCTO
if (isset($_POST['create_producto'])) {
    $codigo_barras = !empty(trim($_POST['codigo_barras'])) ? trim($_POST['codigo_barras']) : null;
    $nombre = trim($_POST['nombre_producto']);
    $descripcion = !empty(trim($_POST['descripcion'])) ? trim($_POST['descripcion']) : null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_compras = !empty($_POST['precio_compras']) ? floatval($_POST['precio_compras']) : 0;
    $precio_venta = !empty($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    $stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
    $stock_minimo = !empty($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $mostrar_en_tienda = isset($_POST['mostrar_en_tienda']) ? intval($_POST['mostrar_en_tienda']) : 1;

    $sql = "INSERT INTO productos (codigo_barras, nombre_producto, descripcion, id_categoria, 
            precio_compras, precio_venta, stock_actual, stock_minimo, mostrar_en_tienda, activo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Error prepare CREATE: " . $conn->error);
        $_SESSION['mensaje'] = 'Error al preparar la consulta';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=productos.php');
        exit;
    }

    $stmt->bind_param(
        "sssiddiii",
        $codigo_barras,
        $nombre,
        $descripcion,
        $id_categoria,
        $precio_compras,
        $precio_venta,
        $stock_actual,
        $stock_minimo,
        $mostrar_en_tienda
    );

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto creado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        error_log("Error execute CREATE: " . $stmt->error);
        $_SESSION['mensaje'] = 'Error al crear producto';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();

    header('Location: menu.php?page=productos.php');
    exit;
}

// ACTUALIZAR PRODUCTO
if (isset($_POST['update_producto'])) {
    $id = intval($_POST['producto_id']);
    $codigo_barras = !empty(trim($_POST['codigo_barras'])) ? trim($_POST['codigo_barras']) : null;
    $nombre = trim($_POST['nombre_producto']);
    $descripcion = !empty(trim($_POST['descripcion'])) ? trim($_POST['descripcion']) : null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_compras = !empty($_POST['precio_compras']) ? floatval($_POST['precio_compras']) : 0;
    $precio_venta = !empty($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    $stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
    $stock_minimo = !empty($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $mostrar_en_tienda = isset($_POST['mostrar_en_tienda']) ? intval($_POST['mostrar_en_tienda']) : 1;

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
        $_SESSION['mensaje'] = 'Producto actualizado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        error_log("Error execute UPDATE: " . $stmt->error);
        $_SESSION['mensaje'] = 'Error al actualizar producto';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();

    header('Location: menu.php?page=productos.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=productos.php');
exit;
?>