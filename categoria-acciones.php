<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require 'validaciones.php';

// ============================================
// CREAR CATEGORÍA
// ============================================
if (isset($_POST['create_categoria'])) {
    $nombre = limpiarInput(trim($_POST['nombre_categoria']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;
    $url_imagen = !empty(trim($_POST['url_imagen'])) ? sanitizarURL(trim($_POST['url_imagen'])) : null;

    // ===== VALIDACIONES =====
    if (!validarNombreProducto($nombre)) { // Reutilizamos validación de nombres
        $_SESSION['mensaje'] = 'El nombre de la categoría contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: categoria-crear.php');
        exit;
    }
    
    if ($url_imagen && !validarURLImagen($url_imagen)) {
        $_SESSION['mensaje'] = 'La URL de la imagen no es válida. Formatos permitidos: JPG, PNG, GIF, WEBP, BMP, SVG';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: categoria-crear.php');
        exit;
    }

    $sql = "INSERT INTO categorias (nombre_categoria, descripcion, url_imagen, activo) VALUES (?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $descripcion, $url_imagen);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Categoría creada exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al crear la categoría: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    
    header('Location: menu.php?page=categorias.php');
    exit;
}

// ============================================
// ACTUALIZAR CATEGORÍA
// ============================================
if (isset($_POST['update_categoria'])) {
    $id = intval($_POST['categoria_id']);
    
    if (!validarID($id)) {
        $_SESSION['mensaje'] = 'ID de categoría no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=categorias.php');
        exit;
    }
    
    $nombre = limpiarInput(trim($_POST['nombre_categoria']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;
    $url_imagen = !empty(trim($_POST['url_imagen'])) ? sanitizarURL(trim($_POST['url_imagen'])) : null;

    // ===== VALIDACIONES =====
    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre de la categoría contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: categoria-editar.php?id=$id");
        exit;
    }
    
    if ($url_imagen && !validarURLImagen($url_imagen)) {
        $_SESSION['mensaje'] = 'La URL de la imagen no es válida. Formatos permitidos: JPG, PNG, GIF, WEBP, BMP, SVG';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: categoria-editar.php?id=$id");
        exit;
    }

    $sql = "UPDATE categorias SET nombre_categoria = ?, descripcion = ?, url_imagen = ? WHERE id_categoria = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $descripcion, $url_imagen, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Categoría actualizada exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar la categoría.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    
    header('Location: menu.php?page=categorias.php');
    exit;
}

// ============================================
// ELIMINAR CATEGORÍA
// ============================================
if (isset($_POST['borrar_categoria'])) {
    $id = intval($_POST['id_categoria']);
    
    if (!validarID($id)) {
        $_SESSION['mensaje'] = 'ID de categoría no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=categorias.php');
        exit;
    }

    $sql = "UPDATE categorias SET activo = 0 WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Categoría eliminada exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la categoría.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    
    header('Location: menu.php?page=categorias.php');
    exit;
}
?>