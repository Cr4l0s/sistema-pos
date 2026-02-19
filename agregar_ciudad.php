<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idRegion = intval($_POST['idRegion']);
    $nombreCiudad = trim($_POST['nombreCiudad']);
    $vigente = 1;

    $sql = "INSERT INTO ciudades (idRegion, nombreCiudad, vigente) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $idRegion, $nombreCiudad, $vigente);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Ciudad agregada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al agregar ciudad: ' . $stmt->error;
    }
    $stmt->close();
    header("Location: gestionar_ciudades.php");
    exit;
}
?>