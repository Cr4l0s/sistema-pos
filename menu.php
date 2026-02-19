<?php
session_start();

// Definir las opciones principales y secundarias
$menu = [
    'Acceso' => [
        ['name' => 'Acceso', 'file' => 'login.php'],
        ['name' => 'Cerrar Sesión', 'file' => 'logout.php']
    ],
    'Mantenedores' => [
        ['name' => 'Categorías', 'file' => 'categorias.php'],
        ['name' => 'Productos', 'file' => 'productos.php'],
        ['name' => 'Usuarios', 'file' => 'inicio.php'],
        ['name' => 'Empresas', 'file' => 'inicio-empresas.php'],
    ],
    'Configuración' => [
        ['name' => 'Parámetros Generales', 'file' => '#'],
        ['name' => 'Parámetros Opcionales', 'file' => '#'],
        ['name' => 'Otros Parámetros', 'file' => '#']
    ]
];

// Valores por defecto para evitar warnings
$textoMantenedor = $_SESSION['textoMantenedor'] ?? '';
$textoBoton = $_SESSION['textoBoton'] ?? 'Seleccionar';
$titulo = $_SESSION['titulo'] ?? 'Sistema POS';
$filas_x_pagina = $_SESSION['filas_x_pagina'] ?? 8;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="menu.php">Sistema POS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php foreach ($menu as $main => $subs): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <?php echo $main; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($subs as $sub): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo $sub['file']; ?>">
                                            <?php echo $sub['name']; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Bienvenido al Sistema POS</h1>
        <p>Seleccioná una opción del menú para comenzar.</p>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>