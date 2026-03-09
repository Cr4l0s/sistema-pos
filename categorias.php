<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="components/tabla_pro/tabla_pro.css">
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            padding: 1rem 1.25rem;
        }

        .card-header h4 {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .header-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-shrink: 0;
        }

        .header-controls input,
        .header-controls select {
            width: 200px;
        }

        @media (max-width: 992px) {

            .header-controls input,
            .header-controls select {
                width: 150px;
            }
        }

        @media (max-width: 768px) {
            .card-header {
                flex-wrap: wrap;
                gap: 10px;
            }

            .header-controls {
                width: auto;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .header-controls input,
            .header-controls select {
                width: 180px;
            }
        }

        @media (max-width: 576px) {
            .card-header {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .header-controls {
                width: 100%;
                justify-content: space-between;
            }

            .header-controls input,
            .header-controls select {
                width: 48%;
            }
        }

        .btn-agregar {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<!-- Librerías para PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Listado de Categorías</h4>
                <div class="header-controls">
                    <input type="text" id="buscarTabla" class="form-control" placeholder="Buscar...">
                    <select id="filasTabla" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <?php 
                require_once 'components/tabla_pro/tabla_pro.php';
                
                $columnas = [
                    'nombre_categoria' => 'Nombre',
                    'descripcion' => 'Descripción'
                ];
                
                tablaPro("components/tabla_pro/tabla_endpoint_categorias.php", 'nombre_categoria', $columnas); 
                ?>

                <!-- BOTÓN AGREGAR -->
                <div class="btn-agregar">
                    <a href="categoria-crear.php" class="btn btn-primary">
                        <span class="bi bi-plus-circle-fill"></span> Agregar Categoría
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="components/tabla_pro/tabla_pro.js"></script>
</body>

</html>