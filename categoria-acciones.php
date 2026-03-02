<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// CREAR CATEGORÍA
if (isset($_POST['create_categoria'])) {
    $nombre = trim($_POST['nombre_categoria']);
    $descripcion = trim($_POST['descripcion']);

    $sql = "INSERT INTO categorias (nombre_categoria, descripcion, activo) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $descripcion);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Categoría creada exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al crear la categoría: ' . $stmt->error;
    }
    $stmt->close();
    header('Location: categorias.php');
    exit;
}

// ACTUALIZAR CATEGORÍA
if (isset($_POST['update_categoria'])) {
    $id = intval($_POST['categoria_id']);
    $nombre = trim($_POST['nombre_categoria']);
    $descripcion = trim($_POST['descripcion']);

    $sql = "UPDATE categorias SET nombre_categoria = ?, descripcion = ? WHERE id_categoria = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Categoría actualizada exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar la categoría.';
    }
    $stmt->close();
    header('Location: categorias.php');
    exit;
}

// ELIMINAR CATEGORÍA (borrado lógico)
if (isset($_POST['borrar_categoria'])) {
    $id = intval($_POST['id_categoria']);

    $sql = "UPDATE categorias SET activo = 0 WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Categoría eliminada exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la categoría.';
    }
    $stmt->close();
    header('Location: categorias.php');
    exit;
}
?>