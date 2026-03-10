<?php
session_start();
require 'db.php';

if (isset($_POST['idCiudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
    
    // Borrado lógico
    $sql = "UPDATE ciudades SET vigente = 0 WHERE idCiudad = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idCiudad);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Ciudad eliminada correctamente.';
        $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la ciudad: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }
    $stmt->close();
} else {
    $_SESSION['mensaje'] = 'ID de ciudad no proporcionado.';
    $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
}

header('Location: menu.php?page=gestionar_ciudades.php');
exit;
?>