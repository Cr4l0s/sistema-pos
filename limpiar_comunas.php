<?php
session_start();
require 'db.php';

// 1. Eliminar duplicados, mantener el ID más bajo
$sql_duplicados = "
    SELECT idCiudad, nomComuna, MIN(idComuna) as min_id
    FROM comunas
    GROUP BY idCiudad, nomComuna
    HAVING COUNT(*) > 1
";
$result = $conn->query($sql_duplicados);

while ($row = $result->fetch_assoc()) {
    $idCiudad = $row['idCiudad'];
    $nomComuna = $row['nomComuna'];
    $min_id = $row['min_id'];
    
    // Eliminar los duplicados (excepto el más bajo)
    $sql_delete = "DELETE FROM comunas WHERE idCiudad = ? AND nomComuna = ? AND idComuna > ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("isi", $idCiudad, $nomComuna, $min_id);
    $stmt->execute();
    $stmt->close();
}

// 2. Asegurar que todos los registros activos tengan vigente = 1
$sql_activar = "UPDATE comunas SET vigente = 1 WHERE vigente = 0";
$conn->query($sql_activar);

// 3. Opcional: Reindexar IDs (¡CUIDADO! Esto cambia relaciones)
// $sql_reindex = "SET @count = 0;
//                 UPDATE comunas SET idComuna = @count:= @count + 1;
//                 ALTER TABLE comunas AUTO_INCREMENT = 1;";
// $conn->multi_query($sql_reindex);

$_SESSION['mensaje'] = 'Limpieza completada.';
header('Location: gestionar_comunas.php');
exit;
?>