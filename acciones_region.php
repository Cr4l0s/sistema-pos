<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require 'validaciones.php';

// ============================================
// CREAR REGIÓN
// ============================================
if (isset($_POST['create_region'])) {
    $idPais = intval($_POST['idPais']);
    $nombreRegion = limpiarInput(trim($_POST['nombreRegion']));
    $codRegion = limpiarInput(trim($_POST['codRegion']));

    // Validaciones
    if (!validarNombreProducto($nombreRegion)) {
        $_SESSION['mensaje'] = 'El nombre de la región contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=gestionar_regiones.php');
        exit;
    }
    
    if (!validarSiglaPais($codRegion)) {
        $_SESSION['mensaje'] = 'El código debe tener 2 letras mayúsculas';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=gestionar_regiones.php');
        exit;
    }

    if (!empty($nombreRegion) && !empty($codRegion)) {
        $sql = "INSERT INTO regiones (idPais, nombreRegion, codRegion, vigente) VALUES (?, ?, ?, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $idPais, $nombreRegion, $codRegion);

        if ($stmt->execute()) {
            $idRegion = $stmt->insert_id;
            $_SESSION['mensaje'] = "Región '$nombreRegion' creada exitosamente.";
            $_SESSION['tipo_mensaje'] = 'success';
            $stmt->close();
            
            // 🔴 REDIRIGIR CON POST A VER REGIÓN
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirigiendo...</title>
            </head>
            <body>
                <form id="redirectForm" action="menu.php" method="POST">
                    <input type="hidden" name="page" value="ver_region.php">
                    <input type="hidden" name="idRegion" value="<?= $idRegion ?>">
                </form>
                <script>
                    document.getElementById('redirectForm').submit();
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            $_SESSION['mensaje'] = 'Error al crear la región: ' . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';
            $stmt->close();
        }
    } else {
        $_SESSION['mensaje'] = 'Todos los campos son requeridos.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }

    header('Location: menu.php?page=gestionar_regiones.php');
    exit;
}

// ============================================
// ACTUALIZAR REGIÓN
// ============================================
if (isset($_POST['update_region'])) {
    $idRegion = intval($_POST['idRegion']);
    $nombreRegion = trim($_POST['nombreRegion']);
    $codRegion = trim($_POST['codRegion']);

    // Obtener nombre actual antes de actualizar
    $sql_nombre = "SELECT nombreRegion FROM regiones WHERE idRegion = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idRegion);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $region_actual = $result_nombre->fetch_assoc();
    $nombre_original = $region_actual ? $region_actual['nombreRegion'] : 'desconocido';
    $stmt_nombre->close();

    if (!empty($nombreRegion) && !empty($codRegion)) {
        $sql = "UPDATE regiones SET nombreRegion = ?, codRegion = ? WHERE idRegion = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nombreRegion, $codRegion, $idRegion);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Región '$nombreRegion' actualizada correctamente.";
            $_SESSION['tipo_mensaje'] = 'success';
            $stmt->close();
            
            // 🔴 REDIRIGIR CON POST A VER REGIÓN
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirigiendo...</title>
            </head>
            <body>
                <form id="redirectForm" action="menu.php" method="POST">
                    <input type="hidden" name="page" value="ver_region.php">
                    <input type="hidden" name="idRegion" value="<?= $idRegion ?>">
                </form>
                <script>
                    document.getElementById('redirectForm').submit();
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar la región: " . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';
            $stmt->close();
        }
    } else {
        $_SESSION['mensaje'] = 'Todos los campos son requeridos.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }

    header('Location: menu.php?page=gestionar_regiones.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=gestionar_regiones.php');
exit;
?>