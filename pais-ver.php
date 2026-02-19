<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ver País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Ver País
                    <a href="inicio_pais.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['idPais'])) {
                    $idPais = intval($_GET['idPais']);
                    
                    $sql = "SELECT * FROM paises WHERE idPais = ? AND vigente = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $idPais);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $pais = $result->fetch_assoc();
                        $stmt->close();
                ?>
                <div class="mb-3">
                    <label><b>ID</b></label>
                    <p class="form-control"><?= $pais['idPais'] ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Sigla</b></label>
                    <p class="form-control"><?= htmlspecialchars($pais['siglaPais']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Código Moneda</b></label>
                    <p class="form-control"><?= htmlspecialchars($pais['codMoneda']) ?></p>
                </div>
                <div class="mb-3">
                    <label><b>Nombre</b></label>
                    <p class="form-control"><?= htmlspecialchars($pais['nombrePais']) ?></p>
                </div>
                <?php
                    } else {
                        echo '<h5>País no encontrado</h5>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>