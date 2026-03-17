<?php
/**
 * validaciones.php - Archivo central de validaciones
 */

// ===== FUNCIONES DE LIMPIEZA =====
function limpiarInput($input) {
    return preg_replace('/[<>"\']/', '', $input);
}

// ===== VALIDACIONES PARA PRODUCTOS =====
function validarCodigoBarras($codigo) {
    return preg_match('/^[0-9]{13}$/', $codigo);
}

function validarPrecio($precio) {
    return preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $precio);
}

function validarNombreProducto($nombre) {
    return preg_match('/^[a-zA-ZáéíóúñÑ0-9\s]+$/', $nombre);
}

function validarStock($stock) {
    return preg_match('/^[0-9]+$/', $stock);
}

// ===== VALIDACIONES PARA USUARIOS =====
function validarEmail($email) {
    return preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email);
}

function validarNombreUsuario($nombre) {
    return preg_match('/^[a-zA-ZáéíóúñÑ\s]+$/', $nombre);
}

// ===== VALIDACIONES PARA PAÍSES =====
function validarSiglaPais($sigla) {
    return preg_match('/^[A-Z]{2}$/', $sigla);
}

function validarNombrePais($nombre) {
    return preg_match('/^[a-zA-ZáéíóúñÑ\s]+$/', $nombre);
}

// ===== VALIDACIONES GENERALES =====
function validarID($id) {
    return preg_match('/^[1-9][0-9]*$/', $id);
}
// ===== FUNCIONES PARA URLs =====
function sanitizarURL($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}

function validarURLImagen($url) {
    return preg_match('/^(https?:\/\/).+\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?.*)?$/i', $url);
}

// ===== VALIDACIONES DE URLs =====
function validarURL($url) {
    return preg_match('/^(https?|ftp):\/\/([a-z0-9-]+\.)+[a-z]{2,}(:\d+)?(\/[^\s]*)?$/i', $url);
}

function validarURLVideo($url) {
    return preg_match('/^(https?:\/\/).+\.(mp4|webm|ogg|mov|avi|wmv)(\?.*)?$/i', $url);
}

function validarURLYouTube($url) {
    return preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[a-zA-Z0-9_-]{11}$/', $url);
}

function validarURLSegura($url) {
    return preg_match('/^https:\/\/([a-z0-9-]+\.)+[a-z]{2,}(:\d+)?(\/[^\s]*)?$/i', $url);
}

function extraerDominio($url) {
    if (preg_match('/^(https?|ftp):\/\/([^\/]+)/', $url, $matches)) {
        return $matches[2];
    }
    return false;
}
?>