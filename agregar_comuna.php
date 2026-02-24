<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nomComuna'], $_POST['idCiudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
    $nomComuna = trim($_POST['nomComuna']);

    if (!empty($nomComuna)) {
        $sql = "INSERT INTO comunas (idCiudad, nomComuna, vigente) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $idCiudad, $nomComuna);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = 'Comuna agregada correctamente.';
        } else {
            $_SESSION['mensaje'] = 'Error al agregar la comuna: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = 'El nombre de la comuna no puede estar vacío.';
    }
} else {
    $_SESSION['mensaje'] = 'Solicitud inválida.';
}
header('Location: gestionar_comunas.php');
exit;
?>