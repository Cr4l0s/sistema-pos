<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPais = intval($_POST['idPais']);
    $nombreRegion = trim($_POST['nombreRegion']);
    $codRegion = trim($_POST['codRegion']);
    $vigente = 1;

    $sql = "INSERT INTO regiones (idPais, nombreRegion, codRegion, vigente) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $idPais, $nombreRegion, $codRegion, $vigente);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Región agregada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al agregar región: ' . $stmt->error;
    }
    $stmt->close();
    header("Location: gestionar_regiones.php");
    exit;
}
?>