<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Editar País
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
                <form action="acciones-pais.php" method="POST">
                    <input type="hidden" name="idPais" value="<?= $pais['idPais'] ?>">
                    
                    <div class="mb-3">
                        <label>Sigla del País</label>
                        <input type="text" class="form-control" name="siglaPais" value="<?= htmlspecialchars($pais['siglaPais']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Código de la Moneda</label>
                        <input type="text" class="form-control" name="codMoneda" value="<?= htmlspecialchars($pais['codMoneda']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Nombre del País</label>
                        <input type="text" class="form-control" name="nombrePais" value="<?= htmlspecialchars($pais['nombrePais']) ?>" required>
                    </div>
                    <button type="submit" name="update_pais" class="btn btn-primary">Actualizar</button>
                </form>
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