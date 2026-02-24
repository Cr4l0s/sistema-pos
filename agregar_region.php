<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPais = $_POST['idPais'];
    $nombreRegion = $_POST['nombreRegion'];
    $codRegion = $_POST['codRegion'];
    $vigente = 1;

    $sql = "INSERT INTO regiones (idPais, nombreRegion, codRegion, vigente) VALUES ('$idPais', '$nombreRegion', '$codRegion', '$vigente')";

    if ($conn->query($sql) === TRUE) {
       // Mensaje de éxito
        echo "<script>alert('Nueva región agregada correctamente.');</script>";
    } else {
        // Mensaje de error
        echo "<script>alert('Error al agregar la región: " . $conn->error . "');</script>";
    }
	header("Location: gestionar_regiones.php");
    $conn->close();
}
?>
