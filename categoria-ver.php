<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Ver Categoría
                            <a href="categorias.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                            
                            $sql = "SELECT * FROM categorias WHERE id_categoria = ? AND activo = 1";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $categoria = $result->fetch_assoc();
                                $stmt->close();
                        ?>
                        <div class="mb-3">
                            <label><b>ID</b></label>
                            <p class="form-control"><?php echo $categoria['id_categoria']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Nombre</b></label>
                            <p class="form-control"><?php echo $categoria['nombre_categoria']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label><b>Descripción</b></label>
                            <p class="form-control"><?php echo $categoria['descripcion']; ?></p>
                        </div>
                        <?php
                            } else {
                                echo '<h5>No se encontró la categoría</h5>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>