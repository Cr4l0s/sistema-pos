<?php
session_start();
require 'db.php';

// CREAR PRODUCTO
if (isset($_POST['create_producto'])) {
    $codigo_barras = mysqli_real_escape_string($conn, trim($_POST['codigo_barras']));
    $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre_producto']));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion']));
    $id_categoria = $_POST['id_categoria'] ? intval($_POST['id_categoria']) : 'NULL';
    $precio_venta = floatval($_POST['precio_venta']);
    $stock_actual = intval($_POST['stock_actual']);
    $stock_minimo = intval($_POST['stock_minimo']);

    $sql = "INSERT INTO productos (codigo_barras, nombre_producto, descripcion, id_categoria, precio_venta, stock_actual, stock_minimo, activo) 
            VALUES ('$codigo_barras', '$nombre', '$descripcion', $id_categoria, $precio_venta, $stock_actual, $stock_minimo, 1)";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = 'Producto creado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al crear producto: ' . mysqli_error($conn);
    }
    header('Location: productos.php');
    exit;
}

// ACTUALIZAR PRODUCTO
if (isset($_POST['update_producto'])) {
    $id = mysqli_real_escape_string($conn, $_POST['producto_id']);
    $codigo_barras = mysqli_real_escape_string($conn, trim($_POST['codigo_barras']));
    $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre_producto']));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion']));
    $id_categoria = $_POST['id_categoria'] ? intval($_POST['id_categoria']) : 'NULL';
    $precio_venta = floatval($_POST['precio_venta']);
    $stock_actual = intval($_POST['stock_actual']);
    $stock_minimo = intval($_POST['stock_minimo']);

    $sql = "UPDATE productos SET 
            codigo_barras = '$codigo_barras',
            nombre_producto = '$nombre',
            descripcion = '$descripcion',
            id_categoria = $id_categoria,
            precio_venta = $precio_venta,
            stock_actual = $stock_actual,
            stock_minimo = $stock_minimo
            WHERE id_producto = '$id'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = 'Producto actualizado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar producto.';
    }
    header('Location: productos.php');
    exit;
}

// ELIMINAR PRODUCTO (borrado lógico)
if (isset($_POST['borrar_producto'])) {
    $id = mysqli_real_escape_string($conn, $_POST['borrar_producto']);
    
    $sql = "UPDATE productos SET activo = 0 WHERE id_producto = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = 'Producto eliminado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar producto.';
    }
    header('Location: productos.php');
    exit;
}
?>