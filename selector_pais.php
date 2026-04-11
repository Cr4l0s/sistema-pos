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
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>

        <div class="card">
            <div class="card-header">
                <h4>Seleccionar País para Gestionar Regiones</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="menu.php?page=gestionar_regiones.php">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">País</label>
                            <select class="form-select" name="idPais" id="idPais" required>
                                <option value="">Seleccione un País de la Lista</option>
                                <?php
                                $sql = "SELECT idPais, nombrePais, siglaPais FROM paises WHERE vigente = 1 ORDER BY nombrePais";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['idPais'] . "'>" . htmlspecialchars($row['nombrePais']) . " (" . htmlspecialchars($row['siglaPais']) . ")</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 align-self-end">
                            <input type="hidden" name="nombrePais" id="nombrePais">
                            <button type="submit" class="btn btn-primary">Gestionar Regiones</button>
                            <a href="menu.php" class="btn btn-danger">Volver al Menú</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('idPais').addEventListener('change', function() {
            var select = this;
            var nombrePaisInput = document.getElementById('nombrePais');
            if (select.selectedIndex > 0) {
                nombrePaisInput.value = select.options[select.selectedIndex].text;
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>