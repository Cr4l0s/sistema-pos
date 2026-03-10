<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Verificar que se recibió el ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_producto'])) {
    $id = intval($_POST['id_producto']);
} else {
    $_SESSION['mensaje'] = 'ID de producto no proporcionado.';
    $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    header('Location: menu.php?page=productos.php');
    exit;
}

// Verificar que el producto existe y está activo
$sql_check = "SELECT id_producto FROM productos WHERE id_producto = ? AND activo = 1";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    $_SESSION['mensaje'] = 'El producto no existe o ya fue eliminado.';
    $_SESSION['tipo_mensaje'] = 'warning';  // 🟡 Amarillo
    $stmt_check->close();
    header('Location: menu.php?page=productos.php');
    exit;
}
$stmt_check->close();

// Borrado lógico
$sql = "UPDATE productos SET activo = 0 WHERE id_producto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['mensaje'] = 'Producto eliminado correctamente.';
    $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
} else {
    $_SESSION['mensaje'] = 'Error al eliminar el producto: ' . $stmt->error;
    $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
}

$stmt->close();
header('Location: menu.php?page=productos.php');
exit;
?>