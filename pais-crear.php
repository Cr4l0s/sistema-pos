<?php
header('Content-Type: text/html; charset=utf-8');
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
    <title>Crear País</title>
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
                        <h4>Agregar País
                            <a href="menu.php?page=inicio_pais.php" class="btn btn-danger float-end">
                                <span class="bi bi-arrow-left"></span>&nbsp;Volver
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="acciones-pais.php">
                            <input type="hidden" name="create_pais" value="1">

                            <!-- Datos del País -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="fw-bold">Sigla del País</label>
                                    <input type="text" class="form-control" name="siglaPais" maxlength="3" 
                                        placeholder="Ej: JPN" autocomplete="off" required>
                                    <small class="text-muted">Código de 3 letras (ISO 3166-1 alfa-3)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-bold">Nombre del País</label>
                                    <input type="text" class="form-control" name="nombrePais" 
                                        placeholder="Ej: Japón" autocomplete="off" required>
                                </div>
                            </div>

                            <!-- Datos de la Moneda -->
                            <h5 class="mb-3"><i class="bi bi-cash-coin"></i> Moneda del País</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="fw-bold">Código de Moneda</label>
                                    <input type="text" class="form-control" name="codMoneda" 
                                        placeholder="Ej: JPY" maxlength="10" autocomplete="off" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold">Nombre de la Moneda</label>
                                    <input type="text" class="form-control" name="nombreMoneda" 
                                        placeholder="Ej: Yen Japonés" autocomplete="off" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-bold">Símbolo de la Moneda</label>
                                    <input type="text" class="form-control" name="simbolo_moneda" 
                                        placeholder="Ej: ¥" value="$" autocomplete="off" required>
                                </div>
                            </div>

                            <hr>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-lg"></i> Crear País
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>