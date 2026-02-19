<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Región</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Editar Región
                    <a href="gestionar_regiones.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['idRegion'])) {
                    $idRegion = intval($_GET['idRegion']);
                    
                    $sql = "SELECT * FROM regiones WHERE idRegion = ? AND vigente = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $idRegion);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $region = $result->fetch_assoc();
                        $stmt->close();
                        
                        // Obtener nombre del país para mostrarlo
                        $sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ?";
                        $stmtPais = $conn->prepare($sqlPais);
                        $stmtPais->bind_param("i", $region['idPais']);
                        $stmtPais->execute();
                        $pais = $stmtPais->get_result()->fetch_assoc();
                        $stmtPais->close();
                ?>
                <form action="acciones_region.php" method="POST">
                    <input type="hidden" name="idRegion" value="<?= $region['idRegion'] ?>">
                    
                    <div class="mb-3">
                        <label>País</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($pais['nombrePais']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Nombre de la Región</label>
                        <input type="text" class="form-control" name="nombreRegion" value="<?= htmlspecialchars($region['nombreRegion']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Código de la Región</label>
                        <input type="text" class="form-control" name="codRegion" value="<?= htmlspecialchars($region['codRegion']) ?>" required>
                    </div>
                    <button type="submit" name="update_region" class="btn btn-primary">Actualizar</button>
                </form>
                <?php
                    } else {
                        echo '<h5>Región no encontrada</h5>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>