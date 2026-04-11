<?php
session_start();
require 'db.php';

if (isset($_POST['idComuna'])) {
    $idComuna = intval($_POST['idComuna']);

    // 🔴 NUEVO: Obtener nombre de la comuna antes de eliminarla
    $sql_nombre = "SELECT nombreComuna FROM comunas WHERE idComuna = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idComuna);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $comuna = $result_nombre->fetch_assoc();
    $nombre_comuna = $comuna ? $comuna['nombreComuna'] : 'desconocido';
    $stmt_nombre->close();

    $sql = "UPDATE comunas SET vigente = 0 WHERE idComuna = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idComuna);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Comuna '$nombre_comuna' eliminada correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "Error al eliminar la comuna '$nombre_comuna': " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
} else {
    $_SESSION['mensaje'] = 'ID de comuna no proporcionado.';
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: menu.php?page=gestionar_comunas.php');
exit;
?>