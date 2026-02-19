<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        // Variables globales para almacenar IDs seleccionados
        var paisSeleccionado = 0;
        var regionSeleccionada = 0;
        var ciudadSeleccionada = 0;

        function cargarRegiones() {
            const idPais = document.getElementById('pais').value;
            const regionSelect = document.getElementById('region');
            regionSelect.innerHTML = '<option value="">-- Selecciona región --</option>';
            if (idPais) {
                fetch(`get_regiones.php?idPais=${idPais}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(r => {
                            const opt = document.createElement('option');
                            opt.value = r.idRegion;
                            opt.textContent = r.nombreRegion;
                            if (r.idRegion == regionSeleccionada) opt.selected = true;
                            regionSelect.appendChild(opt);
                        });
                        if (regionSeleccionada) cargarCiudades();
                    });
            }
        }
        function cargarCiudades() {
            const idRegion = document.getElementById('region').value;
            const ciudadSelect = document.getElementById('ciudad');
            ciudadSelect.innerHTML = '<option value="">-- Selecciona ciudad --</option>';
            if (idRegion) {
                fetch(`get_ciudades.php?idRegion=${idRegion}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.idCiudad;
                            opt.textContent = c.nomCiudad;
                            if (c.idCiudad == ciudadSeleccionada) opt.selected = true;
                            ciudadSelect.appendChild(opt);
                        });
                        if (ciudadSeleccionada) cargarComunas();
                    });
            }
        }
        function cargarComunas() {
            const idCiudad = document.getElementById('ciudad').value;
            const comunaSelect = document.getElementById('comuna');
            comunaSelect.innerHTML = '<option value="">-- Selecciona comuna --</option>';
            if (idCiudad) {
                fetch(`get_comunas.php?idCiudad=${idCiudad}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.idComuna;
                            opt.textContent = c.nomComuna;
                            comunaSelect.appendChild(opt);
                        });
                    });
            }
        }
    </script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Editar Empresa
                    <a href="inicio-empresas.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                    $sql = "SELECT e.*, c.idCiudad, ci.idRegion, r.idPais 
                            FROM empresas e
                            LEFT JOIN comunas c ON e.idComuna = c.idComuna
                            LEFT JOIN ciudades ci ON c.idCiudad = ci.idCiudad
                            LEFT JOIN regiones r ON ci.idRegion = r.idRegion
                            WHERE e.idEmpresa = ? AND e.vigente = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $empresa = $result->fetch_assoc();
                        ?>
                        <script>
                            // Pasar los IDs seleccionados a JavaScript
                            paisSeleccionado = <?= $empresa['idPais'] ?? 0 ?>;
                            regionSeleccionada = <?= $empresa['idRegion'] ?? 0 ?>;
                            ciudadSeleccionada = <?= $empresa['idCiudad'] ?? 0 ?>;
                        </script>
                        
                        <form action="empresa-acciones.php" method="POST">
                            <input type="hidden" name="idEmpresa" value="<?= $empresa['idEmpresa'] ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>RUT</label>
                                    <input type="text" name="rut" class="form-control" value="<?= $empresa['rut'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Nombre Empresa</label>
                                    <input type="text" name="nombreEmpresa" class="form-control" value="<?= $empresa['nombreEmpresa'] ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Dirección</label>
                                <input type="text" name="direccion" class="form-control" value="<?= $empresa['direccion'] ?>">
                            </div>
                            
                            <!-- SELECTORES GEOGRÁFICOS -->
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label>País</label>
                                    <select class="form-select" id="pais" onchange="cargarRegiones()" required>
                                        <option value="">-- Selecciona país --</option>
                                        <?php
                                        $paises = $conn->query("SELECT idPais, nombrePais FROM paises WHERE vigente=1");
                                        while ($p = $paises->fetch_assoc()) {
                                            $selected = ($p['idPais'] == $empresa['idPais']) ? 'selected' : '';
                                            echo "<option value='{$p['idPais']}' $selected>{$p['nombrePais']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Región</label>
                                    <select class="form-select" id="region" onchange="cargarCiudades()" required>
                                        <option value="">-- Cargando... --</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Ciudad</label>
                                    <select class="form-select" id="ciudad" onchange="cargarComunas()" required>
                                        <option value="">-- Cargando... --</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Comuna</label>
                                    <select class="form-select" name="idComuna" id="comuna" required>
                                        <option value="">-- Primero selecciona ciudad --</option>
                                        <?php
                                        // Precargar comunas de la ciudad seleccionada
                                        if ($empresa['idCiudad']) {
                                            $comunas = $conn->prepare("SELECT idComuna, nomComuna FROM comunas WHERE idCiudad = ? AND vigente = 1");
                                            $comunas->bind_param("i", $empresa['idCiudad']);
                                            $comunas->execute();
                                            $res_comunas = $comunas->get_result();
                                            while ($c = $res_comunas->fetch_assoc()) {
                                                $selected = ($c['idComuna'] == $empresa['idComuna']) ? 'selected' : '';
                                                echo "<option value='{$c['idComuna']}' $selected>{$c['nomComuna']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?= $empresa['telefono'] ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= $empresa['email'] ?>">
                                </div>
                            </div>
                            <button type="submit" name="update_empresa" class="btn btn-primary">Actualizar</button>
                        </form>
                        
                        <script>
                            // Iniciar carga de regiones si hay país seleccionado
                            if (paisSeleccionado) {
                                cargarRegiones();
                            }
                        </script>
                        <?php
                    } else {
                        echo '<h5>Empresa no encontrada</h5>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>