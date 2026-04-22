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

    // Usar prepared statements para seguridad
    $sqlPais = "SELECT nombrePais FROM paises WHERE idPais = ?";
    $stmt = $conn->prepare($sqlPais);
    $stmt->bind_param("i", $_SESSION['idPais']);
    $stmt->execute();
    $_SESSION['nombrePais'] = $stmt->get_result()->fetch_assoc()['nombrePais'];
    $stmt->close();

    $sqlRegion = "SELECT nombreRegion FROM regiones WHERE idRegion = ?";
    $stmt = $conn->prepare($sqlRegion);
    $stmt->bind_param("i", $_SESSION['idRegion']);
    $stmt->execute();
    $_SESSION['nombreRegion'] = $stmt->get_result()->fetch_assoc()['nombreRegion'];
    $stmt->close();
}

$idPais = $_SESSION['idPais'] ?? 1;
$nombrePais = $_SESSION['nombrePais'] ?? 'Desconocido';
$idRegion = $_SESSION['idRegion'] ?? 1;
$nombreRegion = $_SESSION['nombreRegion'] ?? 'Desconocido';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mantenedor de Ciudades</title>
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
<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Ciudades de la Región: [<?= htmlspecialchars($nombreRegion) ?>] del País:
                [<?= htmlspecialchars($nombrePais) ?>]</h3>
            <a href="menu.php?page=selector_pais_y_region.php" class="btn btn-danger">
                <span class="bi bi-arrow-left"></span> Volver
            </a>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="mb-0">Listado de Ciudades</h4>
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
                    'nombreCiudad' => 'Nombre de la Ciudad'
                ];
                
                tablaPro("components/tabla_pro/tabla_endpoint_ciudades.php", 'nombreCiudad', $columnas); 
                ?>

                <!-- Formulario para agregar -->
                <br>
                <h3>Agregar Nueva Ciudad</h3>
                <form method="POST" action="agregar_ciudad.php">
                    <input type="hidden" name="idRegion" value="<?= $idRegion ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="nombreCiudad" class="form-label">Nombre de la Ciudad:</label>
                            <input type="text" class="form-control" name="nombreCiudad" id="nombreCiudad"autocomplete="off" required>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="btn btn-primary">Agregar Ciudad</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- 🔴 NUEVO: Definir variables globales ANTES de tabla_pro.js -->
    <script>
        var globalIdPais = <?php echo $idPais; ?>;
        var globalIdRegion = <?php echo $idRegion; ?>;
        console.log("✅ Variables globales definidas:", {globalIdPais, globalIdRegion});
    </script>
    
    <script src="components/tabla_pro/tabla_pro.js"></script>
</body>

</html>
<?php ob_end_flush(); ?>