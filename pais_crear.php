<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Agregar País
                    <a href="inicio_pais.php" class="btn btn-danger float-end">Volver</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="acciones-pais.php" method="POST">
                    <div class="mb-3">
                        <label>Sigla del País</label>
                        <input type="text" class="form-control" name="siglaPais" pattern=".{2,}" title="La sigla debe tener al menos 2 caracteres" required>
                    </div>
                    <div class="mb-3">
                        <label>Código de la Moneda</label>
                        <input type="text" class="form-control" name="codMoneda" pattern=".{3,}" title="El código debe tener 3 caracteres" required>
                    </div>
                    <div class="mb-3">
                        <label>Nombre del País</label>
                        <input type="text" class="form-control" name="nombrePais" pattern=".{3,}" title="El nombre debe tener al menos 3 caracteres" required>
                    </div>
                    <button type="submit" name="create_pais" class="btn btn-primary">Grabar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>