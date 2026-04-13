<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Verificar que se recibió el ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idPais'])) {
    $idPais = intval($_POST['idPais']);
} else {
    $_SESSION['mensaje'] = 'ID de país no proporcionado.';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}

// Obtener nombre del país ANTES de eliminarlo
$sql_nombre = "SELECT nombrePais FROM paises WHERE idPais = ? AND vigente = 1";
$stmt_nombre = $conn->prepare($sql_nombre);
$stmt_nombre->bind_param("i", $idPais);
$stmt_nombre->execute();
$result_nombre = $stmt_nombre->get_result();
$pais = $result_nombre->fetch_assoc();
$nombrePais = $pais ? $pais['nombrePais'] : 'desconocido';
$stmt_nombre->close();

error_log("ID recibido para eliminar: " . $idPais);
error_log("Nombre del país a eliminar: " . $nombrePais);

// =============================================
// REGLA 1: Verificar si el país tiene regiones activas
// =============================================
$sql_check_regiones = "SELECT COUNT(*) as total FROM regiones WHERE idPais = ? AND vigente = 1";
$stmt_check = $conn->prepare($sql_check_regiones);
$stmt_check->bind_param("i", $idPais);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row = $result_check->fetch_assoc();

error_log("Regiones activas encontradas: " . $row['total']);

if ($row['total'] > 0) {
    $_SESSION['mensaje'] = "No se puede eliminar el país '$nombrePais' porque tiene {$row['total']} región(es) activa(s).";
    $_SESSION['tipo_mensaje'] = 'warning';
    $stmt_check->close();
    $conn->close();
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}
$stmt_check->close();

// Borrado lógico (vigente = 0)
$sql = "UPDATE paises SET vigente = 0 WHERE idPais = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPais);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['mensaje'] = "País '$nombrePais' eliminado correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "El país '$nombrePais' no existe o ya fue eliminado.";
        $_SESSION['tipo_mensaje'] = 'warning';
    }
} else {
    $_SESSION['mensaje'] = "Error al eliminar el país '$nombrePais': " . $stmt->error;
    $_SESSION['tipo_mensaje'] = 'danger';
}

$stmt->close();
$conn->close();
header('Location: menu.php?page=inicio_pais.php');
exit;
?>