<?php
session_start();
require 'db.php';

if (isset($_POST['update_comuna'])) {
    $idComuna = intval($_POST['idComuna']);
    $nombreComuna = trim($_POST['nombreComuna']); // ✅ CAMBIADO

    if (!empty($nombreComuna)) {
        $sql = "UPDATE comunas SET nombreComuna = ? WHERE idComuna = ? AND vigente = 1"; // ✅ CAMBIADO
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreComuna, $idComuna);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Comuna actualizada correctamente.';
        } else {
            $_SESSION['mensaje'] = 'Error al actualizar la comuna: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'El nombre de la comuna no puede estar vacío.';
    }
    header('Location: gestionar_comunas.php');
    exit;
} else {
    header('Location: gestionar_comunas.php');
    exit;
}
?>