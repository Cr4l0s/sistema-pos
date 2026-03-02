<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// ACTUALIZAR CIUDAD
if (isset($_POST['update_ciudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
    $nombreCiudad = trim($_POST['nombreCiudad']);

    if (!empty($nombreCiudad)) {
        $sql = "UPDATE ciudades SET nombreCiudad = ? WHERE idCiudad = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreCiudad, $idCiudad);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Ciudad actualizada correctamente.';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la ciudad: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'El nombre de la ciudad no puede estar vacío.';
    }

    header('Location: gestionar_ciudades.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: gestionar_ciudades.php');
exit;
?>