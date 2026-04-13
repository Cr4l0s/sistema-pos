<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Verificar que se recibió el ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idRegion'])) {
    $idRegion = intval($_POST['idRegion']);
} else {
    $_SESSION['mensaje'] = 'ID de región no proporcionado.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: menu.php?page=gestionar_regiones.php');
    exit;
}

// Obtener nombre de la región antes de eliminarla
$sql_nombre = "SELECT nombreRegion FROM regiones WHERE idRegion = ? AND vigente = 1";
$stmt_nombre = $conn->prepare($sql_nombre);
$stmt_nombre->bind_param("i", $idRegion);
$stmt_nombre->execute();
$result_nombre = $stmt_nombre->get_result();
$region = $result_nombre->fetch_assoc();
$nombre_region = $region ? $region['nombreRegion'] : 'desconocido';
$stmt_nombre->close();

// Verificar que la región existe y está vigente
$sql_check = "SELECT idRegion FROM regiones WHERE idRegion = ? AND vigente = 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $idRegion);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    $_SESSION['mensaje'] = "La región '$nombre_region' no existe o ya fue eliminada.";
    $_SESSION['tipo_mensaje'] = 'warning';
    $stmt_check->close();
    header('Location: menu.php?page=gestionar_regiones.php');
    exit;
}
$stmt_check->close();

// =============================================
// REGLA 3: Verificar si la región tiene ciudades activas
// =============================================
$sql_check_ciudades = "SELECT COUNT(*) as total FROM ciudades WHERE idRegion = ? AND vigente = 1";
$stmt_check = $conn->prepare($sql_check_ciudades);
$stmt_check->bind_param("i", $idRegion);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row = $result_check->fetch_assoc();

if ($row['total'] > 0) {
    $_SESSION['mensaje'] = "No se puede eliminar la región '$nombre_region' porque tiene {$row['total']} ciudad(es) activa(s).";
    $_SESSION['tipo_mensaje'] = 'warning';
    $stmt_check->close();
    $conn->close();
    header('Location: menu.php?page=gestionar_regiones.php');
    exit;
}
$stmt_check->close();

// Borrado lógico
$sql = "UPDATE regiones SET vigente = 0 WHERE idRegion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idRegion);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = "Región '$nombre_region' eliminada correctamente.";
    $_SESSION['tipo_mensaje'] = 'success';
} else {
    $_SESSION['mensaje'] = "Error al eliminar la región '$nombre_region': " . $stmt->error;
    $_SESSION['tipo_mensaje'] = 'danger';
}

$stmt->close();
$conn->close();
header('Location: menu.php?page=gestionar_regiones.php');
exit;
?>