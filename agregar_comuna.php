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
                $_SESSION['mensaje'] = "Ya existe una comuna activa con el nombre '$nombreComuna'.";
                $_SESSION['tipo_mensaje'] = 'warning';
                $stmt_check->close();
                header('Location: menu.php?page=gestionar_comunas.php');
                exit;
            } else {
                // Reactivar
                $sql_reactivar = "UPDATE comunas SET vigente = 1 WHERE idComuna = ?";
                $stmt_reactivar = $conn->prepare($sql_reactivar);
                $stmt_reactivar->bind_param("i", $existente['idComuna']);
                $stmt_reactivar->execute();
                $stmt_reactivar->close();
                $stmt_check->close();
                
                $_SESSION['mensaje'] = "Comuna '$nombreComuna' reactivada exitosamente.";
                $_SESSION['tipo_mensaje'] = 'success';
                
                // 🔴 REDIRIGIR CON POST A VER COMUNA
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Redirigiendo...</title>
                </head>
                <body>
                    <form id="redirectForm" action="menu.php" method="POST">
                        <input type="hidden" name="page" value="ver_comuna.php">
                        <input type="hidden" name="idComuna" value="<?= $existente['idComuna'] ?>">
                    </form>
                    <script>
                        document.getElementById('redirectForm').submit();
                    </script>
                </body>
                </html>
                <?php
                exit;
            }
        } else {
            // Insertar nueva
            $sql = "INSERT INTO comunas (idCiudad, nombreComuna, vigente) VALUES (?, ?, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $idCiudad, $nombreComuna);
            
            if ($stmt->execute()) {
                $idComuna = $stmt->insert_id;
                $_SESSION['mensaje'] = "Comuna '$nombreComuna' creada exitosamente.";
                $_SESSION['tipo_mensaje'] = 'success';
                $stmt->close();
                
                // 🔴 REDIRIGIR CON POST A VER COMUNA
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
                $_SESSION['mensaje'] = 'Error al agregar comuna: ' . $stmt->error;
                $_SESSION['tipo_mensaje'] = 'danger';
                $stmt->close();
            }
        }
    } else {
        $_SESSION['mensaje'] = 'El nombre de la comuna no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';
    }
} else {
    $_SESSION['mensaje'] = 'Solicitud inválida.';
    $_SESSION['tipo_mensaje'] = 'danger';
}

header('Location: menu.php?page=gestionar_comunas.php');
exit;
?>