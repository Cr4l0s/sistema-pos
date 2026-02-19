<?php
session_start();
require 'db.php';

if (isset($_POST['idRegion'])) {
    $idRegion = intval($_POST['idRegion']);
    
    // Borrado lógico
    $sql = "UPDATE regiones SET vigente = 0 WHERE idRegion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idRegion);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Región eliminada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la región: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['mensaje'] = 'ID de región no proporcionado.';
}

header('Location: gestionar_regiones.php');
exit;
?>