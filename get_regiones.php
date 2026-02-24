<?php
// get_regiones.php
require 'db.php';

// Activar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if (!isset($_GET['idPais'])) {
    echo json_encode(['error' => 'No se proporcionó idPais']);
    exit;
}

$idPais = intval($_GET['idPais']);

$sql = "SELECT idRegion, nombreRegion FROM regiones WHERE idPais = ? AND vigente = 1 ORDER BY nombreRegion";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Error al preparar consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $idPais);
$stmt->execute();
$result = $stmt->get_result();
$regiones = [];

while ($row = $result->fetch_assoc()) {
    $regiones[] = $row;
}

$stmt->close();
echo json_encode($regiones);
exit;
?>