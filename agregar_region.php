<?php
require_once 'db.php';
require_once 'mensajes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['idPais']) ||
        empty($_POST['nombreRegion']) ||
        empty($_POST['codRegion'])
    ) {
        $_SESSION['mensaje'] = 'Error: Todos los campos son requeridos.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=gestionar_regiones.php");
        exit();
    }

    $idPais = (int) $_POST['idPais'];
    $nombreRegion = trim($_POST['nombreRegion']);
    $codRegion = trim($_POST['codRegion']);
    $vigente = 1;

    $stmt = $conn->prepare(
        "INSERT INTO regiones (idPais, nombreRegion, codRegion, vigente) 
         VALUES (?, ?, ?, ?)"
    );

    if ($stmt === false) {
        $_SESSION['mensaje'] = 'Error al preparar la consulta: ' . $conn->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=gestionar_regiones.php");
        exit();
    }

    $stmt->bind_param("issi", $idPais, $nombreRegion, $codRegion, $vigente);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Región agregada correctamente.';
        $_SESSION['tipo_mensaje'] = 'success';
        header("Location: menu.php?page=gestionar_regiones.php");
    } else {
        $_SESSION['mensaje'] = 'Error al agregar la región: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=gestionar_regiones.php");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>