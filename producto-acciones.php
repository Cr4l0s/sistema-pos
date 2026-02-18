<?php
session_start();
require 'db.php';

// CREAR PRODUCTO
if (isset($_POST['create_producto'])) {
    $codigo_barras = trim($_POST['codigo_barras']) ?: null;
    $nombre = trim($_POST['nombre_producto']);
    $descripcion = trim($_POST['descripcion']) ?: null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_venta = floatval($_POST['precio_venta']);
    $stock_actual = intval($_POST['stock_actual']);
    $stock_minimo = intval($_POST['stock_minimo']);

    $sql = "INSERT INTO productos (codigo_barras, nombre_producto, descripcion, id_categoria, precio_venta, stock_actual, stock_minimo, activo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiddi", $codigo_barras, $nombre, $descripcion, $id_categoria, $precio_venta, $stock_actual, $stock_minimo);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto creado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al crear producto: ' . $conn->error;
    }
    $stmt->close();
    header('Location: productos.php');
    exit;
}

// ACTUALIZAR PRODUCTO
if (isset($_POST['update_producto'])) {
    $id = intval($_POST['producto_id']);
    $codigo_barras = trim($_POST['codigo_barras']) ?: null;
    $nombre = trim($_POST['nombre_producto']);
    $descripcion = trim($_POST['descripcion']) ?: null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_venta = floatval($_POST['precio_venta']);
    $stock_actual = intval($_POST['stock_actual']);
    $stock_minimo = intval($_POST['stock_minimo']);

    $sql = "UPDATE productos SET 
            codigo_barras = ?,
            nombre_producto = ?,
            descripcion = ?,
            id_categoria = ?,
            precio_venta = ?,
            stock_actual = ?,
            stock_minimo = ?
            WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiddii", $codigo_barras, $nombre, $descripcion, $id_categoria, $precio_venta, $stock_actual, $stock_minimo, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto actualizado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar producto.';
    }
    $stmt->close();
    header('Location: productos.php');
    exit;
}

// ELIMINAR PRODUCTO (borrado lógico)
if (isset($_POST['borrar_producto'])) {
    $id = intval($_POST['borrar_producto']);

    $sql = "UPDATE productos SET activo = 0 WHERE id_producto = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Producto eliminado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar producto.';
    }
    $stmt->close();
    header('Location: productos.php');
    exit;
}
?>