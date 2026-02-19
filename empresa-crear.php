<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crear Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
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
                            regionSelect.appendChild(opt);
                        });
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
                            ciudadSelect.appendChild(opt);
                        });
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
                <h4>Agregar Empresa
                    <a href="inicio-empresas.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="empresa-acciones.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>RUT</label>
                            <input type="text" name="rut" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Nombre Empresa</label>
                            <input type="text" name="nombreEmpresa" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control">
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
                                    echo "<option value='{$p['idPais']}'>{$p['nombrePais']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Región</label>
                            <select class="form-select" id="region" onchange="cargarCiudades()" required>
                                <option value="">-- Primero selecciona país --</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Ciudad</label>
                            <select class="form-select" id="ciudad" onchange="cargarComunas()" required>
                                <option value="">-- Primero selecciona región --</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Comuna</label>
                            <select class="form-select" name="idComuna" id="comuna" required>
                                <option value="">-- Primero selecciona ciudad --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="create_empresa" class="btn btn-primary">Grabar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>