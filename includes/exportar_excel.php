<?php
// Obtener la tabla del POST
$tabla_html = $_POST['tabla'] ?? '';

if (empty($tabla_html)) {
    exit;
}

// Función para limpiar y formatear el HTML
function limpiarTablaParaExcel($html) {
    // Eliminar la columna de Acciones (última columna)
    // 1. Eliminar el último <th> del thead
    $html = preg_replace('/<th[^>]*>.*?<\/th>\s*<\/tr>/', '</tr>', $html);
    
    // 2. Eliminar el último <td> de cada fila del tbody
    $html = preg_replace_callback(
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
        $html
    );
    
    // Reemplazar iconos por texto
    $html = str_replace(['✅', '❌', '🔍', '📝', '🗑️'], ['Sí', 'No', '', '', ''], $html);
    
    // Eliminar botones y formularios
    $html = preg_replace('/<form.*?<\/form>/s', '', $html);
    $html = preg_replace('/<button.*?<\/button>/s', '', $html);
    $html = preg_replace('/<i.*?<\/i>/s', '', $html);
    
    // Limpiar atributos de las celdas (dejar solo el contenido)
    $html = preg_replace('/<td[^>]*>/', '<td>', $html);
    $html = preg_replace('/<th[^>]*>/', '<th>', $html);
    
    return $html;
}

// Limpiar la tabla
$tabla_limpia = limpiarTablaParaExcel($tabla_html);

// Configurar headers para Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=exportacion_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Generar el archivo Excel
echo '<html>';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<style>';
echo 'td { border: 1px solid #000; padding: 4px; }';
echo 'th { background-color: #f2f2f2; font-weight: bold; border: 1px solid #000; padding: 4px; }';
echo 'table { border-collapse: collapse; width: 100%; }';
echo '</style>';
echo '</head>';
echo '<body>';
echo '<h2>Listado de Productos</h2>';
echo $tabla_limpia;
echo '</body>';
echo '</html>';
?>