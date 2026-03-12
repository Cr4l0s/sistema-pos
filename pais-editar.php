<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idPais'])) {
    $idPais = intval($_POST['idPais']);
} else {
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
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Editar País</h4>
                <a href="menu.php?page=inicio_pais.php" class="btn btn-danger">
                    <span class="bi bi-arrow-left"></span> Volver
                </a>
            </div>
            <div class="card-body">
                
                <!-- BUSCADOR DE PAÍS (PRIMERO) -->
                <div class="buscador-card">
                    <h5><i class="bi bi-globe"></i> Buscar País</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <select class="form-control" id="countrySearch" style="width: 100%;">
                                <option value="">-- Busca un país --</option>
                            </select>
                            <small class="text-muted">Al seleccionar, se completarán la sigla y nombre del país</small>
                        </div>
                    </div>
                </div>

                <!-- BUSCADOR DE MONEDA (SEGUNDO) -->
                <div class="buscador-card">
                    <h5><i class="bi bi-currency-exchange"></i> Buscar Moneda</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <select class="form-control" id="currencySearch" style="width: 100%;">
                                <option value="">-- Busca una moneda --</option>
                            </select>
                            <small class="text-muted">Al seleccionar, se completarán el código y símbolo de la moneda</small>
                        </div>
                    </div>
                </div>

                <hr>

                <form action="acciones-pais.php" method="POST" id="paisForm">
                    <input type="hidden" name="idPais" value="<?= $pais['idPais'] ?>">
                    
                    <div class="row">
                        <!-- Grupo País -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Datos del País</h6>
                                    <div class="mb-3">
                                        <label>Sigla del País</label>
                                        <input type="text" class="form-control" name="siglaPais" id="siglaPais" 
                                               value="<?= htmlspecialchars($pais['siglaPais']) ?>" maxlength="2" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Nombre del País</label>
                                        <input type="text" class="form-control" name="nombrePais" id="nombrePais" 
                                               value="<?= htmlspecialchars($pais['nombrePais']) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Grupo Moneda -->
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Datos de la Moneda</h6>
                                    <div class="mb-3">
                                        <label>Código de Moneda</label>
                                        <input type="text" class="form-control" name="codMoneda" id="codMoneda" 
                                               value="<?= htmlspecialchars($pais['codMoneda']) ?>" maxlength="3" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Símbolo</label>
                                        <input type="text" class="form-control" name="simbolo_moneda" id="simboloMoneda" 
                                               value="<?= htmlspecialchars($pais['simbolo_moneda']) ?>" maxlength="10" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button type="submit" name="update_pais" class="btn btn-primary btn-lg">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Base de datos de monedas
    const currencyDatabase = [
        { symbol: '$', code: 'USD', name: 'Dólar estadounidense' },
        { symbol: '€', code: 'EUR', name: 'Euro' },
        { symbol: '£', code: 'GBP', name: 'Libra esterlina' },
        { symbol: '¥', code: 'JPY', name: 'Yen japonés' },
        { symbol: 'C$', code: 'CAD', name: 'Dólar canadiense' },
        { symbol: 'A$', code: 'AUD', name: 'Dólar australiano' },
        { symbol: 'Fr', code: 'CHF', name: 'Franco suizo' },
        { symbol: '¥', code: 'CNY', name: 'Yuan chino' },
        { symbol: '₹', code: 'INR', name: 'Rupia india' },
        { symbol: 'R$', code: 'BRL', name: 'Real brasileño' },
        { symbol: '$', code: 'MXN', name: 'Peso mexicano' },
        { symbol: '$', code: 'ARS', name: 'Peso argentino' },
        { symbol: '$', code: 'COP', name: 'Peso colombiano' },
        { symbol: '$', code: 'CLP', name: 'Peso chileno' },
        { symbol: 'Bs', code: 'BOB', name: 'Boliviano' },
        { symbol: 'S/', code: 'PEN', name: 'Sol peruano' },
        { symbol: '₲', code: 'PYG', name: 'Guaraní paraguayo' },
        { symbol: '$', code: 'UYU', name: 'Peso uruguayo' },
        { symbol: '₡', code: 'CRC', name: 'Colón costarricense' },
        { symbol: 'L', code: 'HNL', name: 'Lempira hondureña' },
        { symbol: '؋', code: 'AFN', name: 'Afgani afgano' },
        { symbol: '₸', code: 'KZT', name: 'Tenge kazajo' },
        { symbol: '៛', code: 'KHR', name: 'Riel camboyano' },
        { symbol: '₩', code: 'KRW', name: 'Won surcoreano' },
        { symbol: 'HK$', code: 'HKD', name: 'Dólar hongkonés' },
        { symbol: 'S$', code: 'SGD', name: 'Dólar de Singapur' },
        { symbol: 'NT$', code: 'TWD', name: 'Nuevo dólar taiwanés' },
        { symbol: 'Rp', code: 'IDR', name: 'Rupia indonesia' },
        { symbol: 'RM', code: 'MYR', name: 'Ringgit malasio' },
        { symbol: '₱', code: 'PHP', name: 'Peso filipino' },
        { symbol: '฿', code: 'THB', name: 'Baht tailandés' },
        { symbol: '₫', code: 'VND', name: 'Dong vietnamita' }
    ];

    // Base de datos de países
    const countryDatabase = [
        { code: 'AF', name: 'Afganistán' },
        { code: 'AL', name: 'Albania' },
        { code: 'DE', name: 'Alemania' },
        { code: 'AD', name: 'Andorra' },
        { code: 'AO', name: 'Angola' },
        { code: 'SA', name: 'Arabia Saudita' },
        { code: 'DZ', name: 'Argelia' },
        { code: 'AR', name: 'Argentina' },
        { code: 'AM', name: 'Armenia' },
        { code: 'AU', name: 'Australia' },
        { code: 'AT', name: 'Austria' },
        { code: 'AZ', name: 'Azerbaiyán' },
        { code: 'BS', name: 'Bahamas' },
        { code: 'BD', name: 'Bangladesh' },
        { code: 'BB', name: 'Barbados' },
        { code: 'BH', name: 'Baréin' },
        { code: 'BE', name: 'Bélgica' },
        { code: 'BZ', name: 'Belice' },
        { code: 'BJ', name: 'Benín' },
        { code: 'BY', name: 'Bielorrusia' },
        { code: 'MM', name: 'Birmania' },
        { code: 'BO', name: 'Bolivia' },
        { code: 'BA', name: 'Bosnia y Herzegovina' },
        { code: 'BW', name: 'Botsuana' },
        { code: 'BR', name: 'Brasil' },
        { code: 'BN', name: 'Brunéi' },
        { code: 'BG', name: 'Bulgaria' },
        { code: 'BF', name: 'Burkina Faso' },
        { code: 'BI', name: 'Burundi' },
        { code: 'BT', name: 'Bután' },
        { code: 'CV', name: 'Cabo Verde' },
        { code: 'KH', name: 'Camboya' },
        { code: 'CM', name: 'Camerún' },
        { code: 'CA', name: 'Canadá' },
        { code: 'QA', name: 'Catar' },
        { code: 'TD', name: 'Chad' },
        { code: 'CL', name: 'Chile' },
        { code: 'CN', name: 'China' },
        { code: 'CY', name: 'Chipre' },
        { code: 'CO', name: 'Colombia' },
        { code: 'KM', name: 'Comoras' },
        { code: 'KP', name: 'Corea del Norte' },
        { code: 'KR', name: 'Corea del Sur' },
        { code: 'CI', name: 'Costa de Marfil' },
        { code: 'CR', name: 'Costa Rica' },
        { code: 'HR', name: 'Croacia' },
        { code: 'CU', name: 'Cuba' },
        { code: 'DK', name: 'Dinamarca' },
        { code: 'DM', name: 'Dominica' },
        { code: 'EC', name: 'Ecuador' },
        { code: 'EG', name: 'Egipto' },
        { code: 'SV', name: 'El Salvador' },
        { code: 'AE', name: 'Emiratos Árabes Unidos' },
        { code: 'ER', name: 'Eritrea' },
        { code: 'SK', name: 'Eslovaquia' },
        { code: 'SI', name: 'Eslovenia' },
        { code: 'ES', name: 'España' },
        { code: 'US', name: 'Estados Unidos' },
        { code: 'EE', name: 'Estonia' },
        { code: 'ET', name: 'Etiopía' },
        { code: 'PH', name: 'Filipinas' },
        { code: 'FI', name: 'Finlandia' },
        { code: 'FJ', name: 'Fiyi' },
        { code: 'FR', name: 'Francia' },
        { code: 'GA', name: 'Gabón' },
        { code: 'GM', name: 'Gambia' },
        { code: 'GE', name: 'Georgia' },
        { code: 'GH', name: 'Ghana' },
        { code: 'GR', name: 'Grecia' },
        { code: 'GD', name: 'Granada' },
        { code: 'GT', name: 'Guatemala' },
        { code: 'GN', name: 'Guinea' },
        { code: 'GW', name: 'Guinea-Bisáu' },
        { code: 'GQ', name: 'Guinea Ecuatorial' },
        { code: 'GY', name: 'Guyana' },
        { code: 'HT', name: 'Haití' },
        { code: 'HN', name: 'Honduras' },
        { code: 'HU', name: 'Hungría' },
        { code: 'IN', name: 'India' },
        { code: 'ID', name: 'Indonesia' },
        { code: 'IQ', name: 'Irak' },
        { code: 'IR', name: 'Irán' },
        { code: 'IE', name: 'Irlanda' },
        { code: 'IS', name: 'Islandia' },
        { code: 'IL', name: 'Israel' },
        { code: 'IT', name: 'Italia' },
        { code: 'JM', name: 'Jamaica' },
        { code: 'JP', name: 'Japón' },
        { code: 'JO', name: 'Jordania' },
        { code: 'KZ', name: 'Kazajistán' },
        { code: 'KE', name: 'Kenia' },
        { code: 'KG', name: 'Kirguistán' },
        { code: 'KI', name: 'Kiribati' },
        { code: 'KW', name: 'Kuwait' },
        { code: 'LA', name: 'Laos' },
        { code: 'LS', name: 'Lesoto' },
        { code: 'LV', name: 'Letonia' },
        { code: 'LB', name: 'Líbano' },
        { code: 'LR', name: 'Liberia' },
        { code: 'LY', name: 'Libia' },
        { code: 'LI', name: 'Liechtenstein' },
        { code: 'LT', name: 'Lituania' },
        { code: 'LU', name: 'Luxemburgo' },
        { code: 'MK', name: 'Macedonia del Norte' },
        { code: 'MG', name: 'Madagascar' },
        { code: 'MY', name: 'Malasia' },
        { code: 'MW', name: 'Malawi' },
        { code: 'MV', name: 'Maldivas' },
        { code: 'ML', name: 'Malí' },
        { code: 'MT', name: 'Malta' },
        { code: 'MA', name: 'Marruecos' },
        { code: 'MU', name: 'Mauricio' },
        { code: 'MR', name: 'Mauritania' },
        { code: 'MX', name: 'México' },
        { code: 'FM', name: 'Micronesia' },
        { code: 'MD', name: 'Moldavia' },
        { code: 'MC', name: 'Mónaco' },
        { code: 'MN', name: 'Mongolia' },
        { code: 'ME', name: 'Montenegro' },
        { code: 'MZ', name: 'Mozambique' },
        { code: 'NA', name: 'Namibia' },
        { code: 'NR', name: 'Nauru' },
        { code: 'NP', name: 'Nepal' },
        { code: 'NI', name: 'Nicaragua' },
        { code: 'NE', name: 'Níger' },
        { code: 'NG', name: 'Nigeria' },
        { code: 'NO', name: 'Noruega' },
        { code: 'NZ', name: 'Nueva Zelanda' },
        { code: 'OM', name: 'Omán' },
        { code: 'NL', name: 'Países Bajos' },
        { code: 'PK', name: 'Pakistán' },
        { code: 'PW', name: 'Palaos' },
        { code: 'PS', name: 'Palestina' },
        { code: 'PA', name: 'Panamá' },
        { code: 'PG', name: 'Papúa Nueva Guinea' },
        { code: 'PY', name: 'Paraguay' },
        { code: 'PE', name: 'Perú' },
        { code: 'PL', name: 'Polonia' },
        { code: 'PT', name: 'Portugal' },
        { code: 'PR', name: 'Puerto Rico' },
        { code: 'GB', name: 'Reino Unido' },
        { code: 'CF', name: 'República Centroafricana' },
        { code: 'CZ', name: 'República Checa' },
        { code: 'CG', name: 'República del Congo' },
        { code: 'CD', name: 'República Democrática del Congo' },
        { code: 'DO', name: 'República Dominicana' },
        { code: 'RW', name: 'Ruanda' },
        { code: 'RO', name: 'Rumania' },
        { code: 'RU', name: 'Rusia' },
        { code: 'EH', name: 'Sáhara Occidental' },
        { code: 'WS', name: 'Samoa' },
        { code: 'KN', name: 'San Cristóbal y Nieves' },
        { code: 'SM', name: 'San Marino' },
        { code: 'VC', name: 'San Vicente y las Granadinas' },
        { code: 'LC', name: 'Santa Lucía' },
        { code: 'ST', name: 'Santo Tomé y Príncipe' },
        { code: 'SN', name: 'Senegal' },
        { code: 'RS', name: 'Serbia' },
        { code: 'SC', name: 'Seychelles' },
        { code: 'SL', name: 'Sierra Leona' },
        { code: 'SG', name: 'Singapur' },
        { code: 'SX', name: 'Sint Maarten' },
        { code: 'SY', name: 'Siria' },
        { code: 'SO', name: 'Somalia' },
        { code: 'LK', name: 'Sri Lanka' },
        { code: 'ZA', name: 'Sudáfrica' },
        { code: 'SD', name: 'Sudán' },
        { code: 'SS', name: 'Sudán del Sur' },
        { code: 'SE', name: 'Suecia' },
        { code: 'CH', name: 'Suiza' },
        { code: 'SR', name: 'Surinam' },
        { code: 'SJ', name: 'Svalbard y Jan Mayen' },
        { code: 'SZ', name: 'Suazilandia' },
        { code: 'TH', name: 'Tailandia' },
        { code: 'TW', name: 'Taiwán' },
        { code: 'TZ', name: 'Tanzania' },
        { code: 'TJ', name: 'Tayikistán' },
        { code: 'IO', name: 'Territorio Británico del Océano Índico' },
        { code: 'PS', name: 'Territorios Palestinos' },
        { code: 'TL', name: 'Timor Oriental' },
        { code: 'TG', name: 'Togo' },
        { code: 'TK', name: 'Tokelau' },
        { code: 'TO', name: 'Tonga' },
        { code: 'TT', name: 'Trinidad y Tobago' },
        { code: 'TN', name: 'Túnez' },
        { code: 'TM', name: 'Turkmenistán' },
        { code: 'TC', name: 'Turcas y Caicos' },
        { code: 'TR', name: 'Turquía' },
        { code: 'TV', name: 'Tuvalu' },
        { code: 'UA', name: 'Ucrania' },
        { code: 'UG', name: 'Uganda' },
        { code: 'UY', name: 'Uruguay' },
        { code: 'UZ', name: 'Uzbekistán' },
        { code: 'VU', name: 'Vanuatu' },
        { code: 'VA', name: 'Vaticano' },
        { code: 'VE', name: 'Venezuela' },
        { code: 'VN', name: 'Vietnam' },
        { code: 'YE', name: 'Yemen' },
        { code: 'DJ', name: 'Yibuti' },
        { code: 'ZM', name: 'Zambia' },
        { code: 'ZW', name: 'Zimbabue' }
    ];

    // Inicializar Select2 para países (PRIMERO)
    $(document).ready(function() {
        $('#countrySearch').select2({
            placeholder: 'Buscar país...',
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
            data: countryDatabase.map(function(country) {
                return {
                    id: country.code,
                    text: `${country.name} (${country.code})`,
                    code: country.code,
                    name: country.name
                };
            })
        });

        // Inicializar Select2 para monedas (SEGUNDO)
        $('#currencySearch').select2({
            placeholder: 'Buscar moneda...',
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
                    text: `${currency.symbol} - ${currency.name} (${currency.code})`,
                    symbol: currency.symbol,
                    code: currency.code
                };
            })
        });

        // Evento al seleccionar un país
        $('#countrySearch').on('select2:select', function(e) {
            const data = e.params.data;
            $('#siglaPais').val(data.code);
            $('#nombrePais').val(data.name);
        });

        // Evento al seleccionar una moneda
        $('#currencySearch').on('select2:select', function(e) {
            const data = e.params.data;
            $('#codMoneda').val(data.code);
            $('#simboloMoneda').val(data.symbol);
        });

        // Evento al limpiar país
        $('#countrySearch').on('select2:clear', function() {
            $('#siglaPais').val('<?= $pais['siglaPais'] ?>');
            $('#nombrePais').val('<?= $pais['nombrePais'] ?>');
        });

        // Evento al limpiar moneda
        $('#currencySearch').on('select2:clear', function() {
            $('#codMoneda').val('<?= $pais['codMoneda'] ?>');
            $('#simboloMoneda').val('<?= $pais['simbolo_moneda'] ?>');
        });
    });
    </script>
</body>
</html>