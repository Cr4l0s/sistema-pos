<?php
session_start();
require 'db.php';

// CREAR EMPRESA
if (isset($_POST['create_empresa'])) {
    $rut = trim($_POST['rut']);
    $nombre = trim($_POST['nombreEmpresa']);
    $direccion = trim($_POST['direccion']);
    $idComuna = intval($_POST['idComuna']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);

    $sql = "INSERT INTO empresas (rut, nombreEmpresa, direccion, idComuna, telefono, email, vigente)
            VALUES (?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiss", $rut, $nombre, $direccion, $idComuna, $telefono, $email);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Empresa creada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al crear empresa: ' . $stmt->error;
    }
    $stmt->close();
    header('Location: inicio-empresas.php');
    exit;
}

// ACTUALIZAR EMPRESA
if (isset($_POST['update_empresa'])) {
    $id = intval($_POST['idEmpresa']);
    $rut = trim($_POST['rut']);
    $nombre = trim($_POST['nombreEmpresa']);
    $direccion = trim($_POST['direccion']);
    $idComuna = intval($_POST['idComuna']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);

    $sql = "UPDATE empresas SET 
            rut = ?, 
            nombreEmpresa = ?, 
            direccion = ?, 
            idComuna = ?, 
            telefono = ?, 
            email = ? 
            WHERE idEmpresa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $rut, $nombre, $direccion, $idComuna, $telefono, $email, $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Empresa actualizada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar empresa: ' . $stmt->error;
    }
    $stmt->close();
    header('Location: inicio-empresas.php');
    exit;
}

// ELIMINAR EMPRESA (borrado lógico)
if (isset($_POST['borrar_empresa'])) {
    $id = intval($_POST['borrar_empresa']);
    
    $sql = "UPDATE empresas SET vigente = 0 WHERE idEmpresa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Empresa eliminada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar empresa: ' . $stmt->error;
    }
    $stmt->close();
    header('Location: inicio-empresas.php');
    exit;
}
?>