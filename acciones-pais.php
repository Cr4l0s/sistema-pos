<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';
require 'validaciones.php';

// Debug: Guardar POST recibido
file_put_contents('debug_post.txt', date('Y-m-d H:i:s') . " - POST: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Verificar que sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menu.php?page=inicio_pais.php');
    exit;
}

// ============================================
// EDITAR PAÍS
// ============================================
if (isset($_POST['editar_pais'])) {
    // Validar campos requeridos
    if (!isset($_POST['idPais']) || !isset($_POST['siglaPais']) || !isset($_POST['nombrePais'])) {
        $_SESSION['mensaje'] = 'Faltan datos requeridos para editar';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio_pais.php');
        exit;
    }

    $idPais = intval($_POST['idPais']);
    $siglaPais = strtoupper(limpiarInput(trim($_POST['siglaPais'])));
    $nombrePais = limpiarInput(trim($_POST['nombrePais']));

    // ===== VALIDACIONES CON EXPRESIONES REGULARES =====

    // Validar ID
    if (!validarID($idPais)) {
        $_SESSION['mensaje'] = 'ID de país no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio_pais.php');
        exit;
    }

    // Validar sigla (2 letras mayúsculas)
    if (!validarSiglaPais($siglaPais)) {
        $_SESSION['mensaje'] = 'La sigla debe tener 2 letras mayúsculas';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=pais-editar.php&id=$idPais");
        exit;
    }

    // Validar nombre (solo letras y espacios)
    if (!validarNombrePais($nombrePais)) {
        $_SESSION['mensaje'] = 'El nombre del país contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=pais-editar.php&id=$idPais");
        exit;
    }

    // Actualizar país
    $update = $conn->prepare("UPDATE paises SET siglaPais = ?, nombrePais = ? WHERE idPais = ? AND vigente = 1");
    $update->bind_param("ssi", $siglaPais, $nombrePais, $idPais);

    if ($update->execute()) {
        $_SESSION['mensaje'] = "País '$nombrePais' actualizado correctamente";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar: ' . $update->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }

    $update->close();
    header("Location: menu.php?page=pais-ver.php&id=$idPais");
    exit;
}

// ============================================
// CREAR PAÍS
// ============================================
if (isset($_POST['create_pais'])) {
    // Validar campos requeridos
    if (!isset($_POST['siglaPais']) || !isset($_POST['nombrePais'])) {
        $_SESSION['mensaje'] = 'Faltan datos requeridos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=pais-crear.php');
        exit;
    }

    $siglaPais = strtoupper(limpiarInput(trim($_POST['siglaPais'])));
    $nombrePais = limpiarInput(trim($_POST['nombrePais']));
    $monedas = $_POST['monedas'] ?? [];

    // ===== VALIDACIONES =====

    if (!validarSiglaPais($siglaPais)) {
        $_SESSION['mensaje'] = 'La sigla debe tener 2 letras mayúsculas';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=pais-crear.php');
        exit;
    }

    if (!validarNombrePais($nombrePais)) {
        $_SESSION['mensaje'] = 'El nombre del país contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=pais-crear.php');
        exit;
    }

    if (empty($monedas)) {
        $_SESSION['mensaje'] = 'Debe seleccionar al menos una moneda';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=pais-crear.php');
        exit;
    }

    // Insertar país
    $conn->begin_transaction();

    try {
        $sql = "INSERT INTO paises (siglaPais, nombrePais, vigente) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $siglaPais, $nombrePais);

        if (!$stmt->execute()) {
            throw new Exception("Error al crear país: " . $stmt->error);
        }

        $idPais = $stmt->insert_id;
        $stmt->close();
        // Asociar país al usuario que lo creó
        $idUsuario = $_SESSION['usuario_id'] ?? 0;
        if ($idUsuario > 0) {
            $sql_relacion = "INSERT INTO usuarios_paises (idUsuario, idPais) VALUES (?, ?)";
            $stmt_relacion = $conn->prepare($sql_relacion);
            $stmt_relacion->bind_param("ii", $idUsuario, $idPais);
            $stmt_relacion->execute();
            $stmt_relacion->close();
        }
        // Insertar monedas (la primera es la principal)
        foreach ($monedas as $index => $idMoneda) {
            // Validar que idMoneda sea un número
            if (!validarID($idMoneda)) {
                throw new Exception("ID de moneda no válido");
            }

            $es_principal = ($index == 0) ? 1 : 0;
            $sql_moneda = "INSERT INTO paises_monedas (idPais, idMoneda, es_principal) VALUES (?, ?, ?)";
            $stmt_moneda = $conn->prepare($sql_moneda);
            $stmt_moneda->bind_param("iii", $idPais, $idMoneda, $es_principal);

            if (!$stmt_moneda->execute()) {
                throw new Exception("Error al asignar moneda: " . $stmt_moneda->error);
            }
            $stmt_moneda->close();
        }

        $conn->commit();
        $_SESSION['mensaje'] = "País '$nombrePais' creado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        header("Location: menu.php?page=pais-ver.php&id=$idPais");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=pais-crear.php');
        exit;
    }
}

// ============================================
// ELIMINAR PAÍS (CORREGIDO - Con nombre del país)
// ============================================
if (isset($_POST['borrar_pais'])) {
    $idPais = intval($_POST['borrar_pais']);

    // Validar ID
    if (!validarID($idPais)) {
        $_SESSION['mensaje'] = 'ID de país no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio_pais.php');
        exit;
    }

    // 🔴 NUEVO: Obtener nombre del país antes de eliminarlo
    $sql_nombre = "SELECT nombrePais FROM paises WHERE idPais = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idPais);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $pais = $result_nombre->fetch_assoc();
    $nombrePais = $pais ? $pais['nombrePais'] : 'desconocido';
    $stmt_nombre->close();

    // Eliminar país (borrado lógico)
    $sql = "UPDATE paises SET vigente = 0 WHERE idPais = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPais);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "País '$nombrePais' eliminado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "Error al eliminar país '$nombrePais': " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();

    header('Location: menu.php?page=inicio_pais.php');
    exit;
}

// Si llegamos aquí, no se ejecutó ninguna acción
$_SESSION['mensaje'] = 'Acción no reconocida';
$_SESSION['tipo_mensaje'] = 'warning';
header('Location: menu.php?page=inicio_pais.php');
exit;
?>