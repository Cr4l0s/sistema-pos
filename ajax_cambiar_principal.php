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

// Iniciar transacción
$conn->begin_transaction();

try {
    // Quitar principal de todas las monedas del país
    $reset = $conn->prepare("UPDATE paises_monedas SET es_principal = 0 WHERE idPais = ?");
    $reset->bind_param("i", $idPais);
    $reset->execute();
    $reset->close();
    
    // Establecer nueva principal
    $set = $conn->prepare("UPDATE paises_monedas SET es_principal = 1 WHERE idPais = ? AND idMoneda = ?");
    $set->bind_param("ii", $idPais, $idMoneda);
    $set->execute();
    $set->close();
    
    $conn->commit();
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>