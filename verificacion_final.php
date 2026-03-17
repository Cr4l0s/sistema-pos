<?php
header('Content-Type: text/html; charset=utf-8');
require 'db.php';

// Forzar charset nuevamente por si acaso
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4'");

echo "<h2>VERIFICACIÓN FINAL</h2>";

// Probar una consulta
$sql = "SELECT codMoneda, nombreMoneda, simbolo FROM monedas LIMIT 10";
$result = $conn->query($sql);

echo "<table border='1'>";
echo "<tr><th>Código</th><th>Nombre</th><th>Símbolo</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['codMoneda'] . "</td>";
    echo "<td>" . $row['nombreMoneda'] . "</td>";
    echo "<td>" . $row['simbolo'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p>Charset de conexión actual: " . $conn->character_set_name() . "</p>";
?>