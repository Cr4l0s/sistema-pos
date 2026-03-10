<?php
echo "<h1>TEST</h1>";
echo "<p>Archivo actual: " . __FILE__ . "</p>";
echo "<p>Directorio: " . __DIR__ . "</p>";

echo "<h3>Archivos PHP en esta carpeta:</h3>";
echo "<ul>";
$archivos = scandir(__DIR__);
foreach ($archivos as $archivo) {
    if (strpos($archivo, '.php') !== false) {
        echo "<li>$archivo</li>";
    }
}
echo "</ul>";
?>