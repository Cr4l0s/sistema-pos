<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Verificar que se recibió el ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idRegion'])) {
    $idRegion = intval($_POST['idRegion']);
} else {
    $_SESSION['mensaje'] = 'ID de región no proporcionado.';
    header('Location: gestionar_regiones.php');
    exit;
}

// Verificar que la región existe y está vigente
$sql_check = "SELECT idRegion FROM regiones WHERE idRegion = ? AND vigente = 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $idRegion);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    $_SESSION['mensaje'] = 'La región no existe o ya fue eliminada.';
    $stmt_check->close();
    header('Location: gestionar_regiones.php');
    exit;
}
$stmt_check->close();

// Borrado lógico
$sql = "UPDATE regiones SET vigente = 0 WHERE idRegion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegion);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = 'Región eliminada correctamente.';
} else {
    $_SESSION['mensaje'] = 'Error al eliminar la región: ' . $stmt->error;
}

$stmt->close();
header('Location: gestionar_regiones.php');
exit;
?>