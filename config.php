<?php
// config.php - Configuración principal del sistema

// Evitar que se ejecute más de una vez
if (defined('CONFIG_LOADED')) {
    return;
}
define('CONFIG_LOADED', true);

// ===== CONFIGURACIÓN DE APLICACIÓN =====
define('APP_NOMBRE', 'Sistema de Ventas en Línea');
define('APP_EMPRESA', 'Socopar SpA');
define('APP_VERSION', '1.0');
define('APP_ANO', date('Y'));
define('APP_DEVELOPER', 'Carlos Olivares Castillo');
define('APP_EMAIL', 'soporte@socopar.cl');
define('TITULO_POS', 'Socopar - Sistema de Ventas en Línea');

// ===== CONFIGURACIÓN DE BASE DE DATOS =====
define('DB_HOST', 'localhost');
define('DB_USER', 'practica_global');
define('DB_PASS', 'Global_2026');
define('DB_NAME', 'practica_sventas_desa');

// ===== COLORES DEL SIDEBAR =====
define('SIDEBAR_BG', '#111827');
define('SIDEBAR_ACTIVE', '#2563eb');
define('SIDEBAR_ACTIVE_GRADIENT', '#1d4ed8');
define('SIDEBAR_BG_DARK', '#0f172a');

// ===== CONFIGURACIÓN DE TABLAS =====
define('FILASXPAGINA', 10);
define('FLASH_TIEMPO', 5);

// ===== CONFIGURACIÓN DE SESIÓN =====
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);
}

// ===== DETECCIÓN AUTOMÁTICA DE ENTORNO PARA RUTAS =====
// Normalizar hostname (quitar www si existe)
$host = $_SERVER['HTTP_HOST'];
$host_normalizado = preg_replace('/^www\./', '', $host);

// Detectar si estamos en localhost (con o sin www)
$es_localhost = ($host_normalizado == 'localhost' || $host_normalizado == '127.0.0.1');

if ($es_localhost) {
    define('BASE_URL', 'http://' . $host . '/practica_sventas_desa/');
    define('BASE_PATH', '/practica_sventas_desa/');
} else {
    // En servidor de producción - detección automática
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $base_path = ($script_dir == '/' || $script_dir == '\\') ? '/' : $script_dir . '/';
    define('BASE_PATH', $base_path);
    define('BASE_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $host . BASE_PATH);
}

define('BASE_URL_RELATIVE', BASE_PATH);

// Opcional: Mostrar entorno actual para debugging (oculto en producción)
// echo "<!-- Entorno: " . ($es_localhost ? 'LOCAL' : 'SERVIDOR') . " -->";
// echo "<!-- BASE_PATH: " . BASE_PATH . " -->";
?>