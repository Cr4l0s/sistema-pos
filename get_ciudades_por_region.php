<?php
// get_ciudades_por_region.php
require 'db.php';

if (isset($_GET['idRegion'])) {
    $idRegion = intval($_GET['idRegion']);
    $sql = "SELECT idCiudad, nombreCiudad FROM ciudades WHERE idRegion = ? AND vigente = 1 ORDER BY nombreCiudad";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idRegion);
    $stmt->execute();
    $result = $stmt->get_result();
    $ciudades = [];
    while ($row = $result->fetch_assoc()) {
        $ciudades[] = $row;
    }
    $stmt->close();
    echo json_encode($ciudades);
    exit;
}
?>