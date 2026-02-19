<?php
require 'db.php';

if (isset($_GET['idRegion'])) {
    $idRegion = intval($_GET['idRegion']);
    $stmt = $conn->prepare("SELECT idCiudad, nombreCiudad FROM ciudades WHERE idRegion = ? AND vigente = 1");
    $stmt->bind_param("i", $idRegion);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    $stmt->close();
}
?>