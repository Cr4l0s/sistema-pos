<?php
require_once 'db.php';
require_once 'mensajes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['idPais']) ||
        empty($_POST['nombreRegion']) ||
        empty($_POST['codRegion'])
    ) {
        $_SESSION['mensaje'] = 'Error: Todos los campos son requeridos.';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=gestionar_regiones.php");
        exit();
    }

    $idPais = (int) $_POST['idPais'];
    $nombreRegion = trim($_POST['nombreRegion']);
    $codRegion = trim($_POST['codRegion']);
    $vigente = 1;

    $stmt = $conn->prepare(
        "INSERT INTO regiones (idPais, nombreRegion, codRegion, vigente) 
         VALUES (?, ?, ?, ?)"
    );

    if ($stmt === false) {
        $_SESSION['mensaje'] = 'Error al preparar la consulta: ' . $conn->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=gestionar_regiones.php");
        exit();
    }

    $stmt->bind_param("issi", $idPais, $nombreRegion, $codRegion, $vigente);

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
        $_SESSION['mensaje'] = 'Error al agregar la región: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header("Location: menu.php?page=gestionar_regiones.php");
        exit();
    }
}
?>