<?php
$host = 'localhost';
$dbname = 'practica_sventas_desa';
$username = 'practica_global';
$password = 'Global_2026';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>