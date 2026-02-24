<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Comuna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Editar Comuna
                    <a href="gestionar_comunas.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_GET['idComuna'])) {
                    $idComuna = intval($_GET['idComuna']);

                    $sql = "SELECT * FROM comunas WHERE idComuna = ? AND vigente = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $idComuna);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $comuna = $result->fetch_assoc();
                        $stmt->close();
                ?>
                <form action="acciones_comuna.php" method="POST">
                    <input type="hidden" name="idComuna" value="<?= $comuna['idComuna'] ?>">
                    <div class="mb-3">
                        <label for="nomComuna" class="form-label">Nombre de la Comuna</label>
                        <input type="text" class="form-control" name="nomComuna" id="nomComuna" value="<?= htmlspecialchars($comuna['nomComuna']) ?>" required>
                    </div>
                    <button type="submit" name="update_comuna" class="btn btn-primary">Actualizar</button>
                </form>
                <?php
                    } else {
                        echo '<h5>Comuna no encontrada</h5>';
                    }
                } else {
                    echo '<h5>ID de comuna no proporcionado.</h5>';
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>