<?php
// Obtener la tabla del POST
$tabla_html = $_POST['tabla'] ?? '';

if (empty($tabla_html)) {
    die('No se recibió ninguna tabla');
}

// Guardar original
file_put_contents(__DIR__ . '/debug_pdf_original.html', $tabla_html);

// Procesar con DOMDocument
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="UTF-8">' . $tabla_html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
libxml_clear_errors();

$xpath = new DOMXPath($dom);

// Encontrar todas las celdas de la columna Tienda (penúltima columna)
$filas = $xpath->query("//tr");
foreach ($filas as $fila) {
    $celdas = $fila->getElementsByTagName('td');
    if ($celdas->length >= 7) { // Mínimo 7 columnas (Tienda es la 7ª)
        $celda_tienda = $celdas->item(6); // Índice 6 = 7ª columna
        if ($celda_tienda) {
            $contenido = $celda_tienda->textContent;
            if (trim($contenido) === '✅') {
                $celda_tienda->nodeValue = 'Sí';
            } elseif (trim($contenido) === '❌') {
                $celda_tienda->nodeValue = 'No';
            }
        }
    }
}

// Eliminar la columna de Acciones (última columna)
foreach ($filas as $fila) {
    $celdas = $fila->getElementsByTagName('td');
    if ($celdas->length > 0) {
        // Eliminar la última celda
        $ultima_celda = $celdas->item($celdas->length - 1);
        if ($ultima_celda) {
            $ultima_celda->parentNode->removeChild($ultima_celda);
        }
    }
}

// Eliminar el encabezado de Acciones
$encabezados = $xpath->query("//th");
foreach ($encabezados as $th) {
    if (trim($th->textContent) === 'Acciones') {
        $th->parentNode->removeChild($th);
        break;
    }
}

// Obtener el HTML procesado
$tabla_html_procesado = $dom->saveHTML();

// Guardar procesado
file_put_contents(__DIR__ . '/debug_pdf_procesado.html', $tabla_html_procesado);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Exportar PDF</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; } 
        table { border-collapse: collapse; width: 100%; } 
        th, td { border: 1px solid #ddd; padding: 8px; } 
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <?php echo $tabla_html_procesado; ?>
    
    <script>
        setTimeout(function() { window.print(); }, 500);
        window.onafterprint = function() { window.close(); };
    </script>
</body>
</html>