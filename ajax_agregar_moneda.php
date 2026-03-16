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

// Verificar si la relación ya existe
$check = $conn->prepare("SELECT 1 FROM paises_monedas WHERE idPais = ? AND idMoneda = ?");
$check->bind_param("ii", $idPais, $idMoneda);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if ($exists) {
    echo json_encode(['success' => false, 'error' => 'La moneda ya está asociada al país']);
    exit;
}

// Verificar si es la primera moneda (será principal)
$primera = $conn->prepare("SELECT COUNT(*) as total FROM paises_monedas WHERE idPais = ?");
$primera->bind_param("i", $idPais);
$primera->execute();
$total = $primera->get_result()->fetch_assoc()['total'];
$primera->close();

$es_principal = ($total == 0) ? 1 : 0;

$stmt = $conn->prepare("INSERT INTO paises_monedas (idPais, idMoneda, es_principal) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $idPais, $idMoneda, $es_principal);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
$stmt->close();
?>