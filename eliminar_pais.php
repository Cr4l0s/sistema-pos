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
    $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}

// Después de recibir el ID
error_log("ID recibido para eliminar: " . $idPais);

// Antes de ejecutar la consulta
error_log("SQL: UPDATE paises SET vigente = 0 WHERE idPais = $idPais AND vigente = 1");

// Borrado lógico (vigente = 0)
$sql = "UPDATE paises SET vigente = 0 WHERE idPais = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPais);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['mensaje'] = 'País eliminado correctamente.';
        $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
    } else {
        $_SESSION['mensaje'] = 'El país no existe o ya fue eliminado.';
        $_SESSION['tipo_mensaje'] = 'warning';  // 🟡 Amarillo
    }
} else {
    $_SESSION['mensaje'] = 'Error al eliminar el país: ' . $stmt->error;
    $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
}

$stmt->close();
header('Location: menu.php?page=inicio_pais.php');
exit;
?>