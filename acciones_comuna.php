<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// ACTUALIZAR COMUNA
if (isset($_POST['update_comuna'])) {
    $idComuna = intval($_POST['idComuna']);
    $nombreComuna = trim($_POST['nombreComuna']);

    // Obtener nombre actual antes de actualizar
    $sql_nombre = "SELECT nombreComuna FROM comunas WHERE idComuna = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idComuna);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $comuna_actual = $result_nombre->fetch_assoc();
    $nombre_original = $comuna_actual ? $comuna_actual['nombreComuna'] : 'desconocido';
    $stmt_nombre->close();

    if (!empty($nombreComuna)) {
        $sql = "UPDATE comunas SET nombreComuna = ? WHERE idComuna = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreComuna, $idComuna);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Comuna '$nombreComuna' actualizada correctamente.";
            $_SESSION['tipo_mensaje'] = 'success';
            $stmt->close();
            
            // 🔴 MODIFICADO: Redirigir a ver_comuna.php con POST
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirigiendo...</title>
            </head>
            <body>
                <form id="redirectForm" action="menu.php" method="POST">
                    <input type="hidden" name="page" value="ver_comuna.php">
                    <input type="hidden" name="idComuna" value="<?= $idComuna ?>">
                </form>
                <script>
                    document.getElementById('redirectForm').submit();
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar la comuna: " . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';
            $stmt->close();
        }
    } else {
        $_SESSION['mensaje'] = 'El nombre de la comuna no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }

    header('Location: menu.php?page=gestionar_comunas.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=gestionar_comunas.php');
exit;
?>