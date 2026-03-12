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
    </style>
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
                        
                        <!-- BUSCADOR DE MONEDAS (ÚNICO CAMPO PRINCIPAL) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">🔍 Moneda</label>
                            <select class="form-control" id="currencySearch" name="moneda_seleccionada" style="width: 100%;" required>
                                <option value="">-- Busca y selecciona una moneda --</option>
                            </select>
                            <small class="text-muted">Busca por nombre de moneda (ej: Dólar, Euro, Peso) o por país</small>
                        </div>

                        <hr>

                        <form action="acciones-pais.php" method="POST" id="paisForm">
                            <!-- Campos ocultos que se llenarán con JS -->
                            <input type="hidden" name="siglaPais" id="siglaPais">
                            <input type="hidden" name="codMoneda" id="codMoneda">
                            <input type="hidden" name="simbolo_moneda" id="simboloMoneda">
                            <input type="hidden" name="nombrePais" id="nombrePais">
                            
                            <!-- Vista previa de los datos -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold">País:</label>
                                        <p class="form-control-static" id="vistaPais">—</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="fw-bold">Sigla:</label>
                                        <p class="form-control-static" id="vistaSigla">—</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="fw-bold">Moneda:</label>
                                        <p class="form-control-static" id="vistaMoneda">—</p>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" name="create_pais" class="btn btn-primary" id="btnGrabar" disabled>Grabar</button>
                        </form>
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
    // Base de datos de monedas (completa)
    const currencyDatabase = [
        // América
        { symbol: '$', code: 'USD', name: 'Dólar estadounidense', countries: ['Estados Unidos', 'Ecuador', 'El Salvador'] },
        { symbol: 'C$', code: 'CAD', name: 'Dólar canadiense', countries: ['Canadá'] },
        { symbol: 'R$', code: 'BRL', name: 'Real brasileño', countries: ['Brasil'] },
        { symbol: '$', code: 'MXN', name: 'Peso mexicano', countries: ['México'] },
        { symbol: '$', code: 'ARS', name: 'Peso argentino', countries: ['Argentina'] },
        { symbol: '$', code: 'COP', name: 'Peso colombiano', countries: ['Colombia'] },
        { symbol: '$', code: 'CLP', name: 'Peso chileno', countries: ['Chile'] },
        { symbol: 'Bs', code: 'BOB', name: 'Boliviano', countries: ['Bolivia'] },
        { symbol: 'S/', code: 'PEN', name: 'Sol peruano', countries: ['Perú'] },
        { symbol: '₲', code: 'PYG', name: 'Guaraní paraguayo', countries: ['Paraguay'] },
        { symbol: '$', code: 'UYU', name: 'Peso uruguayo', countries: ['Uruguay'] },
        { symbol: 'Bs.S.', code: 'VES', name: 'Bolívar soberano', countries: ['Venezuela'] },
        { symbol: '₡', code: 'CRC', name: 'Colón costarricense', countries: ['Costa Rica'] },
        { symbol: 'L', code: 'HNL', name: 'Lempira hondureña', countries: ['Honduras'] },
        
        // Europa
        { symbol: '€', code: 'EUR', name: 'Euro', countries: ['Alemania', 'Francia', 'Italia', 'España', 'Países Bajos', 'Bélgica', 'Austria', 'Portugal', 'Grecia', 'Irlanda', 'Finlandia', 'Eslovaquia', 'Eslovenia', 'Lituania', 'Letonia', 'Estonia', 'Croacia', 'Luxemburgo', 'Chipre', 'Malta'] },
        { symbol: '£', code: 'GBP', name: 'Libra esterlina', countries: ['Reino Unido'] },
        { symbol: 'Fr', code: 'CHF', name: 'Franco suizo', countries: ['Suiza', 'Liechtenstein'] },
        { symbol: '₽', code: 'RUB', name: 'Rublo ruso', countries: ['Rusia'] },
        { symbol: 'kr', code: 'SEK', name: 'Corona sueca', countries: ['Suecia'] },
        { symbol: 'kr', code: 'NOK', name: 'Corona noruega', countries: ['Noruega'] },
        { symbol: 'kr', code: 'DKK', name: 'Corona danesa', countries: ['Dinamarca'] },
        { symbol: 'zł', code: 'PLN', name: 'Złoty polaco', countries: ['Polonia'] },
        { symbol: 'Kč', code: 'CZK', name: 'Corona checa', countries: ['República Checa'] },
        { symbol: 'Ft', code: 'HUF', name: 'Florín húngaro', countries: ['Hungría'] },
        { symbol: 'лв', code: 'BGN', name: 'Lev búlgaro', countries: ['Bulgaria'] },
        { symbol: 'lei', code: 'RON', name: 'Leu rumano', countries: ['Rumania'] },
        
        // Asia
        { symbol: '¥', code: 'CNY', name: 'Yuan chino', countries: ['China'] },
        { symbol: '¥', code: 'JPY', name: 'Yen japonés', countries: ['Japón'] },
        { symbol: '₩', code: 'KRW', name: 'Won surcoreano', countries: ['Corea del Sur'] },
        { symbol: '₹', code: 'INR', name: 'Rupia india', countries: ['India'] },
        { symbol: 'HK$', code: 'HKD', name: 'Dólar hongkonés', countries: ['Hong Kong'] },
        { symbol: 'S$', code: 'SGD', name: 'Dólar de Singapur', countries: ['Singapur'] },
        { symbol: 'NT$', code: 'TWD', name: 'Nuevo dólar taiwanés', countries: ['Taiwán'] },
        { symbol: 'Rp', code: 'IDR', name: 'Rupia indonesia', countries: ['Indonesia'] },
        { symbol: 'RM', code: 'MYR', name: 'Ringgit malasio', countries: ['Malasia'] },
        { symbol: '₱', code: 'PHP', name: 'Peso filipino', countries: ['Filipinas'] },
        { symbol: '฿', code: 'THB', name: 'Baht tailandés', countries: ['Tailandia'] },
        { symbol: '₫', code: 'VND', name: 'Dong vietnamita', countries: ['Vietnam'] },
        { symbol: '₸', code: 'KZT', name: 'Tenge kazajo', countries: ['Kazajistán'] },
        { symbol: '؋', code: 'AFN', name: 'Afgani afgano', countries: ['Afganistán'] },
        { symbol: '៛', code: 'KHR', name: 'Riel camboyano', countries: ['Camboya'] },
        
        // Oceanía
        { symbol: 'A$', code: 'AUD', name: 'Dólar australiano', countries: ['Australia'] },
        { symbol: 'NZ$', code: 'NZD', name: 'Dólar neozelandés', countries: ['Nueva Zelanda'] },
        { symbol: 'FJ$', code: 'FJD', name: 'Dólar fiyiano', countries: ['Fiyi'] },
        { symbol: 'K', code: 'PGK', name: 'Kina papú', countries: ['Papúa Nueva Guinea'] },
        
        // África
        { symbol: 'R', code: 'ZAR', name: 'Rand sudafricano', countries: ['Sudáfrica'] },
        { symbol: '₦', code: 'NGN', name: 'Naira nigeriana', countries: ['Nigeria'] },
        { symbol: 'KSh', code: 'KES', name: 'Chelín keniano', countries: ['Kenia'] },
        { symbol: 'د.م.', code: 'MAD', name: 'Dírham marroquí', countries: ['Marruecos'] },
        { symbol: '£E', code: 'EGP', name: 'Libra egipcia', countries: ['Egipto'] },
        { symbol: 'د.ت', code: 'TND', name: 'Dinar tunecino', countries: ['Túnez'] },
        { symbol: 'د.ج', code: 'DZD', name: 'Dinar argelino', countries: ['Argelia'] },
        { symbol: 'Kz', code: 'AOA', name: 'Kwanza angoleño', countries: ['Angola'] },
        { symbol: 'Fr', code: 'XAF', name: 'Franco CFA de África Central', countries: ['Camerún', 'República Centroafricana', 'Chad', 'Guinea Ecuatorial', 'Gabón', 'República del Congo'] },
        { symbol: 'Fr', code: 'XOF', name: 'Franco CFA de África Occidental', countries: ['Benín', 'Burkina Faso', 'Costa de Marfil', 'Guinea-Bisáu', 'Mali', 'Níger', 'Senegal', 'Togo'] },
        { symbol: 'Ar', code: 'MGA', name: 'Ariary malgache', countries: ['Madagascar'] },
        { symbol: 'P', code: 'BWP', name: 'Pula botsuano', countries: ['Botsuana'] },
        
        // Medio Oriente
        { symbol: '﷼', code: 'IRR', name: 'Rial iraní', countries: ['Irán'] },
        { symbol: '﷼', code: 'SAR', name: 'Rial saudí', countries: ['Arabia Saudita'] },
        { symbol: 'د.إ', code: 'AED', name: 'Dírham de EAU', countries: ['Emiratos Árabes Unidos'] },
        { symbol: '₪', code: 'ILS', name: 'Nuevo séquel israelí', countries: ['Israel'] },
        { symbol: 'ل.ل', code: 'LBP', name: 'Libra libanesa', countries: ['Líbano'] },
        { symbol: 'د.ك', code: 'KWD', name: 'Dinar kuwaití', countries: ['Kuwait'] },
        { symbol: 'ر.ع.', code: 'OMR', name: 'Rial omaní', countries: ['Omán'] },
        { symbol: 'ر.ق', code: 'QAR', name: 'Riyal catarí', countries: ['Catar'] },
        { symbol: 'د.ع', code: 'IQD', name: 'Dinar iraquí', countries: ['Irak'] },
        { symbol: 'ل.س', code: 'SYP', name: 'Libra siria', countries: ['Siria'] },
        { symbol: '₺', code: 'TRY', name: 'Lira turca', countries: ['Turquía'] }
    ];

    // Inicializar Select2
    $(document).ready(function() {
        $('#currencySearch').select2({
            placeholder: 'Buscar moneda o país...',
            allowClear: true,
            minimumInputLength: 2,
            language: {
                inputTooShort: function() {
                    return 'Ingresa al menos 2 caracteres';
                },
                searching: function() {
                    return 'Buscando...';
                },
                noResults: function() {
                    return 'No se encontraron resultados';
                }
            },
            data: currencyDatabase.map(function(currency) {
                return {
                    id: currency.code,
                    text: `${currency.symbol} - ${currency.name} (${currency.countries.slice(0, 3).join(', ')}${currency.countries.length > 3 ? '...' : ''})`,
                    symbol: currency.symbol,
                    code: currency.code,
                    name: currency.name,
                    countries: currency.countries
                };
            })
        });

        // Evento al seleccionar una moneda
        $('#currencySearch').on('select2:select', function(e) {
            const data = e.params.data;
            
            // Actualizar campos ocultos
            $('#siglaPais').val(data.code.slice(0, 2));
            $('#codMoneda').val(data.code);
            $('#simboloMoneda').val(data.symbol);
            $('#nombrePais').val(data.countries[0]);
            
            // Actualizar vista previa
            $('#vistaPais').text(data.countries[0]);
            $('#vistaSigla').text(data.code.slice(0, 2));
            $('#vistaMoneda').text(`${data.code} (${data.symbol})`);
            
            // Habilitar botón de grabar
            $('#btnGrabar').prop('disabled', false);
        });

        // Evento al limpiar la selección
        $('#currencySearch').on('select2:clear', function() {
            // Limpiar campos
            $('#siglaPais').val('');
            $('#codMoneda').val('');
            $('#simboloMoneda').val('');
            $('#nombrePais').val('');
            
            $('#vistaPais').text('—');
            $('#vistaSigla').text('—');
            $('#vistaMoneda').text('—');
            
            // Deshabilitar botón de grabar
            $('#btnGrabar').prop('disabled', true);
        });
    });
    </script>
</body>
</html>