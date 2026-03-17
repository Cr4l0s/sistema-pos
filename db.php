<?php
$host = 'localhost';
$dbname = 'practica_sventas_desa';
$username = 'practica_global';
$password = 'Global_2026';

$conn = new mysqli($host, $username, $password, $dbname);

if($conn->connect_error) {
    die('Connection failed'. $conn->connect_error);
}

// FORZAR UTF-8 DE MÚLTIPLES FORMAS
$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4'");
$conn->query("SET CHARACTER SET utf8mb4");
?>