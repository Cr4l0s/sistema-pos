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
            $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la ciudad: ' . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'El nombre de la ciudad no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }

    header('Location: menu.php?page=gestionar_ciudades.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=gestionar_ciudades.php');
exit;
?>