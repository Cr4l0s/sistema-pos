<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Editar</title>
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
                        <h4>Editar Categoría
                            <a href="categorias.php" class="btn btn-danger float-end"><span class="bi bi-arrow-left"></span>&nbsp;Volver</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                            if (isset($_GET['id'])) {
                                $id = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM categorias WHERE id_categoria = '$id' AND activo = 1";
                                $query = mysqli_query($conn, $sql);

                                if(mysqli_num_rows($query) > 0) {
                                    $categoria = mysqli_fetch_array($query);
                        ?>        
                        <form action="categoria-acciones.php" method="POST">
                            <input type="hidden" name="categoria_id" value="<?php echo $categoria['id_categoria']; ?>">
                            
                            <div class="mb-3">
                                <label>Nombre de la Categoría</label>
                                <input type="text" name="nombre_categoria" value="<?php echo $categoria['nombre_categoria']; ?>" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="3"><?php echo $categoria['descripcion']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <button type="submit" name="update_categoria" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                        <?php
                                } else {
                                    echo "<h5>Categoría no encontrada</h5>";
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