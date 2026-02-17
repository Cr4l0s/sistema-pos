<?php
session_start();

// Definir las opciones principales y secundarias
$menu = [
    'Acceso' => [
        ['name' => 'Acceso', 'file' => ''],
        ['name' => 'Cerrar Sesión', 'file' => '']
    ],
    'Mantenedores' => [
        ['name' => 'Categorías', 'file' => 'categorias.php'],
        ['name' => 'Productos', 'file' => 'productos.php'],        
        ['name' => 'Usuarios', 'file' => 'inicio-usuarios.php']
    ],
    'Configuración' => [
        ['name' => 'Parámetros Generales', 'file' => ''],
        ['name' => 'Parámetros Opcionales', 'file' => ''],
        ['name' => 'Otros Parámetros', 'file' => '']        
    ]
];

// Definir los parámetros y variables globales
	$textoMantenedor = $_SESSION['textoMantenedor']; 
		
	
	if (!isset($textoBoton)) {
		$textoBoton = 'Seleccionar';
	} 
	else { $textoBoton = $_SESSION['textoBoton']; }
		
	if (!isset($titulo)) {	
		$titulo = '';
	} 
	else { $titulo = $_SESSION['titulo']; }
	
	if (!isset($filas_x_pagina)) {
		$filas_x_pagina = 8;
	} 
	else { $filas_x_pagina = $_SESSION['filas_x_pagina']; }
	
	$_SESSION['textoMantenedor'] = $textoMantenedor;
	$_SESSION['accion'] = $accion;
	$_SESSION['textoBoton'] = $textoBoton ;
	$_SESSION['titulo'] = $titulo;
	$_SESSION['filas_x_pagina'] = $filas_x_pagina;	
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?></title>
    <!-- Incluir CSS de Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php foreach ($menu as $main => $subs): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown<?php echo $main; ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $main; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown<?php echo $main; ?>">
                            <?php foreach ($subs as $sub): ?>
                                <a class="dropdown-item" href="<?php echo $sub['file']; ?>"><?php echo $sub['name']; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <!-- Incluir JavaScript de Bootstrap y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>