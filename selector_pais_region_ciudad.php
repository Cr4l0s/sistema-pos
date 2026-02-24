<?php
// selector_pais_region_ciudad.php
session_start();
include 'db.php';

// Obtener lista de países para el primer selector
$sqlPaises = "SELECT idPais, nombrePais FROM paises WHERE vigente = 1 ORDER BY nombrePais";
$resultPaises = $conn->query($sqlPaises);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar País, Región y Ciudad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <h2>Seleccionar Ubicación para Gestionar Comunas</h2>
        <form method="POST" action="gestionar_comunas.php" id="formUbicacion">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="pais" class="form-label">País:</label>
                    <select name="idPais" id="pais" class="form-select" required>
                        <option value="">-- Selecciona un país --</option>
                        <?php while ($row = $resultPaises->fetch_assoc()): ?>
                            <option value="<?= $row['idPais'] ?>"><?= $row['nombrePais'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="region" class="form-label">Región:</label>
                    <select name="idRegion" id="region" class="form-select" required disabled>
                        <option value="">-- Primero selecciona un país --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="ciudad" class="form-label">Ciudad:</label>
                    <select name="idCiudad" id="ciudad" class="form-select" required disabled>
                        <option value="">-- Primero selecciona una región --</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="btnGestionar" disabled>Gestionar Comunas</button>
        </form>
    </div>

    <script>
        // Espera a que todo el DOM esté cargado
        document.addEventListener('DOMContentLoaded', function() {
            const paisSelect = document.getElementById('pais');
            const regionSelect = document.getElementById('region');
            const ciudadSelect = document.getElementById('ciudad');
            const btnGestionar = document.getElementById('btnGestionar');

            // --- Cargar Regiones al seleccionar un País ---
            paisSelect.addEventListener('change', function() {
                const idPais = this.value;
                // Reset y deshabilitar selectores dependientes
                regionSelect.innerHTML = '<option value="">-- Cargando regiones... --</option>';
                regionSelect.disabled = true;
                ciudadSelect.innerHTML = '<option value="">-- Primero selecciona una región --</option>';
                ciudadSelect.disabled = true;
                btnGestionar.disabled = true;

                if (idPais) {
                    fetch(`get_regiones.php?idPais=${idPais}`)
                        .then(response => response.json())
                        .then(data => {
                            regionSelect.innerHTML = '<option value="">-- Selecciona una región --</option>';
                            data.forEach(region => {
                                const option = document.createElement('option');
                                option.value = region.idRegion;
                                option.textContent = region.nombreRegion;
                                regionSelect.appendChild(option);
                            });
                            regionSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error cargando regiones:', error);
                            regionSelect.innerHTML = '<option value="">-- Error al cargar --</option>';
                        });
                } else {
                    regionSelect.innerHTML = '<option value="">-- Primero selecciona un país --</option>';
                }
            });

            // --- Cargar Ciudades al seleccionar una Región ---
            regionSelect.addEventListener('change', function() {
                const idRegion = this.value;
                ciudadSelect.innerHTML = '<option value="">-- Cargando ciudades... --</option>';
                ciudadSelect.disabled = true;
                btnGestionar.disabled = true;

                if (idRegion) {
                    fetch(`get_ciudades_por_region.php?idRegion=${idRegion}`)
                        .then(response => response.json())
                        .then(data => {
                            ciudadSelect.innerHTML = '<option value="">-- Selecciona una ciudad --</option>';
                            data.forEach(ciudad => {
                                const option = document.createElement('option');
                                option.value = ciudad.idCiudad;
                                option.textContent = ciudad.nombreCiudad;
                                ciudadSelect.appendChild(option);
                            });
                            ciudadSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error cargando ciudades:', error);
                            ciudadSelect.innerHTML = '<option value="">-- Error al cargar --</option>';
                        });
                } else {
                    ciudadSelect.innerHTML = '<option value="">-- Primero selecciona una región --</option>';
                }
            });

            // --- Habilitar botón al seleccionar una Ciudad ---
            ciudadSelect.addEventListener('change', function() {
                btnGestionar.disabled = !this.value;
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>