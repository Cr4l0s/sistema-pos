<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['idPais'] = intval($_POST['idPais']);
    $_SESSION['idRegion'] = intval($_POST['idRegion']);
    $_SESSION['idCiudad'] = intval($_POST['idCiudad']);

    // Usar prepared statements para seguridad
    $sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ?";
    $stmt = $conn->prepare($sqlPais);
    $stmt->bind_param("i", $_SESSION['idPais']);
    $stmt->execute();
    $_SESSION['nombrePais'] = $stmt->get_result()->fetch_assoc()['nombrePais'] ?? 'Desconocido';
    $stmt->close();

    $sqlRegion = "SELECT nombreRegion FROM regiones WHERE idRegion = ?";
    $stmt = $conn->prepare($sqlRegion);
    $stmt->bind_param("i", $_SESSION['idRegion']);
    $stmt->execute();
    $_SESSION['nombreRegion'] = $stmt->get_result()->fetch_assoc()['nombreRegion'] ?? 'Desconocido';
    $stmt->close();

    $sqlCiudad = "SELECT nombreCiudad FROM ciudades WHERE idCiudad = ?";
    $stmt = $conn->prepare($sqlCiudad);
    $stmt->bind_param("i", $_SESSION['idCiudad']);
    $stmt->execute();
    $_SESSION['nombreCiudad'] = $stmt->get_result()->fetch_assoc()['nombreCiudad'] ?? 'Desconocido';
    $stmt->close();
}

$idPais = $_SESSION['idPais'] ?? 1;
$nombrePais = $_SESSION['nombrePais'] ?? 'Desconocido';
$idRegion = $_SESSION['idRegion'] ?? 1;
$nombreRegion = $_SESSION['nombreRegion'] ?? 'Desconocido';
$idCiudad = $_SESSION['idCiudad'] ?? 1;
$nombreCiudad = $_SESSION['nombreCiudad'] ?? 'Desconocido';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mantenedor de Comunas</title>
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
    </style>

    <!-- Librerías para PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Comunas de la Ciudad: [<?= htmlspecialchars($nombreCiudad) ?>] en
                [<?= htmlspecialchars($nombreRegion) ?>, <?= htmlspecialchars($nombrePais) ?>]</h3>
            <a href="menu.php?page=selector_pais_region_ciudad.php" class="btn btn-danger">
                <span class="bi bi-arrow-left"></span> Volver
            </a>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="mb-0">Listado de Comunas</h4>
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
                    'nombreComuna' => 'Nombre de la Comuna'
                ];
                
                tablaPro("components/tabla_pro/tabla_endpoint_comunas.php", 'nombreComuna', $columnas); 
                ?>

                <!-- Formulario para agregar -->
                <br>
                <h3>Agregar Nueva Comuna</h3>
                <form method="POST" action="agregar_comuna.php">
                    <input type="hidden" name="idCiudad" value="<?= $idCiudad ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="nombreComuna" class="form-label">Nombre de la Comuna:</label>
                            <input type="text" class="form-control" name="nombreComuna" id="nombreComuna" required>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary">Agregar Comuna</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="components/tabla_pro/tabla_pro.js"></script>
</body>

</html>
<?php ob_end_flush(); ?>