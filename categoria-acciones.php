<?php
session_start();
require 'db.php';

// CREAR CATEGORÍA
if (isset($_POST['create_categoria'])) {
    $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre_categoria']));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion']));
    
    $sql = "INSERT INTO categorias (nombre_categoria, descripcion, activo) VALUES ('$nombre', '$descripcion', 1)";
    
    if(mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = 'Categoría creada exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al crear la categoría: ' . mysqli_error($conn);
    }
    header('Location: categorias.php');
    exit;
}

// ACTUALIZAR CATEGORÍA
if (isset($_POST['update_categoria'])) {
    $id = mysqli_real_escape_string($conn, $_POST['categoria_id']);
    $nombre = mysqli_real_escape_string($conn, trim($_POST['nombre_categoria']));
    $descripcion = mysqli_real_escape_string($conn, trim($_POST['descripcion']));
    
    $sql = "UPDATE categorias SET nombre_categoria = '$nombre', descripcion = '$descripcion' WHERE id_categoria = '$id'";
    
    if(mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = 'Categoría actualizada exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar la categoría.';
    }
    header('Location: categorias.php');
    exit;
}

// ELIMINAR CATEGORÍA (borrado lógico)
if (isset($_POST['borrar_categoria'])) {
    $id = mysqli_real_escape_string($conn, $_POST['borrar_categoria']);
    
    $sql = "UPDATE categorias SET activo = 0 WHERE id_categoria = '$id'";
    
    if(mysqli_query($conn, $sql)) {
        $_SESSION['mensaje'] = 'Categoría eliminada exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la categoría.';
    }
    header('Location: categorias.php');
    exit;
}
?>