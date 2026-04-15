<?php
header('Content-Type: text/html; charset=utf-8');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Obtener todas las monedas disponibles
$monedas = $conn->query("SELECT * FROM monedas WHERE vigente = 1 ORDER BY codMoneda ASC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear País</title>
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

        #listaMonedasTemp {
            min-height: 100px;
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

                        <!-- DATOS BÁSICOS DEL PAÍS CON AYUDA DE SELECTORES -->
                        <div class="buscador-card">
                            <h5 class="mb-3"><i class="bi bi-globe"></i> Datos del País</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Sigla del País</label>
                                    <input type="text" class="form-control" id="siglaInput" maxlength="3"
                                        placeholder="Ej: CL" required>
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
                                    <input type="text" class="form-control" id="nombreInput" placeholder="Ej: Chile"
                                        required>
                                    <div class="ayuda-selector">
                                        <i class="bi bi-search"></i> Buscar por nombre:
                                        <select class="form-control form-control-sm mt-1" id="buscadorNombre"
                                            style="width: 100%;">
                                            <option value="">-- Seleccionar nombre --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GESTIÓN DE MONEDAS -->
                        <div class="buscador-card">
                            <h5 class="mb-3"><i class="bi bi-cash-coin"></i> Monedas del País</h5>

                            <!-- Lista de monedas temporales -->
                            <div id="listaMonedasTemp" class="mb-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Agrega monedas usando el buscador (mínimo 2
                                    caracteres).
                                </div>
                            </div>

                            <!-- Agregar nueva moneda -->
                            <div class="row g-2">
                                <div class="col-md-9">
                                    <select class="form-control" id="nuevaMoneda" style="width: 100%;">
                                        <option value="">-- Buscar moneda (mínimo 2 caracteres) --</option>
                                        <?php if ($monedas && $monedas->num_rows > 0): ?>
                                            <?php while ($moneda = $monedas->fetch_assoc()): ?>
                                                <option value="<?= $moneda['idMoneda'] ?>"
                                                    data-codigo="<?= htmlspecialchars($moneda['codMoneda'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-nombre="<?= htmlspecialchars($moneda['nombreMoneda'], ENT_QUOTES, 'UTF-8') ?>"
                                                    data-simbolo="<?= htmlentities($moneda['simbolo'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= htmlspecialchars($moneda['codMoneda'], ENT_QUOTES, 'UTF-8') ?> -
                                                    <?= htmlspecialchars($moneda['nombreMoneda'], ENT_QUOTES, 'UTF-8') ?>
                                                    (<?= htmlentities($moneda['simbolo'], ENT_QUOTES, 'UTF-8') ?>)
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
                                <i class="bi bi-info-circle"></i> La primera moneda que agregues será la principal.
                            </small>
                        </div>

                        <hr>

                        <!-- Botón Crear -->
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg px-5" id="btnCrear"
                                onclick="crearPais()" disabled>
                                <i class="bi bi-check-lg"></i> Crear País
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Base de datos de países (TODOS los países existentes)
        const countryDatabase = [
            <?php
            $todos_paises = $conn->query("SELECT siglaPais, nombrePais FROM paises WHERE vigente = 1 ORDER BY nombrePais");
            while ($p = $todos_paises->fetch_assoc()) {
                $sigla = htmlspecialchars($p['siglaPais'], ENT_QUOTES, 'UTF-8');
                $nombre = htmlspecialchars($p['nombrePais'], ENT_QUOTES, 'UTF-8');
                echo "{ code: '{$sigla}', name: '{$nombre}' },\n";
            }
            ?>
        ];

        console.log('CountryDatabase cargado:', countryDatabase);

        // Variables globales
        let monedasSeleccionadas = [];

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

        // Función para actualizar SOLO el campo seleccionado
        function actualizarSigla(code) {
            if (code) {
                document.getElementById('siglaInput').value = code;

                const buscadorCard = document.querySelector('.buscador-card');
                if (buscadorCard) {
                    buscadorCard.classList.add('border', 'border-success');
                    setTimeout(function () {
                        buscadorCard.classList.remove('border', 'border-success');
                    }, 500);
                }
            }
            habilitarBotonCrear();
        }

        function actualizarNombre(name) {
            if (name) {
                document.getElementById('nombreInput').value = name;

                const buscadorCard = document.querySelector('.buscador-card');
                if (buscadorCard) {
                    buscadorCard.classList.add('border', 'border-success');
                    setTimeout(function () {
                        buscadorCard.classList.remove('border', 'border-success');
                    }, 500);
                }
            }
            habilitarBotonCrear();
        }

        $(document).ready(function () {
            console.log('Document ready - Inicializando selectores');

            $('#buscadorSigla').empty().append('<option value="">-- Seleccionar sigla --</option>');
            $('#buscadorNombre').empty().append('<option value="">-- Seleccionar nombre --</option>');

            countryDatabase.forEach(function (country) {
                $('#buscadorSigla').append(
                    $('<option>', {
                        value: country.code,
                        text: country.code + ' - ' + country.name,
                        'data-code': country.code
                    })
                );
            });

            countryDatabase.forEach(function (country) {
                $('#buscadorNombre').append(
                    $('<option>', {
                        value: country.name,
                        text: country.name + ' (' + country.code + ')',
                        'data-name': country.name
                    })
                );
            });

            $('#buscadorSigla').select2({
                placeholder: 'Buscar por sigla (mínimo 2 caracteres)',
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: function () { return 'Ingresa al menos 2 caracteres'; },
                    searching: function () { return 'Buscando...'; },
                    noResults: function () { return 'No se encontraron países'; }
                }
            });

            $('#buscadorNombre').select2({
                placeholder: 'Buscar por nombre (mínimo 2 caracteres)',
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: function () { return 'Ingresa al menos 2 caracteres'; },
                    searching: function () { return 'Buscando...'; },
                    noResults: function () { return 'No se encontraron países'; }
                }
            });

            // SELECTOR DE MONEDAS CON MATCHER PERSONALIZADO
            $('#nuevaMoneda').select2({
                placeholder: 'Buscar moneda (mínimo 2 caracteres)',
                allowClear: true,
                minimumInputLength: 2,
                matcher: matcherCustom,
                language: {
                    inputTooShort: function () { return 'Ingresa al menos 2 caracteres'; },
                    searching: function () { return 'Buscando...'; },
                    noResults: function () { return 'No se encontraron monedas'; }
                }
            });

            $('#buscadorSigla').on('change', function () {
                const selectedCode = $(this).val();
                console.log('Sigla seleccionada:', selectedCode);
                if (selectedCode) {
                    actualizarSigla(selectedCode);
                }
            });

            $('#buscadorNombre').on('change', function () {
                const selectedName = $(this).val();
                console.log('Nombre seleccionado:', selectedName);
                if (selectedName) {
                    actualizarNombre(selectedName);
                }
            });

            $('#buscadorSigla, #buscadorNombre').on('select2:clear', function () {
                console.log('Selector limpiado');
            });

            $('#siglaInput, #nombreInput').on('input', function () {
                habilitarBotonCrear();
            });
        });

        function habilitarBotonCrear() {
            const tieneSigla = $('#siglaInput').val().trim().length > 0;
            const tieneNombre = $('#nombreInput').val().trim().length > 0;
            document.getElementById('btnCrear').disabled = !(tieneSigla && tieneNombre && monedasSeleccionadas.length > 0);
        }

        function agregarMoneda() {
            const select = document.getElementById('nuevaMoneda');
            const selectedOption = select.options[select.selectedIndex];

            if (!select.value) {
                alert('Seleccione una moneda');
                return;
            }

            if (monedasSeleccionadas.some(m => m.id == select.value)) {
                alert('Esta moneda ya está agregada');
                return;
            }

            const moneda = {
                id: select.value,
                codigo: selectedOption.dataset.codigo,
                nombre: selectedOption.dataset.nombre,
                simbolo: selectedOption.dataset.simbolo
            };

            monedasSeleccionadas.push(moneda);
            actualizarListaMonedas();
            $('#nuevaMoneda').val(null).trigger('change');
            habilitarBotonCrear();
        }

        function quitarMoneda(index) {
            monedasSeleccionadas.splice(index, 1);
            actualizarListaMonedas();
            habilitarBotonCrear();
        }

        function cambiarPrincipal(index) {
            const moneda = monedasSeleccionadas.splice(index, 1)[0];
            monedasSeleccionadas.unshift(moneda);
            actualizarListaMonedas();
        }

        function actualizarListaMonedas() {
            const container = document.getElementById('listaMonedasTemp');

            if (monedasSeleccionadas.length === 0) {
                container.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> Agrega monedas usando el buscador.</div>';
                return;
            }

            let html = '';
            monedasSeleccionadas.forEach((moneda, index) => {
                const esPrincipal = (index === 0);
                html += `
                    <div class="moneda-item ${esPrincipal ? 'moneda-principal' : ''}">
                        <div class="radio-principal">
                            <input type="radio" name="moneda_principal" 
                                   ${esPrincipal ? 'checked' : ''}
                                   onchange="cambiarPrincipal(${index})"
                                   class="form-check-input">
                        </div>
                        <div class="moneda-info">
                            <span class="moneda-codigo">${moneda.codigo}</span>
                            <span class="text-muted mx-2">-</span>
                            ${moneda.nombre}
                            <span class="moneda-simbolo">${moneda.simbolo}</span>
                            ${esPrincipal ? '<span class="badge-principal">Principal</span>' : ''}
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="quitarMoneda(${index})">
                                <i class="bi bi-trash"></i> Quitar
                            </button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }

        function crearPais() {
            const sigla = $('#siglaInput').val().trim().toUpperCase();
            const nombre = $('#nombreInput').val().trim();

            if (!sigla || !nombre) {
                alert('Debe completar la sigla y nombre del país');
                return;
            }

            if (monedasSeleccionadas.length === 0) {
                alert('Debe agregar al menos una moneda');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'acciones-pais.php';

            const inputAccion = document.createElement('input');
            inputAccion.type = 'hidden';
            inputAccion.name = 'create_pais';
            inputAccion.value = '1';
            form.appendChild(inputAccion);

            const inputSigla = document.createElement('input');
            inputSigla.type = 'hidden';
            inputSigla.name = 'siglaPais';
            inputSigla.value = sigla;
            form.appendChild(inputSigla);

            const inputNombre = document.createElement('input');
            inputNombre.type = 'hidden';
            inputNombre.name = 'nombrePais';
            inputNombre.value = nombre;
            form.appendChild(inputNombre);

            monedasSeleccionadas.forEach((moneda) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'monedas[]';
                input.value = moneda.id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>