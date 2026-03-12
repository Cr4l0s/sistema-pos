<?php
// Obtener la tabla del POST
$tabla_html = $_POST['tabla'] ?? '';

if (empty($tabla_html)) {
    exit;
}

// Eliminar la columna de Acciones (última columna) del HTML
// 1. Eliminar el último <th> del thead
$tabla_html = preg_replace('/<th[^>]*>Acciones<\/th>\s*<\/tr>/', '</tr>', $tabla_html);

// 2. Eliminar el último <td> de cada fila del tbody
$tabla_html = preg_replace_callback(
    '/<tr>(.*?)<\/tr>/s',
    function ($matches) {
        // Dividir la fila en celdas
        preg_match_all('/<td[^>]*>.*?<\/td>/s', $matches[1], $celdas);
        if (count($celdas[0]) > 0) {
            // Quitar la última celda (Acciones)
            array_pop($celdas[0]);
            // Reconstruir la fila sin la última columna
            return '<tr>' . implode('', $celdas[0]) . '</tr>';
        }
        return $matches[0];
    },
    $tabla_html
);

// Reemplazar íconos de la columna Tienda por texto (opcional)
$tabla_html = str_replace(['✅', '❌'], ['Sí', 'No'], $tabla_html);

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