<?php

require_once 'config.php';
session_start();

$page = $_GET['page'] ?? '';
$currentPage = $page;

$nombreSistema = APP_NOMBRE;
$usuario = $_SESSION['usuario'] ?? 'Invitado';

// Ruta del logo (cambia por tu archivo real)
$logo = "img/logo.jpeg";

$menu = [
    'Mantenedores' => [
        // Visibles
        ['name' => 'Países', 'file' => 'inicio_pais.php', 'icon' => 'bi-globe-americas'],
        ['name' => 'Regiones', 'file' => 'selector_pais.php', 'icon' => 'bi-map'],
        ['name' => 'Ciudades', 'file' => 'selector_pais_y_region.php', 'icon' => 'bi-building'],
        ['name' => 'Comunas', 'file' => 'selector_pais_region_ciudad.php', 'icon' => 'bi-grid'],
        ['name' => 'Categorías', 'file' => 'categorias.php', 'icon' => 'bi-tags'],
        ['name' => 'Productos', 'file' => 'productos.php', 'icon' => 'bi-box'],
        ['name' => 'Usuarios', 'file' => 'inicio-usuarios.php', 'icon' => 'bi-people'],

        // Ocultos (gestión)
        ['name' => 'Gestionar Regiones', 'file' => 'gestionar_regiones.php', 'icon' => 'bi-map', 'hidden' => true],
        ['name' => 'Gestionar Ciudades', 'file' => 'gestionar_ciudades.php', 'icon' => 'bi-building', 'hidden' => true],
        ['name' => 'Gestionar Comunas', 'file' => 'gestionar_comunas.php', 'icon' => 'bi-grid', 'hidden' => true],

        // Ocultos (edición y vista)
        ['name' => 'Editar País', 'file' => 'pais-editar.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver País', 'file' => 'pais-ver.php', 'icon' => 'bi-eye', 'hidden' => true],
        ['name' => 'Editar Región', 'file' => 'editar_region.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver Región', 'file' => 'ver_region.php', 'icon' => 'bi-eye', 'hidden' => true],
        ['name' => 'Editar Ciudad', 'file' => 'editar_ciudad.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver Ciudad', 'file' => 'ver_ciudad.php', 'icon' => 'bi-eye', 'hidden' => true],
        ['name' => 'Editar Comuna', 'file' => 'editar_comuna.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver Comuna', 'file' => 'ver_comuna.php', 'icon' => 'bi-eye', 'hidden' => true],
        ['name' => 'Editar Categoría', 'file' => 'categoria-editar.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver Categoría', 'file' => 'categoria-ver.php', 'icon' => 'bi-eye', 'hidden' => true],
        ['name' => 'Editar Producto', 'file' => 'producto-editar.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver Producto', 'file' => 'producto-ver.php', 'icon' => 'bi-eye', 'hidden' => true],
        ['name' => 'Editar Usuario', 'file' => 'usuario-editar.php', 'icon' => 'bi-pencil', 'hidden' => true],
        ['name' => 'Ver Usuario', 'file' => 'usuario-ver.php', 'icon' => 'bi-eye', 'hidden' => true],
    ],
    'Configuración' => [
        ['name' => 'Parámetros Generales', 'file' => '#', 'icon' => 'bi-gear'],
        ['name' => 'Parámetros Opcionales', 'file' => '#', 'icon' => 'bi-sliders'],
        ['name' => 'Otros Parámetros', 'file' => '#', 'icon' => 'bi-wrench']
    ]
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= $nombreSistema ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg:
                <?= SIDEBAR_BG ?>
            ;
            --sidebar-active-1:
                <?= SIDEBAR_ACTIVE ?>
            ;
            --sidebar-active-2:
                <?= SIDEBAR_ACTIVE_GRADIENT ?>
            ;
            --topbar-bg: #ffffff;
            --button-primary: var(--sidebar-active-1);
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f3f4f6;
        }

        .contenido-principal {
            flex: 1;
            padding: 30px;
        }

        .top-header {
            background: #111827;
            /* gris oscuro elegante */
            color: #fff;
            padding: 12px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .top-header h1 {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .logo-img {
            height: 50px;
            object-fit: contain;
        }

        .navbar {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .nav-link {
            font-weight: 500;
        }

        .card {
            border-radius: 16px;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
        }

        .layout {
            display: flex;
            height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 250px;
            /* background: <?= SIDEBAR_BG ?>
            ;
            */ background: var(--sidebar-bg);
            color: #fff;
            /* transition: all 0.3s ease; */
            transition: background 0.4s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
        }

        .sidebar-menu li {
            padding: 0;
        }

        .sidebar-menu li a {
            color: #cbd5e1;
            text-decoration: none;
            display: block;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            width: 100%;
            transition: 0.2s;
        }

        .sidebar-menu li a:hover {
            background: #1f2937;
            border-radius: 8px;
            color: #fff;
        }

        .sidebar .submenu li a.active-link {
            /* background: linear-gradient(90deg, #2563eb, #1d4ed8); */
            /* background: linear-gradient(90deg, <?= SIDEBAR_ACTIVE ?>
            ,
            <?= SIDEBAR_ACTIVE_GRADIENT ?>
            );
            */ background: linear-gradient(90deg, var(--sidebar-active-1), var(--sidebar-active-2));
            color: #fff !important;
            transition: all 0.4s ease;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .menu-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 15px 20px 5px;
            color: #9ca3af;
        }

        /* MAIN */

        .btn-primary {
            background: var(--button-primary);
            border-color: var(--button-primary);
        }


        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            /* background: #ffffff; */
            background: var(--topbar-bg);
            transition: background 0.4s ease;

            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .content-area {
            flex: 1;
            padding: 30px;
        }

        .footer {
            padding: 15px;
            text-align: center;
            background: #fff;
            border-top: 1px solid #e5e7eb;
        }

        /* DARK MODE */

        .dark-mode {
            background: #0f172a;
            color: #fff;
        }

        .dark-mode .topbar,
        .dark-mode .footer {
            background: #1e293b;
            color: #fff;
        }

        .dark-mode .card {
            background: #1e293b;
            color: #fff;
        }

        .dark-mode .sidebar {
            background: #0f172a;
        }

        .dark-mode .submenu li a {
            color: #cbd5e1;
        }

        .dark-mode .submenu li a.active-link {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
            color: #fff !important;
        }

        .dark-mode .sidebar .submenu li a.active-link {
            /* background: linear-gradient(90deg, #3b82f6, #2563eb); */
            background:
                <?= SIDEBAR_BG_DARK ?>
            ;
            color: #fff !important;
        }

        .submenu {
            list-style: none;
            padding-left: 0px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .menu-group.active .submenu {
            max-height: 500px;
        }


        .content-area {
            animation: fadeIn 0.4s ease;
        }

        .theme-picker {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .theme-dot {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s ease;
            border: 2px solid #fff;
        }

        .theme-dot:hover {
            transform: scale(1.2);
        }


        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="layout">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="<?= $logo ?>" class="logo-img">
                <span class="brand"><?= $nombreSistema ?></span>
            </div>

            <ul class="sidebar-menu">
                <?php foreach ($menu as $main => $subs): ?>

                    <?php
                    // Filtrar solo los ítems que NO tienen 'hidden' = true
                    $subs_visibles = array_filter($subs, function ($item) {
                        return !isset($item['hidden']) || $item['hidden'] !== true;
                    });

                    // Si no hay ítems visibles en este grupo, no mostrar el grupo
                    if (empty($subs_visibles))
                        continue;
                    ?>

                    <li class="menu-group 
            <?php
            foreach ($subs_visibles as $sub) {
                if ($currentPage == $sub['file']) {
                    echo 'active';
                    break;
                }
            }
            ?>">

                        <div class="menu-toggle">
                            <span><?= $main ?></span>
                            <i class="bi bi-chevron-down arrow"></i>
                        </div>

                        <ul class="submenu">
                            <?php foreach ($subs_visibles as $sub): ?>
                                <li>
                                    <a href="menu.php?page=<?= $sub['file'] ?>"
                                        class="<?= ($currentPage == $sub['file']) ? 'active-link' : '' ?>">
                                        <i class="bi <?= $sub['icon'] ?>"></i>
                                        <span><?= $sub['name'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="main-content">

            <!-- TOPBAR -->
            <header class="topbar">

                <div class="theme-picker">
                    <div class="theme-dot" data-theme="blue" style="background:#2563eb"></div>
                    <div class="theme-dot" data-theme="green" style="background:#10b981"></div>
                    <div class="theme-dot" data-theme="purple" style="background:#8b5cf6"></div>
                    <div class="theme-dot" data-theme="corporate" style="background:#334155"></div>
                    <div class="theme-dot" data-theme="dark" style="background:#111827"></div>
                </div>

                <button id="toggleSidebar" class="btn btn-sm btn-light">
                    <i class="bi bi-list"></i>
                </button>

                <div class="topbar-right">
                    <button id="toggleTheme" class="btn btn-sm btn-light">
                        <i class="bi bi-moon"></i>
                    </button>

                    <span class="usuario">
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($usuario) ?>
                    </span>
                </div>
            </header>

            <!-- CONTENIDO -->
            <main class="content-area">
                <div class="card shadow-sm border-0 rounded-4 p-4">

                    <?php
                    $allowedPages = [];

                    $currentTitle = 'Panel Principal';

                    foreach ($menu as $group) {
                        foreach ($group as $sub) {
                            if ($sub['file'] === $currentPage) {
                                $currentTitle = $sub['name'];
                            }
                        }
                    }


                    foreach ($menu as $subs) {
                        foreach ($subs as $sub) {
                            $allowedPages[] = $sub['file'];
                        }
                    }

                    if (in_array($page, $allowedPages)) {
                        include $page;
                    } else {
                        echo "<h3>$currentTitle</h3>";
                    }


                    ?>

                </div>
            </main>
            <!-- FOOTER -->
            <footer class="footer">
                © <?= APP_ANO ?> <?= APP_EMPRESA ?> · <?= APP_NOMBRE ?> v<?= APP_VERSION ?> - En desarrollo por
                <?= APP_DEVELOPER ?> -
                <span id="fecha-hora"></span>
            </footer>

        </div>

    </div>

    <script>
        function actualizarFechaHora() {

            const ahora = new Date();

            const opcionesFecha = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            const fecha = ahora.toLocaleDateString('es-CL', opcionesFecha);
            const hora = ahora.toLocaleTimeString('es-CL');

            const textoFinal = fecha.charAt(0).toUpperCase() + fecha.slice(1)
                + " | " + hora;

            document.getElementById("fecha-hora").innerHTML = textoFinal;
        }

        // Actualiza inmediatamente
        actualizarFechaHora();

        // Actualiza cada segundo
        setInterval(actualizarFechaHora, 1000);
    </script>

    //Script de modo dark
    <script>
        const toggleTheme = document.getElementById("toggleTheme");

        toggleTheme.addEventListener("click", function () {

            document.body.classList.toggle("dark-mode");

            if (document.body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
            } else {
                localStorage.setItem("theme", "light");
            }
        });

        window.addEventListener("load", function () {
            if (localStorage.getItem("theme") === "dark") {
                document.body.classList.add("dark-mode");
            }
        });
    </script>

    <script>
        document.querySelectorAll(".menu-toggle").forEach(function (toggle, index) {

            toggle.addEventListener("click", function () {

                const parent = this.closest(".menu-group");

                document.querySelectorAll(".menu-group").forEach(function (group) {
                    if (group !== parent) {
                        group.classList.remove("active");
                    }
                });

                parent.classList.toggle("active");

                localStorage.setItem("menuOpen", index);
            });
        });

        window.addEventListener("load", function () {

            const savedIndex = localStorage.getItem("menuOpen");

            if (savedIndex !== null) {
                document.querySelectorAll(".menu-group")[savedIndex]
                    ?.classList.add("active");
            }
        });
    </script>

    <script>

        const themes = {
            blue: {
                sidebar: "#111827",
                active1: "#2563eb",
                active2: "#1d4ed8",
                topbar: "#ffffff",
                button: "#2563eb"
            },
            green: {
                sidebar: "#0f1f17",
                active1: "#10b981",
                active2: "#059669",
                topbar: "#ffffff",
                button: "#10b981"
            },
            purple: {
                sidebar: "#1e1b2e",
                active1: "#8b5cf6",
                active2: "#7c3aed",
                topbar: "#ffffff",
                button: "#8b5cf6"
            },
            corporate: {
                sidebar: "#1f2937",
                active1: "#334155",
                active2: "#475569",
                topbar: "#f8fafc",
                button: "#334155"
            }
        };

        function applyTheme(themeName) {
            const theme = themes[themeName];

            document.documentElement.style.setProperty('--sidebar-bg', theme.sidebar);
            document.documentElement.style.setProperty('--sidebar-active-1', theme.active1);
            document.documentElement.style.setProperty('--sidebar-active-2', theme.active2);
            document.documentElement.style.setProperty('--topbar-bg', theme.topbar);
            document.documentElement.style.setProperty('--button-primary', theme.button);

            localStorage.setItem("themeColor", themeName);
        }

        document.querySelectorAll(".theme-dot").forEach(dot => {
            dot.addEventListener("click", function () {
                const theme = this.getAttribute("data-theme");
                applyTheme(theme);
            });
        });

        window.addEventListener("load", function () {
            const saved = localStorage.getItem("themeColor") || "blue";
            applyTheme(saved);
        });


    </script>

</body>

</html>