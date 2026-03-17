<?php
echo "<h2>Prueba de Zona Horaria</h2>";

// Hora del servidor
echo "<h3>Hora del servidor:</h3>";
echo "Zona horaria PHP: " . date_default_timezone_get() . "<br>";
echo "Hora actual PHP: " . date('Y-m-d H:i:s') . "<br>";
echo "Hora UTC (gmdate): " . gmdate('Y-m-d H:i:s') . "<br>";

// Timestamp actual
$timestamp = time();
echo "Timestamp actual: " . $timestamp . "<br>";

// Probar con una fecha específica de la BD (la del producto 33)
$fecha_bd = "2026-03-17 16:57:41";
echo "<h3>Probando con fecha: $fecha_bd</h3>";

// Opción 1: Asumiendo que la BD guarda UTC
$fecha_utc = new DateTime($fecha_bd, new DateTimeZone('UTC'));
echo "Como UTC: " . $fecha_utc->format('Y-m-d H:i:s') . "<br>";

$fecha_utc->setTimezone(new DateTimeZone('America/Santiago'));
echo "Convertida a Chile: " . $fecha_utc->format('Y-m-d H:i:s') . "<br>";

// Opción 2: Asumiendo que la BD guarda hora local del servidor
$fecha_local = new DateTime($fecha_bd);
echo "Como local del servidor: " . $fecha_local->format('Y-m-d H:i:s') . "<br>";

// Comparación con hora actual
$ahora = new DateTime('now', new DateTimeZone('America/Santiago'));
echo "<h3>Hora actual en Chile:</h3>";
echo $ahora->format('Y-m-d H:i:s') . "<br>";
?>