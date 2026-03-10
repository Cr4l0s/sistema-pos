<?php
session_start();
require 'db.php';

// CREAR PAÍS
if (isset($_POST['create_pais'])) {
    $siglaPais = trim($_POST['siglaPais']);
    $codMoneda = trim($_POST['codMoneda']);
    $nombrePais = trim($_POST['nombrePais']);

    $sql = "INSERT INTO paises (siglaPais, codMoneda, nombrePais, vigente) VALUES (?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $siglaPais, $codMoneda, $nombrePais);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'País creado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';  // ✅ VERDE
    } else {
        $_SESSION['mensaje'] = 'Error al crear país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';   // ✅ ROJO
    }
    $stmt->close();
    
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}

// ACTUALIZAR PAÍS
if (isset($_POST['update_pais'])) {
    $idPais = intval($_POST['idPais']);
    $siglaPais = trim($_POST['siglaPais']);
    $codMoneda = trim($_POST['codMoneda']);
    $nombrePais = trim($_POST['nombrePais']);

    $sql = "UPDATE paises SET siglaPais = ?, codMoneda = ?, nombrePais = ? WHERE idPais = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $siglaPais, $codMoneda, $nombrePais, $idPais);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'País actualizado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';  // ✅ VERDE
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';   // ✅ ROJO
    }
    $stmt->close();
    
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}

// ELIMINAR PAÍS
if (isset($_POST['borrar_pais'])) {
    $idPais = intval($_POST['borrar_pais']);

    $sql = "UPDATE paises SET vigente = 0 WHERE idPais = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPais);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'País eliminado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';  // ✅ VERDE
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';   // ✅ ROJO
    }
    $stmt->close();
    
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}
?>