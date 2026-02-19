<?php
require 'db.php';

if (isset($_GET['idCiudad'])) {
    $idCiudad = intval($_GET['idCiudad']);
    $stmt = $conn->prepare("SELECT idComuna, nomComuna FROM comunas WHERE idCiudad = ? AND vigente = 1");
    $stmt->bind_param("i", $idCiudad);
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