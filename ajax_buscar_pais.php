<?php
require 'db.php';

$code = $_GET['code'] ?? '';

if (empty($code)) {
    echo json_encode(['id' => null]);
    exit;
}

$stmt = $conn->prepare("SELECT idPais FROM paises WHERE siglaPais = ? AND vigente = 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['id' => $row['idPais']]);
} else {
    echo json_encode(['id' => null]);
}
?>