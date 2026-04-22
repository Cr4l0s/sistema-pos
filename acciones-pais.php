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
// EDITAR PAÍS (SIMPLIFICADO - SIN MONEDAS)
// ============================================
if (isset($_POST['editar_pais'])) {
    if (!isset($_POST['idPais']) || !isset($_POST['siglaPais']) || !isset($_POST['nombrePais'])) {
        $_SESSION['mensaje'] = 'Faltan datos requeridos para editar';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio_pais.php');
        exit;
    }

    $idPais = intval($_POST['idPais']);
    $siglaPais = strtoupper(limpiarInput(trim($_POST['siglaPais'])));
    $nombrePais = limpiarInput(trim($_POST['nombrePais']));

    if (!validarID($idPais)) {
        $_SESSION['mensaje'] = 'ID de país no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio_pais.php');
        exit;
    }

    if (!validarSiglaPais($siglaPais)) {
        $_SESSION['mensaje'] = 'La sigla debe tener 3 letras mayúsculas';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=pais-editar.php&id=$idPais");
        exit;
    }

    if (!validarNombrePais($nombrePais)) {
        $_SESSION['mensaje'] = 'El nombre del país contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=pais-editar.php&id=$idPais");
        exit;
    }

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
// CREAR PAÍS (SIMPLIFICADO - SIN MONEDAS EXTERNAS)
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
    $codMoneda = !empty($_POST['codMoneda']) ? limpiarInput(trim($_POST['codMoneda'])) : null;
    $nombreMoneda = !empty($_POST['nombreMoneda']) ? limpiarInput(trim($_POST['nombreMoneda'])) : null;
    $simbolo_moneda = !empty($_POST['simbolo_moneda']) ? limpiarInput(trim($_POST['simbolo_moneda'])) : '$';

    // Validaciones
    if (!validarSiglaPais($siglaPais)) {
        $_SESSION['mensaje'] = 'La sigla debe tener 3 letras mayúsculas';
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

    // Insertar país con los datos de moneda directamente en la tabla paises
    $sql = "INSERT INTO paises (siglaPais, nombrePais, codMoneda, nombreMoneda, simbolo_moneda, vigente) 
            VALUES (?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $siglaPais, $nombrePais, $codMoneda, $nombreMoneda, $simbolo_moneda);

    if ($stmt->execute()) {
        $idPais = $stmt->insert_id;
        
        // Asociar país al usuario que lo creó
        $idUsuario = $_SESSION['usuario_id'] ?? 0;
        if ($idUsuario > 0) {
            $sql_relacion = "INSERT INTO usuarios_paises (idUsuario, idPais) VALUES (?, ?)";
            $stmt_relacion = $conn->prepare($sql_relacion);
            $stmt_relacion->bind_param("ii", $idUsuario, $idPais);
            $stmt_relacion->execute();
            $stmt_relacion->close();
        }
        
        $_SESSION['mensaje'] = "País '$nombrePais' creado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        header("Location: menu.php?page=pais-ver.php&id=$idPais");
        exit;
    } else {
        $_SESSION['mensaje'] = 'Error al crear país: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
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