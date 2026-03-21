<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// ACTUALIZAR CIUDAD
if (isset($_POST['update_ciudad'])) {
    $idCiudad = intval($_POST['idCiudad']);
    $nombreCiudad = trim($_POST['nombreCiudad']);

    // Obtener nombre actual antes de actualizar
    $sql_nombre = "SELECT nombreCiudad FROM ciudades WHERE idCiudad = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $idCiudad);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $ciudad_actual = $result_nombre->fetch_assoc();
    $nombre_original = $ciudad_actual ? $ciudad_actual['nombreCiudad'] : 'desconocido';
    $stmt_nombre->close();

    if (!empty($nombreCiudad)) {
        $sql = "UPDATE ciudades SET nombreCiudad = ? WHERE idCiudad = ? AND vigente = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombreCiudad, $idCiudad);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "Ciudad '$nombreCiudad' actualizada correctamente.";
            $_SESSION['tipo_mensaje'] = 'success';
            $stmt->close();
            
            // 🔴 MODIFICADO: Redirigir a ver_ciudad.php con POST
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Redirigiendo...</title>
            </head>
            <body>
                <form id="redirectForm" action="menu.php" method="POST">
                    <input type="hidden" name="page" value="ver_ciudad.php">
                    <input type="hidden" name="idCiudad" value="<?= $idCiudad ?>">
                </form>
                <script>
                    document.getElementById('redirectForm').submit();
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            $_SESSION['mensaje'] = "Error al actualizar la ciudad: " . $stmt->error;
            $_SESSION['tipo_mensaje'] = 'danger';
            $stmt->close();
        }
    } else {
        $_SESSION['mensaje'] = 'El nombre de la ciudad no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }

    header('Location: menu.php?page=gestionar_ciudades.php');
    exit;
}

// Si alguien accede directamente sin POST
header('Location: menu.php?page=gestionar_ciudades.php');
exit;
?>