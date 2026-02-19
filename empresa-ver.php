<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ver Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Ver Empresa
                    <a href="inicio-empresas.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                    
                    $sql = "SELECT e.*, 
                                   c.nomComuna,
                                   ci.nombreCiudad,
                                   r.nombreRegion,
                                   p.nombrePais
                            FROM empresas e
                            LEFT JOIN comunas c ON e.idComuna = c.idComuna
                            LEFT JOIN ciudades ci ON c.idCiudad = ci.idCiudad
                            LEFT JOIN regiones r ON ci.idRegion = r.idRegion
                            LEFT JOIN paises p ON r.idPais = p.idPais
                            WHERE e.idEmpresa = ? AND e.vigente = 1";
                    
                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        die("Error en la consulta: " . $conn->error);
                    }
                    
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $emp = $result->fetch_assoc();
                ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>ID Empresa</b></label>
                        <p class="form-control"><?= $emp['idEmpresa'] ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><b>RUT</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['rut']) ?></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label><b>Nombre Empresa</b></label>
                    <p class="form-control"><?= htmlspecialchars($emp['nombreEmpresa']) ?></p>
                </div>
                
                <div class="mb-3">
                    <label><b>Dirección</b></label>
                    <p class="form-control"><?= htmlspecialchars($emp['direccion'] ?: '—') ?></p>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label><b>País</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['nombrePais'] ?: '—') ?></p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label><b>Región</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['nombreRegion'] ?: '—') ?></p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label><b>Ciudad</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['nombreCiudad'] ?: '—') ?></p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label><b>Comuna</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['nombreCiudad'] ?: '—') ?></p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><b>Teléfono</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['telefono'] ?: '—') ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><b>Email</b></label>
                        <p class="form-control"><?= htmlspecialchars($emp['email'] ?: '—') ?></p>
                    </div>
                </div>
                
                <?php
                    } else {
                        echo '<h5>Empresa no encontrada</h5>';
                    }
                    $stmt->close();
                } else {
                    echo '<h5>ID no proporcionado</h5>';
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>