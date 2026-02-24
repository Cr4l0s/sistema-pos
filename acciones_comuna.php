<?php
session_start();
require 'db.php';

if (isset($_POST['update_comuna'])) {
    $idComuna = intval($_POST['idComuna']);
    $nomComuna = trim($_POST['nomComuna']);

    if (!empty($nomComuna)) {
        $sql = "UPDATE comunas SET nomComuna = ? WHERE idComuna = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nomComuna, $idComuna);

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