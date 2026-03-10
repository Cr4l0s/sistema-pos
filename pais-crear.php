<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="
        sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Agregar País
                            <a href="menu.php?page=inicio_pais.php" class="btn btn-danger float-end">
                                <span class="bi bi-arrow-left"></span>&nbsp;Volver
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="acciones-pais.php" method="POST">
                            <div class="mb-3">
                                <label>Sigla del País</label>
                                <input type="text" class="form-control" name="siglaPais" pattern=".{2,}"
                                    title="La sigla del País debe contener 2 caracteres o más."" required>
                                </div>
                                <div class=" mb-3">
                                <label>Código de la Moneda</label>
                                <input type="text" class="form-control" name="codMoneda" pattern=".{3,}"
                                    title="El Código de la Moneda debe contener 3 caracteres o más."" required>
                                </div>
                                <div class=" mb-3">
                                <label>Nombre del País</label>
                                <input type="text" class="form-control" name="nombrePais" pattern=".{3,}"
                                    title="El Nombre del País debe contener 3 caracteres o más."" required>
                                </div>
                                <div class=" mb-3">
                                <button type="submit" name="create_pais" class="btn btn-primary">Grabar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="
        sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>