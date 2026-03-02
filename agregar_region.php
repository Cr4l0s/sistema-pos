<?php
require_once 'db.php';
require_once 'mensajes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['idPais']) ||
        empty($_POST['nombreRegion']) ||
        empty($_POST['codRegion'])
    ) {
        header("Location: gestionar_regiones.php?status=empty");
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
        $error = urlencode($conn->error);
        header("Location: gestionar_regiones.php?status=error&msg=$error");
        exit();
    }

    $stmt->bind_param("issi", $idPais, $nombreRegion, $codRegion, $vigente);

    if ($stmt->execute()) {
        setMensaje("Región agregada correctamente", "success");
        header("Location: gestionar_regiones.php?status=ok");
    } else {
        $error = urlencode($stmt->error);
        setMensaje("Error al agregar la región", "error");
        header("Location: gestionar_regiones.php?status=error&msg=$error");
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>

