<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// ACTUALIZAR REGIÓN
if (isset($_POST['update_region'])) {
    $idRegion = intval($_POST['idRegion']);
    $nombreRegion = trim($_POST['nombreRegion']);
    $codRegion = trim($_POST['codRegion']);

    if (!empty($nombreRegion) && !empty($codRegion)) {
        $sql = "UPDATE regiones SET nombreRegion = ?, codRegion = ? WHERE idRegion = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombreRegion, $codRegion, $idRegion);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Región actualizada correctamente.';
            $_SESSION['tipo_mensaje'] = 'success';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la región: ' . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'Todos los campos son requeridos.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }

    header('Location: menu.php?page=gestionar_regiones.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=gestionar_regiones.php');
exit;
?>