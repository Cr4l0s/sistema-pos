<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Inicio del archivo<br>";

require '../../db.php';
echo "db.php cargado<br>";

require_once '../../config.php';
echo "config.php cargado<br>";

$prueba = "Todo bien";
echo json_encode(["mensaje" => $prueba]);
?>