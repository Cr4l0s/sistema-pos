<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require 'validaciones.php';

// ============================================
// CREAR MONEDA
// ============================================
if (isset($_POST['create_moneda'])) {
    $codMoneda = strtoupper(limpiarInput(trim($_POST['codMoneda'])));
    $nombreMoneda = limpiarInput(trim($_POST['nombreMoneda']));
    $simbolo = !empty($_POST['simbolo']) ? limpiarInput(trim($_POST['simbolo'])) : '$';

    if (empty($codMoneda) || empty($nombreMoneda)) {
        $_SESSION['mensaje'] = 'Código y nombre de moneda son requeridos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=moneda-crear.php');
        exit;
    }

    $sql = "INSERT INTO monedas (codMoneda, nombreMoneda, simbolo, vigente) VALUES (?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $codMoneda, $nombreMoneda, $simbolo);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Moneda '$nombreMoneda' creada exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        header('Location: menu.php?page=inicio_moneda.php');
        exit;
    } else {
        $_SESSION['mensaje'] = 'Error al crear moneda: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=moneda-crear.php');
        exit;
    }
}

// ============================================
// ACTUALIZAR MONEDA
// ============================================
if (isset($_POST['update_moneda'])) {
    $idMoneda = intval($_POST['idMoneda']);
    $codMoneda = strtoupper(limpiarInput(trim($_POST['codMoneda'])));
    $nombreMoneda = limpiarInput(trim($_POST['nombreMoneda']));
    $simbolo = !empty($_POST['simbolo']) ? limpiarInput(trim($_POST['simbolo'])) : '$';

    if (empty($codMoneda) || empty($nombreMoneda)) {
        $_SESSION['mensaje'] = 'Código y nombre de moneda son requeridos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=moneda-editar.php&id=$idMoneda");
        exit;
    }

    $sql = "UPDATE monedas SET codMoneda = ?, nombreMoneda = ?, simbolo = ? WHERE idMoneda = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $codMoneda, $nombreMoneda, $simbolo, $idMoneda);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Moneda '$nombreMoneda' actualizada correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar moneda: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    header('Location: menu.php?page=inicio_moneda.php');
    exit;
}

// ============================================
// ELIMINAR MONEDA
// ============================================
if (isset($_POST['borrar_moneda'])) {
    $idMoneda = intval($_POST['borrar_moneda']);

    // Obtener nombre de la moneda antes de eliminarla
    $sql_nombre = "SELECT nombreMoneda FROM monedas WHERE idMoneda = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idMoneda);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $moneda = $result_nombre->fetch_assoc();
    $nombreMoneda = $moneda ? $moneda['nombreMoneda'] : 'desconocido';
    $stmt_nombre->close();

    $sql = "UPDATE monedas SET vigente = 0 WHERE idMoneda = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idMoneda);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Moneda '$nombreMoneda' eliminada exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar moneda: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    header('Location: menu.php?page=inicio_moneda.php');
    exit;
}