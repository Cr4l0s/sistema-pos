<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_POST['idPais']) || !isset($_POST['idMoneda'])) {
    echo json_encode(['success' => false, 'error' => 'Faltan parámetros']);
    exit;
}

$idPais = intval($_POST['idPais']);
$idMoneda = intval($_POST['idMoneda']);

// No permitir eliminar la moneda principal
$check = $conn->prepare("SELECT es_principal FROM paises_monedas WHERE idPais = ? AND idMoneda = ?");
$check->bind_param("ii", $idPais, $idMoneda);
$check->execute();
$result = $check->get_result();
$moneda = $result->fetch_assoc();
$check->close();

if (!$moneda) {
    echo json_encode(['success' => false, 'error' => 'La relación no existe']);
    exit;
}

if ($moneda['es_principal'] == 1) {
    echo json_encode(['success' => false, 'error' => 'No se puede eliminar la moneda principal']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM paises_monedas WHERE idPais = ? AND idMoneda = ?");
$stmt->bind_param("ii", $idPais, $idMoneda);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
$stmt->close();
?>