<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Consulta para obtener los países
$sqlPaises = "SELECT idPais, nombrePais FROM paises WHERE vigente = 1";
$resultPaises = $conn->query($sqlPaises);

// Procesar solicitud AJAX para obtener regiones
if (isset($_GET['idPais'])) {
    $idPais = intval($_GET['idPais']);
    $sqlRegiones = "SELECT idRegion, nombreRegion FROM regiones WHERE idPais = ? AND vigente = 1";
    $stmt = $conn->prepare($sqlRegiones);
    $stmt->bind_param("i", $idPais);
    $stmt->execute();
    $result = $stmt->get_result();
    $regiones = [];
    while ($row = $result->fetch_assoc()) {
        $regiones[] = $row;
    }
    $stmt->close();
    echo json_encode($regiones);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar País y Región</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        function cargarRegiones() {
            const idPais = document.getElementById('pais').value;
            const regionSelect = document.getElementById('region');
            regionSelect.innerHTML = '<option value="">-- Selecciona una región --</option>';
            
            if (idPais) {
                fetch(`get_regiones.php?idPais=${idPais}`)  
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(region => {
                            const option = document.createElement('option');
                            option.value = region.idRegion;
                            option.textContent = region.nombreRegion;
                            regionSelect.appendChild(option);
                        });
                    });
            }
        }
    </script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <form method="POST" action="menu.php?page=gestionar_ciudades.php">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>País:</label>
                    <select name="idPais" id="pais" class="form-select" onchange="cargarRegiones()" required>
                        <option value="">-- Selecciona un país --</option>
                        <?php while ($row = $resultPaises->fetch_assoc()): ?>
                            <option value="<?= $row['idPais'] ?>"><?= $row['nombrePais'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Región:</label>
                    <select name="idRegion" id="region" class="form-select" required>
                        <option value="">-- Primero selecciona país --</option>
                    </select>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">Gestionar Ciudades</button>
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>