<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// ACTUALIZAR COMUNA
if (isset($_POST['update_comuna'])) {
    $idComuna = intval($_POST['idComuna']);
    $nombreComuna = trim($_POST['nombreComuna']);

    if (!empty($nombreComuna)) {
        $sql = "UPDATE comunas SET nombreComuna = ? WHERE idComuna = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreComuna, $idComuna);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Comuna actualizada correctamente.';
            $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la comuna: ' . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'El nombre de la comuna no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }

    header('Location: menu.php?page=gestionar_comunas.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=gestionar_comunas.php');
exit;
?>