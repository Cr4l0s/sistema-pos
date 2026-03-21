<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Crear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Agregar Categoría
                            <a href="menu.php?page=categorias.php" class="btn btn-danger float-end">
                                <span class="bi bi-arrow-left"></span>&nbsp;Volver
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="categoria-acciones.php" method="POST" onsubmit="return validarFormularioCategoria()">
                            <div class="mb-3">
                                <label>Nombre de la Categoría <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre_categoria" id="nombre_categoria" required>
                                <small class="text-muted">Solo letras, números y espacios</small>
                            </div>
                            
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" name="create_categoria" class="btn btn-primary">Grabar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validaciones.js"></script>
    <script>
        function validarFormularioCategoria() {
            const nombre = document.getElementById('nombre_categoria')?.value;
            
            if (!Validaciones.required(nombre)) {
                alert('El nombre de la categoría es obligatorio');
                return false;
            }
            
            return true;
        }
    </script>
</body>

</html>