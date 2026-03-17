<?php
header('Content-Type: text/html; charset=utf-8');

// Mostrar qué archivo db.php se está incluyendo
echo "<h2>Diagnóstico de archivos</h2>";
echo "Buscando db.php en: " . __DIR__ . "/db.php<br>";
echo "¿Existe? " . (file_exists(__DIR__ . "/db.php") ? "SÍ" : "NO") . "<br>";
echo "Ruta real: " . realpath(__DIR__ . "/db.php") . "<br>";
echo "<hr>";

require 'db.php';

echo "<h2>Prueba de Codificación</h2>";

// 1. Verificar conexión
echo "<h3>1. Charset de conexión:</h3>";
echo "Valor reportado: " . $conn->character_set_name() . "<br>";

// Forzar charset directamente en esta prueba
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4'");
echo "Después de forzar: " . $conn->character_set_name() . "<br>";

// 2. Mostrar una moneda problemática
$sql = "SELECT codMoneda, nombreMoneda, simbolo FROM monedas WHERE codMoneda IN ('MKD', 'NIO', 'STN')";
$result = $conn->query($sql);

echo "<h3>2. Datos desde la BD:</h3>";
echo "<table border='1'>";
echo "<tr><th>Código</th><th>Nombre</th><th>Símbolo</th><th>HTML Entities</th><th>Hex</th></tr>";
while ($row = $result->fetch_assoc()) {
    $hex = bin2hex($row['simbolo']);
    echo "<tr>";
    echo "<td>" . $row['codMoneda'] . "</td>";
    echo "<td>" . $row['nombreMoneda'] . "</td>";
    echo "<td>" . $row['simbolo'] . "</td>";
    echo "<td>" . htmlentities($row['simbolo'], ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td>" . $hex . "</td>";
    echo "</tr>";
}
echo "</table>";
?>