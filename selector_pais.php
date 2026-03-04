<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mantenedor de Regiones</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    
    <body>
        <!-- NO incluir navbar.php aquí -->
        <div class="container mt-4">
            <?php include('mensaje.php'); ?>
    
            <form method="POST" action="menu.php?page=gestionar_regiones.php">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-select" name="idPais" id="idPais" onchange="updateNombrePais()" required>
                            <option value="">Seleccione un País de la Lista</option>
                            <?php
                            $sql = "SELECT idPais, nombrePais FROM paises WHERE vigente = 1 ORDER BY nombrePais";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['idPais'] . "'>" . $row['nombrePais'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden" name="nombrePais" id="nombrePais">
                        <button type="submit" class="btn btn-primary">Gestionar Regiones</button>
                    </div>
                </div>
            </form>
        </div>

        <script>
            function updateNombrePais() {
                var select = document.getElementById('idPais');
                var nombrePaisInput = document.getElementById('nombrePais');
                if (select.selectedIndex > 0) {
                    nombrePaisInput.value = select.options[select.selectedIndex].text;
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>