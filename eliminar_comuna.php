<?php
session_start();
require 'db.php';

if (isset($_POST['idComuna'])) {
    $idComuna = intval($_POST['idComuna']);

    $sql = "UPDATE comunas SET vigente = 0 WHERE idComuna = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idComuna);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Comuna eliminada correctamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al eliminar la comuna: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['mensaje'] = 'ID de comuna no proporcionado.';
}
header('Location: gestionar_comunas.php');
exit;
?>