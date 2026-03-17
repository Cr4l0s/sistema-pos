<?php
require 'validaciones.php';

echo "<h2>🧪 PRUEBAS DE VALIDACIONES DE URLs</h2>";

// ============================================
// 1. PRUEBA validarURL()
// ============================================
echo "<h3>1. validarURL() - URLs genéricas</h3>";
$urls = [
    'https://www.ejemplo.com' => 'VÁLIDA',
    'http://ejemplo.com' => 'VÁLIDA',
    'ftp://ejemplo.com/archivo.zip' => 'VÁLIDA',
    'https://ejemplo.com/producto/123?param=1' => 'VÁLIDA',
    'www.ejemplo.com' => 'NO VÁLIDA (falta protocolo)',
    'https://ejemplo' => 'NO VÁLIDA (dominio incompleto)',
    'javascript:alert("xss")' => 'NO VÁLIDA (maliciosa)',
    'https://ejemplo .com' => 'NO VÁLIDA (espacio)'
];

foreach ($urls as $url => $esperado) {
    $resultado = validarURL($url) ? '✅ VÁLIDA' : '❌ NO VÁLIDA';
    echo "URL: $url <br>";
    echo "Resultado: $resultado (Esperado: $esperado)<br><br>";
}

// ============================================
// 2. PRUEBA validarURLImagen()
// ============================================
echo "<h3>2. validarURLImagen() - URLs de imágenes</h3>";
$imagenes = [
    'https://ejemplo.com/foto.jpg' => 'VÁLIDA',
    'https://ejemplo.com/foto.jpeg' => 'VÁLIDA',
    'https://ejemplo.com/foto.png' => 'VÁLIDA',
    'https://ejemplo.com/foto.gif' => 'VÁLIDA',
    'https://ejemplo.com/foto.webp' => 'VÁLIDA',
    'https://ejemplo.com/foto.bmp' => 'VÁLIDA',
    'https://ejemplo.com/foto.svg' => 'VÁLIDA',
    'https://ejemplo.com/foto.jpg?width=800' => 'VÁLIDA (con parámetros)',
    'https://ejemplo.com/foto' => 'NO VÁLIDA (sin extensión)',
    'https://ejemplo.com/foto.txt' => 'NO VÁLIDA (extensión no permitida)',
    'https://ejemplo.com/foto.JPG' => 'VÁLIDA (mayúsculas)'
];

foreach ($imagenes as $url => $esperado) {
    $resultado = validarURLImagen($url) ? '✅ VÁLIDA' : '❌ NO VÁLIDA';
    echo "URL: $url <br>";
    echo "Resultado: $resultado (Esperado: $esperado)<br><br>";
}

// ============================================
// 3. PRUEBA validarURLVideo()
// ============================================
echo "<h3>3. validarURLVideo() - URLs de videos</h3>";
$videos = [
    'https://ejemplo.com/video.mp4' => 'VÁLIDA',
    'https://ejemplo.com/video.webm' => 'VÁLIDA',
    'https://ejemplo.com/video.ogg' => 'VÁLIDA',
    'https://ejemplo.com/video.mov' => 'VÁLIDA',
    'https://ejemplo.com/video.avi' => 'VÁLIDA',
    'https://ejemplo.com/video.wmv' => 'VÁLIDA',
    'https://ejemplo.com/video.mp4?calidad=hd' => 'VÁLIDA (con parámetros)',
    'https://ejemplo.com/video.txt' => 'NO VÁLIDA'
];

foreach ($videos as $url => $esperado) {
    $resultado = validarURLVideo($url) ? '✅ VÁLIDA' : '❌ NO VÁLIDA';
    echo "URL: $url <br>";
    echo "Resultado: $resultado (Esperado: $esperado)<br><br>";
}

// ============================================
// 4. PRUEBA validarURLYouTube()
// ============================================
echo "<h3>4. validarURLYouTube() - URLs de YouTube</h3>";
$youtubes = [
    'https://www.youtube.com/watch?v=dQw4w9WgXcQ' => 'VÁLIDA',
    'https://youtu.be/dQw4w9WgXcQ' => 'VÁLIDA',
    'https://youtube.com/watch?v=dQw4w9WgXcQ' => 'VÁLIDA',
    'youtu.be/dQw4w9WgXcQ' => 'VÁLIDA (sin protocolo)',
    'https://www.youtube.com/watch?v=123' => 'NO VÁLIDA (ID muy corto)',
    'https://www.youtube.com/embed/dQw4w9WgXcQ' => 'NO VÁLIDA (formato embed)',
    'https://vimeo.com/123456' => 'NO VÁLIDA'
];

foreach ($youtubes as $url => $esperado) {
    $resultado = validarURLYouTube($url) ? '✅ VÁLIDA' : '❌ NO VÁLIDA';
    echo "URL: $url <br>";
    echo "Resultado: $resultado (Esperado: $esperado)<br><br>";
}

// ============================================
// 5. PRUEBA validarURLSegura()
// ============================================
echo "<h3>5. validarURLSegura() - URLs HTTPS</h3>";
$seguras = [
    'https://ejemplo.com' => 'VÁLIDA',
    'https://ejemplo.com/producto' => 'VÁLIDA',
    'http://ejemplo.com' => 'NO VÁLIDA (no es HTTPS)',
    'ftp://ejemplo.com' => 'NO VÁLIDA'
];

foreach ($seguras as $url => $esperado) {
    $resultado = validarURLSegura($url) ? '✅ VÁLIDA' : '❌ NO VÁLIDA';
    echo "URL: $url <br>";
    echo "Resultado: $resultado (Esperado: $esperado)<br><br>";
}

// ============================================
// 6. PRUEBA extraerDominio()
// ============================================
echo "<h3>6. extraerDominio() - Extraer dominio</h3>";
$dominios = [
    'https://ejemplo.com/producto/123' => 'ejemplo.com',
    'http://subdominio.ejemplo.org' => 'subdominio.ejemplo.org',
    'ftp://archivos.ejemplo.net' => 'archivos.ejemplo.net',
    'www.ejemplo.com' => 'false (sin protocolo)'
];

foreach ($dominios as $url => $esperado) {
    $resultado = extraerDominio($url);
    echo "URL: $url <br>";
    echo "Resultado: " . ($resultado ? $resultado : 'false') . " (Esperado: $esperado)<br><br>";
}

// ============================================
// 7. PRUEBA con datos reales de tu BD (opcional)
// ============================================
echo "<h3>7. PRUEBA con datos de categorías (si existen)</h3>";
require 'db.php';
$result = $conn->query("SELECT url_imagen FROM categorias WHERE url_imagen IS NOT NULL LIMIT 5");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $url = $row['url_imagen'];
        $valida = validarURLImagen($url) ? '✅' : '❌';
        echo "$valida - $url <br>";
    }
} else {
    echo "No hay URLs de imágenes en categorías para probar.<br>";
}
?>