<?php
// Obtener la tabla del POST
$tabla_html = $_POST['tabla'] ?? '';

// Si no hay tabla, salir
if (empty($tabla_html)) {
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Exportar PDF</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px;
            margin: 0;
        } 
        table { 
            border-collapse: collapse; 
            width: 100%; 
        } 
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left;
        } 
        th { 
            background-color: #f2f2f2; 
        }
    </style>
</head>
<body>
    <?php echo $tabla_html; ?>
    
    <script>
        // Auto-imprimir
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 100);
        };
        
        // Después de imprimir, cerrar esta ventana/iframe
        window.onafterprint = function() {
            window.close();
        };
        
        // Si no se imprime en 10 segundos, cerrar igual
        setTimeout(function() {
            window.close();
        }, 10000);
    </script>
</body>
</html>