<?php
session_start();
require 'db.php';

if (isset($_POST['idCiudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
    
    // =============================================
    // Obtener nombre de la ciudad ANTES de eliminarla
    // =============================================
    $sql_nombre = "SELECT nombreCiudad FROM ciudades WHERE idCiudad = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idCiudad);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $ciudad = $result_nombre->fetch_assoc();
    $nombre_ciudad = $ciudad ? $ciudad['nombreCiudad'] : 'desconocido';
    $stmt_nombre->close();
    
    // =============================================
    // REGLA 4: Verificar si la ciudad tiene comunas activas
    // =============================================
    $sql_check_comunas = "SELECT COUNT(*) as total FROM comunas WHERE idCiudad = ? AND vigente = 1";
    $stmt_check = $conn->prepare($sql_check_comunas);
    $stmt_check->bind_param("i", $idCiudad);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();
    
    if ($row['total'] > 0) {
        $_SESSION['mensaje'] = "No se puede eliminar la ciudad '$nombre_ciudad' porque tiene {$row['total']} comuna(s) activa(s).";
        $_SESSION['tipo_mensaje'] = 'warning';
        $stmt_check->close();
        $conn->close();
        header('Location: menu.php?page=gestionar_ciudades.php');
        exit;
    }
    $stmt_check->close();
    
    // Borrado lógico
    $sql = "UPDATE ciudades SET vigente = 0 WHERE idCiudad = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idCiudad);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Ciudad '$nombre_ciudad' eliminada correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "Error al eliminar la ciudad '$nombre_ciudad': " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
} else {
    $_SESSION['mensaje'] = 'ID de ciudad no proporcionado.';
    $_SESSION['tipo_mensaje'] = 'danger';
}

$conn->close();
header('Location: menu.php?page=gestionar_ciudades.php');
exit;
?>