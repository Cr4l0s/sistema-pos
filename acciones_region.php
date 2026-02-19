<?php
session_start();
require 'db.php';

if (isset($_POST['update_region'])) {
    $idRegion = intval($_POST['idRegion']);
    $nombreRegion = trim($_POST['nombreRegion']);
    $codRegion = trim($_POST['codRegion']);
    
    $sql = "UPDATE regiones SET nombreRegion = ?, codRegion = ? WHERE idRegion = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombreRegion, $codRegion, $idRegion);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Región actualizada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar la región: ' . $stmt->error;
    }
    $stmt->close();
    header('Location: gestionar_regiones.php');
    exit;
}
?>