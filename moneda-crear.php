<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Moneda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>

        <div class="card">
            <div class="card-header">
                <h4>Agregar Moneda
                    <a href="menu.php?page=inicio_moneda.php" class="btn btn-danger float-end">
                        <span class="bi bi-arrow-left"></span>&nbsp;Volver
                    </a>
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="acciones-moneda.php">
                    <input type="hidden" name="create_moneda" value="1">

                    <div class="mb-3">
                        <label class="fw-bold">Código de Moneda</label>
                        <input type="text" class="form-control" name="codMoneda" maxlength="10" 
                            placeholder="Ej: JPY" autocomplete="off" required>
                        <small class="text-muted">Código de la moneda (ej: USD, EUR, JPY)</small>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nombre de la Moneda</label>
                        <input type="text" class="form-control" name="nombreMoneda" 
                            placeholder="Ej: Yen Japonés" autocomplete="off" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Símbolo de la Moneda</label>
                        <input type="text" class="form-control" name="simbolo" 
                            placeholder="Ej: ¥" value="$" maxlength="10" autocomplete="off" required>
                    </div>

                    <hr>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-check-lg"></i> Crear Moneda
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>