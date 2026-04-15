<?php
// ACTIVAR VISUALIZACIÓN DE ERRORES (QUITAR EN PRODUCCIÓN)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST o GET
$idPais = isset($_POST['idPais']) ? intval($_POST['idPais']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

// Depuración (quitar después)
echo "<!-- ID del país: " . $idPais . " -->";

if ($idPais == 0) {
    header('Location: inicio_pais.php');
    exit;
}

// Obtener datos del país
$sql = "SELECT * FROM paises WHERE idPais = ? AND vigente = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idPais);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'País no encontrado';
    header('Location: inicio_pais.php');
    exit;
}

$pais = $result->fetch_assoc();
$stmt->close();

// Obtener monedas del país
$monedas_pais = $conn->prepare("
    SELECT m.*, pm.es_principal 
    FROM monedas m
    JOIN paises_monedas pm ON m.idMoneda = pm.idMoneda
    WHERE pm.idPais = ?
    ORDER BY pm.es_principal DESC, m.codMoneda ASC
");
$monedas_pais->bind_param("i", $idPais);
$monedas_pais->execute();
$monedas_pais_result = $monedas_pais->get_result();

// Obtener IDs de monedas que ya tiene el país (para excluirlas del selector)
$ids_excluir = []; // ¡CORREGIDO: antes era $_ids_excluir!
$monedas_pais->data_seek(0);
while ($row = $monedas_pais_result->fetch_assoc()) {
    $ids_excluir[] = $row['idMoneda'];
}
$monedas_pais_result->data_seek(0);

// Obtener todas las monedas disponibles (excluyendo las que ya tiene)
$sql_monedas = "SELECT * FROM monedas WHERE vigente = 1";
if (!empty($ids_excluir)) {
    $excluir = implode(',', $ids_excluir); // ¡AHORA $ids_excluir es un array válido!
    $sql_monedas .= " AND idMoneda NOT IN ($excluir)";
}
$sql_monedas .= " ORDER BY codMoneda ASC";
$todas_monedas = $conn->query($sql_monedas);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar País</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }

        .buscador-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .moneda-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #ffffff;
            transition: all 0.2s;
        }

        .moneda-item:hover {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .moneda-principal {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .radio-principal {
            margin-right: 20px;
        }

        .moneda-info {
            flex-grow: 1;
        }

        .moneda-codigo {
            font-weight: bold;
            font-size: 1.1em;
        }

        .moneda-simbolo {
            font-size: 1.2em;
            margin-left: 10px;
            color: #28a745;
        }

        .badge-principal {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            margin-left: 10px;
        }

        .pais-actual-badge {
            background-color: #17a2b8;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-left: 10px;
        }

        .ayuda-selector {
            margin-top: 5px;
            font-size: 0.85em;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square"></i> Editar País
                    <span class="pais-actual-badge">
                        <?= htmlspecialchars($pais['siglaPais'] ?? '', ENT_QUOTES, 'UTF-8') ?> -
                        <?= htmlspecialchars($pais['nombrePais'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </h4>
                <a href="menu.php?page=inicio_pais.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                <!-- DATOS BÁSICOS DEL PAÍS -->
                <div class="buscador-card">
                    <h5 class="mb-3"><i class="bi bi-pencil"></i> Editar datos actuales</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Sigla del País</label>
                            <input type="text" class="form-control" id="siglaInput" name="siglaPais"
                                value="<?= htmlspecialchars($pais['siglaPais'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                maxlength="3" placeholder="Ej: CL" required>
                            <div class="ayuda-selector">
                                <i class="bi bi-search"></i> Buscar por sigla:
                                <select class="form-control form-control-sm mt-1" id="buscadorSigla"
                                    style="width: 100%;">
                                    <option value="">-- Seleccionar sigla --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Nombre del País</label>
                            <input type="text" class="form-control" id="nombreInput" name="nombrePais"
                                value="<?= htmlspecialchars($pais['nombrePais'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Ej: Chile" required>
                            <div class="ayuda-selector">
                                <i class="bi bi-search"></i> Buscar por nombre:
                                <select class="form-control form-control-sm mt-1" id="buscadorNombre"
                                    style="width: 100%;">
                                    <option value="">-- Seleccionar nombre --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <form method="POST" action="acciones-pais.php">
                            <input type="hidden" name="editar_pais" value="1">
                            <input type="hidden" name="idPais" value="<?= $idPais ?>">
                            <input type="hidden" name="siglaPais" id="siglaHidden"
                                value="<?= htmlspecialchars($pais['siglaPais'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="nombrePais" id="nombreHidden"
                                value="<?= htmlspecialchars($pais['nombrePais'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Actualizar datos
                            </button>
                        </form>
                    </div>
                </div>

                <!-- GESTIÓN DE MONEDAS -->
                <div class="buscador-card">
                    <h5 class="mb-3"><i class="bi bi-cash-coin"></i> Monedas del País</h5>
                    <div id="listaMonedas" class="mb-4">
                        <?php if ($monedas_pais_result->num_rows == 0): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Este país no tiene monedas asignadas.
                            </div>
                        <?php endif; ?>
                        <?php while ($moneda = $monedas_pais_result->fetch_assoc()): ?>
                            <div class="moneda-item <?= $moneda['es_principal'] ? 'moneda-principal' : '' ?>"
                                data-idmoneda="<?= $moneda['idMoneda'] ?>">
                                <div class="radio-principal">
                                    <input type="radio" name="moneda_principal" value="<?= $moneda['idMoneda'] ?>"
                                        <?= $moneda['es_principal'] ? 'checked' : '' ?>
                                        onchange="cambiarPrincipal(<?= $moneda['idMoneda'] ?>)" class="form-check-input">
                                </div>
                                <div class="moneda-info">
                                    <span
                                        class="moneda-codigo"><?= htmlspecialchars($moneda['codMoneda'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="text-muted mx-2">-</span>
                                    <?= htmlspecialchars($moneda['nombreMoneda'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                    <span
                                        class="moneda-simbolo"><?= htmlentities($moneda['simbolo'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if ($moneda['es_principal']): ?>
                                        <span class="badge-principal">Principal</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php if (!$moneda['es_principal']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="eliminarMoneda(<?= $moneda['idMoneda'] ?>)">
                                            <i class="bi bi-trash"></i> Quitar
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">Moneda principal</span>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Agregar nueva moneda -->
                    <div class="row g-2">
                        <div class="col-md-9">
                            <select class="form-control" id="nuevaMoneda" style="width: 100%;">
                                <option value="">-- Buscar moneda (mínimo 2 caracteres) --</option>
                                <?php if ($todas_monedas && $todas_monedas->num_rows > 0): ?>
                                    <?php while ($moneda = $todas_monedas->fetch_assoc()): ?>
                                        <option value="<?= $moneda['idMoneda'] ?>"
                                            data-codigo="<?= htmlspecialchars($moneda['codMoneda'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-nombre="<?= htmlspecialchars($moneda['nombreMoneda'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                            data-simbolo="<?= htmlentities($moneda['simbolo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($moneda['codMoneda'] ?? '', ENT_QUOTES, 'UTF-8') ?> -
                                            <?= htmlspecialchars($moneda['nombreMoneda'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            (<?= htmlentities($moneda['simbolo'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                                        </option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay monedas disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success w-100 h-100" onclick="agregarMoneda()">
                                <i class="bi bi-plus-circle"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="bi bi-info-circle"></i> La moneda marcada como principal será la que se muestre en el
                        listado.
                    </small>
                </div>
                <hr>
                <div class="text-center">
                    <a href="menu.php?page=inicio_pais.php" class="btn btn-secondary btn-lg px-5">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Base de datos de países (excluyendo el país actual)
        const countryDatabase = [
            <?php
            $todos_paises = $conn->query("SELECT idPais, siglaPais, nombrePais FROM paises WHERE vigente = 1 AND idPais != $idPais ORDER BY nombrePais");
            while ($p = $todos_paises->fetch_assoc()) {
                $sigla = htmlspecialchars($p['siglaPais'] ?? '', ENT_QUOTES, 'UTF-8');
                $nombre = htmlspecialchars($p['nombrePais'] ?? '', ENT_QUOTES, 'UTF-8');
                echo "{ id: '{$p['idPais']}', code: '{$sigla}', name: '{$nombre}' },\n";
            }
            ?>
        ];

        // Función matcher personalizada MEJORADA (ignora acentos)
        function matcherCustom(params, data) {
            if ($.trim(params.term) === '') return data;

            // Normalizar término de búsqueda (quitar acentos)
            var term = params.term.toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            // Normalizar texto visible
            var text = data.text.toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            // Obtener y normalizar datos adicionales
            var codigo = $(data.element).data('codigo') || '';
            var nombre = $(data.element).data('nombre') || '';

            codigo = codigo.toLowerCase();
            nombre = nombre.toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            // Buscar en texto, código y nombre (todos normalizados)
            if (text.indexOf(term) > -1 ||
                codigo.indexOf(term) > -1 ||
                nombre.indexOf(term) > -1) {
                return data;
            }
            return null;
        }

        function actualizarSigla(code) {
            if (code) {
                document.getElementById('siglaInput').value = code;
                document.getElementById('siglaHidden').value = code;
                const buscadorCard = document.querySelector('.buscador-card');
                if (buscadorCard) {
                    buscadorCard.classList.add('border', 'border-success');
                    setTimeout(() => buscadorCard.classList.remove('border', 'border-success'), 500);
                }
            }
        }

        function actualizarNombre(name) {
            if (name) {
                document.getElementById('nombreInput').value = name;
                document.getElementById('nombreHidden').value = name;
                const buscadorCard = document.querySelector('.buscador-card');
                if (buscadorCard) {
                    buscadorCard.classList.add('border', 'border-success');
                    setTimeout(() => buscadorCard.classList.remove('border', 'border-success'), 500);
                }
            }
        }

        $(document).ready(function () {
            $('#siglaHidden').val($('#siglaInput').val());
            $('#nombreHidden').val($('#nombreInput').val());

            $('#buscadorSigla').empty().append('<option value="">-- Seleccionar sigla --</option>');
            $('#buscadorNombre').empty().append('<option value="">-- Seleccionar nombre --</option>');

            countryDatabase.forEach(function (country) {
                $('#buscadorSigla').append($('<option>', {
                    value: country.code,
                    text: country.code + ' - ' + country.name,
                    'data-code': country.code
                }));
            });

            countryDatabase.forEach(function (country) {
                $('#buscadorNombre').append($('<option>', {
                    value: country.name,
                    text: country.name + ' (' + country.code + ')',
                    'data-name': country.name
                }));
            });

            $('#buscadorSigla').select2({
                placeholder: 'Buscar por sigla (mínimo 2 caracteres)',
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: () => 'Ingresa al menos 2 caracteres',
                    searching: () => 'Buscando...',
                    noResults: () => 'No se encontraron países'
                }
            });

            $('#buscadorNombre').select2({
                placeholder: 'Buscar por nombre (mínimo 2 caracteres)',
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: () => 'Ingresa al menos 2 caracteres',
                    searching: () => 'Buscando...',
                    noResults: () => 'No se encontraron países'
                }
            });

            $('#nuevaMoneda').select2({
                placeholder: 'Buscar moneda (mínimo 2 caracteres)',
                allowClear: true,
                minimumInputLength: 2,
                matcher: matcherCustom,
                language: {
                    inputTooShort: () => 'Ingresa al menos 2 caracteres',
                    searching: () => 'Buscando...',
                    noResults: () => 'No se encontraron monedas'
                }
            });

            $('#buscadorSigla').on('change', function () {
                const selectedCode = $(this).val();
                if (selectedCode) actualizarSigla(selectedCode);
            });

            $('#buscadorNombre').on('change', function () {
                const selectedName = $(this).val();
                if (selectedName) actualizarNombre(selectedName);
            });

            $('#siglaInput, #nombreInput').on('input', function () {
                $('#siglaHidden').val($('#siglaInput').val());
                $('#nombreHidden').val($('#nombreInput').val());
            });
        });

        function agregarMoneda() {
            const select = document.getElementById('nuevaMoneda');
            if (!select.value) {
                alert('Seleccione una moneda');
                return;
            }
            fetch('ajax_agregar_moneda.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'idPais=<?= $idPais ?>&idMoneda=' + select.value
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Error: ' + data.error);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }

        function eliminarMoneda(idMoneda) {
            if (confirm('¿Está seguro de eliminar esta moneda del país?')) {
                fetch('ajax_eliminar_moneda.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'idPais=<?= $idPais ?>&idMoneda=' + idMoneda
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert('Error: ' + data.error);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error de conexión');
                    });
            }
        }

        function cambiarPrincipal(idMoneda) {
            fetch('ajax_cambiar_principal.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'idPais=<?= $idPais ?>&idMoneda=' + idMoneda
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Error: ' + data.error);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        }
    </script>
</body>

</html>