<?php
// Obtener la tabla del POST
$tabla_html = $_POST['tabla'] ?? '';

if (empty($tabla_html)) {
    exit;
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=exportacion_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo '<html>';
echo '<head><meta charset="UTF-8"></head>';
echo '<body>';
echo $tabla_html;
echo '</body>';
echo '</html>';
?>