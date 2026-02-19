<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Ciudad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Editar Ciudad
                    <a href="gestionar_ciudades.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['idCiudad'])) {
                    $idCiudad = intval($_GET['idCiudad']);
                    
                    $sql = "SELECT * FROM ciudades WHERE idCiudad = ? AND vigente = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $idCiudad);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $ciudad = $result->fetch_assoc();
                        $stmt->close();
                ?>
                <form action="acciones_ciudad.php" method="POST">
                    <input type="hidden" name="idCiudad" value="<?= $ciudad['idCiudad'] ?>">
                    
                    <div class="mb-3">
                        <label>Nombre de la Ciudad</label>
                        <input type="text" class="form-control" name="nombreCiudad" value="<?= htmlspecialchars($ciudad['nombreCiudad']) ?>" required>
                    </div>
                    <button type="submit" name="update_ciudad" class="btn btn-primary">Actualizar</button>
                </form>
                <?php
                    } else {
                        echo '<h5>Ciudad no encontrada</h5>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>