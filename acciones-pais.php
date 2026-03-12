<?php
session_start();
require 'db.php';

// Función para obtener el símbolo (select o custom)
function getSimboloMoneda() {
    if ($_POST['simbolo_moneda_select'] === 'OTHER') {
        return trim($_POST['simbolo_moneda_custom']);
    }
    return $_POST['simbolo_moneda_select'];
}

// CREAR PAÍS
if (isset($_POST['create_pais'])) {
    $siglaPais = trim($_POST['siglaPais']);
    $codMoneda = trim($_POST['codMoneda']);
    $simbolo_moneda = getSimboloMoneda();
    $nombrePais = trim($_POST['nombrePais']);

    $sql = "INSERT INTO paises (siglaPais, codMoneda, simbolo_moneda, nombrePais, vigente) 
            VALUES (?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $siglaPais, $codMoneda, $simbolo_moneda, $nombrePais);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'País creado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al crear país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
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
    $simbolo_moneda = trim($_POST['simbolo_moneda']);  // ← DEBE EXISTIR
    $nombrePais = trim($_POST['nombrePais']);

    $sql = "UPDATE paises SET 
            siglaPais = ?, 
            codMoneda = ?, 
            simbolo_moneda = ?, 
            nombrePais = ? 
            WHERE idPais = ? AND vigente = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $siglaPais, $codMoneda, $simbolo_moneda, $nombrePais, $idPais);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'País actualizado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
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
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}
?>