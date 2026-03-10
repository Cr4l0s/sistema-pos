<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombreComuna'], $_POST['idCiudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
    $nombreComuna = trim($_POST['nombreComuna']);

    if (!empty($nombreComuna)) {
        // Verificar si ya existe
        $sql_check = "SELECT idComuna, vigente FROM comunas WHERE idCiudad = ? AND nombreComuna = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("is", $idCiudad, $nombreComuna);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $existente = $result_check->fetch_assoc();
            if ($existente['vigente'] == 1) {
                $_SESSION['mensaje'] = 'Ya existe una comuna activa con ese nombre.';
                $_SESSION['tipo_mensaje'] = 'warning';  // 🟡 Amarillo
            } else {
                // Reactivar
                $sql_reactivar = "UPDATE comunas SET vigente = 1 WHERE idComuna = ?";
                $stmt_reactivar = $conn->prepare($sql_reactivar);
                $stmt_reactivar->bind_param("i", $existente['idComuna']);
                $stmt_reactivar->execute();
                $stmt_reactivar->close();
                $_SESSION['mensaje'] = 'Comuna reactivada correctamente.';
                $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
            }
            $stmt_check->close();
        } else {
            // Insertar nueva
            $sql = "INSERT INTO comunas (idCiudad, nombreComuna, vigente) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $idCiudad, $nombreComuna);
            $stmt->execute();
            $stmt->close();
            $_SESSION['mensaje'] = 'Comuna agregada correctamente.';
            $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
        }
    } else {
        $_SESSION['mensaje'] = 'El nombre de la comuna no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }
} else {
    $_SESSION['mensaje'] = 'Solicitud inválida.';
    $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
}

header('Location: menu.php?page=gestionar_comunas.php');
exit;
?>