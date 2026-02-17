<?php
$host = 'localhost';
$dbname = 'practica_sventas';
$username = 'practica_global';
$password = 'Global_2026';

$conn = new mysqli($host, $username, $password, $dbname);

if($conn->connect_error) {
    die('Connection failed'. $conn->connect_error);
}
?>
