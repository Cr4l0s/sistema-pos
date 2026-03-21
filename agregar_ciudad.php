<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idRegion = intval($_POST['idRegion']);
    $nombreCiudad = trim($_POST['nombreCiudad']);
    $vigente = 1;

    // 🔴 NUEVO: Verificar que el nombre no esté vacío
    if (empty($nombreCiudad)) {
        $_SESSION['mensaje'] = 'El nombre de la ciudad no puede estar vacío.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=gestionar_ciudades.php");
        exit;
    }

    $sql = "INSERT INTO ciudades (idRegion, nombreCiudad, vigente) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $idRegion, $nombreCiudad, $vigente);

    if ($stmt->execute()) {
        $idCiudad = $stmt->insert_id;
        $_SESSION['mensaje'] = "Ciudad '$nombreCiudad' creada exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        
        // 🔴 REDIRIGIR CON POST A VER CIUDAD
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
        $_SESSION['mensaje'] = 'Error al agregar ciudad: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header("Location: menu.php?page=gestionar_ciudades.php");
        exit;
    }
}
?>