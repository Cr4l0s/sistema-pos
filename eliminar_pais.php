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

// 🔴 NUEVO: Obtener nombre del país ANTES de eliminarlo
$sql_nombre = "SELECT nombrePais FROM paises WHERE idPais = ? AND vigente = 1";
$stmt_nombre = $conn->prepare($sql_nombre);
$stmt_nombre->bind_param("i", $idPais);
$stmt_nombre->execute();
$result_nombre = $stmt_nombre->get_result();
$pais = $result_nombre->fetch_assoc();
$nombrePais = $pais ? $pais['nombrePais'] : 'desconocido';
$stmt_nombre->close();

// Después de recibir el ID
error_log("ID recibido para eliminar: " . $idPais);
error_log("Nombre del país a eliminar: " . $nombrePais);

// Antes de ejecutar la consulta
error_log("SQL: UPDATE paises SET vigente = 0 WHERE idPais = $idPais AND vigente = 1");

// Borrado lógico (vigente = 0)
$sql = "UPDATE paises SET vigente = 0 WHERE idPais = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPais);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // 🔴 MODIFICADO: Incluir nombre del país en el mensaje
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
header('Location: menu.php?page=inicio_pais.php');
exit;
?>