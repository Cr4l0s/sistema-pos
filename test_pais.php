<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba de PHP</h1>";
echo "PHP está funcionando correctamente.<br>";
echo "Versión de PHP: " . phpversion() . "<br>";

// Probar conexión a BD
require 'db.php';
echo "Conexión a BD: OK<br>";

// Probar consulta simple
$result = $conn->query("SELECT COUNT(*) as total FROM paises");
$row = $result->fetch_assoc();
echo "Total de países: " . $row['total'] . "<br>";