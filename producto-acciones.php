<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require 'validaciones.php'; // Incluir validaciones

// Al principio del archivo, después de los requires
date_default_timezone_set('America/Santiago');

// Luego, si insertas fecha manualmente, usa:
$fecha_actual = date('Y-m-d H:i:s');

// ============================================
// CREAR PRODUCTO
// ============================================
if (isset($_POST['create_producto'])) {

    // Obtener y limpiar datos - CORREGIDO: limpiarInput con I mayúscula
    $codigo_barras = !empty(trim($_POST['codigo_barras'])) ? limpiarInput(trim($_POST['codigo_barras'])) : null;
    $nombre = limpiarInput(trim($_POST['nombre_producto']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_compras = !empty($_POST['precio_compras']) ? floatval($_POST['precio_compras']) : 0;
    $precio_venta = !empty($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    $stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
    $stock_minimo = !empty($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $mostrar_en_tienda = isset($_POST['mostrar_en_tienda']) ? intval($_POST['mostrar_en_tienda']) : 1;

    // ===== VALIDACIONES CON EXPRESIONES REGULARES =====
    
    // Validar nombre del producto
    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre del producto contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }
    
    // Validar código de barras (si existe)
    if ($codigo_barras && !validarCodigoBarras($codigo_barras)) {
        $_SESSION['mensaje'] = 'El código de barras debe tener 13 dígitos numéricos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }
    
    // Validar precio de venta
    if (!validarPrecio($_POST['precio_venta']) || $precio_venta <= 0) {
        $_SESSION['mensaje'] = 'El precio de venta no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }
    
    // Validar precio de costo (si existe)
    if ($precio_compras > 0 && !validarPrecio($_POST['precio_compras'])) {
        $_SESSION['mensaje'] = 'El precio de costo no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }
    
    // Validar stock actual
    if (!validarStock($_POST['stock_actual'])) {
        $_SESSION['mensaje'] = 'El stock actual debe ser un número entero positivo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }
    
    // Validar stock mínimo
    if (!validarStock($_POST['stock_minimo'])) {
        $_SESSION['mensaje'] = 'El stock mínimo debe ser un número entero positivo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }
    
    // Validar que el precio de venta sea mayor al de costo
    if ($precio_venta <= $precio_compras) {
        $_SESSION['mensaje'] = 'El precio de venta debe ser mayor al precio de costo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
        exit;
    }

    // ===== INSERTAR EN BASE DE DATOS =====
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
        $id_producto = $stmt->insert_id;
        $_SESSION['mensaje'] = 'Producto creado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
        header("Location: menu.php?page=producto-ver.php&id=$id_producto");
    } else {
        error_log("Error execute CREATE: " . $stmt->error);
        $_SESSION['mensaje'] = 'Error al crear producto';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: producto-crear.php');
    }
    $stmt->close();
    exit;
}

// ============================================
// ACTUALIZAR PRODUCTO
// ============================================
if (isset($_POST['update_producto'])) {
    
    $id = intval($_POST['producto_id']);
    
    // Validar ID
    if (!validarID($id)) {
        $_SESSION['mensaje'] = 'ID de producto no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=productos.php');
        exit;
    }

    // Obtener y limpiar datos
    $codigo_barras = !empty(trim($_POST['codigo_barras'])) ? limpiarInput(trim($_POST['codigo_barras'])) : null;
    $nombre = limpiarInput(trim($_POST['nombre_producto']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;
    $id_categoria = !empty($_POST['id_categoria']) ? intval($_POST['id_categoria']) : null;
    $precio_compras = !empty($_POST['precio_compras']) ? floatval($_POST['precio_compras']) : 0;
    $precio_venta = !empty($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
    $stock_actual = !empty($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
    $stock_minimo = !empty($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 0;
    $mostrar_en_tienda = isset($_POST['mostrar_en_tienda']) ? intval($_POST['mostrar_en_tienda']) : 1;

    // ===== VALIDACIONES =====
    
    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre del producto contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: producto-editar.php?id=$id");
        exit;
    }
    
    if ($codigo_barras && !validarCodigoBarras($codigo_barras)) {
        $_SESSION['mensaje'] = 'El código de barras debe tener 13 dígitos numéricos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: producto-editar.php?id=$id");
        exit;
    }
    
    if (!validarPrecio($_POST['precio_venta']) || $precio_venta <= 0) {
        $_SESSION['mensaje'] = 'El precio de venta no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: producto-editar.php?id=$id");
        exit;
    }
    
    if (!validarStock($_POST['stock_actual'])) {
        $_SESSION['mensaje'] = 'El stock actual debe ser un número entero positivo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: producto-editar.php?id=$id");
        exit;
    }
    
    if ($precio_venta <= $precio_compras) {
        $_SESSION['mensaje'] = 'El precio de venta debe ser mayor al precio de costo';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: producto-editar.php?id=$id");
        exit;
    }

    // ===== ACTUALIZAR EN BASE DE DATOS =====
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
        header("Location: menu.php?page=producto-ver.php&id=$id");
    } else {
        error_log("Error execute UPDATE: " . $stmt->error);
        $_SESSION['mensaje'] = 'Error al actualizar producto';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: producto-editar.php?id=$id");
    }
    $stmt->close();
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=productos.php');
exit;
?>