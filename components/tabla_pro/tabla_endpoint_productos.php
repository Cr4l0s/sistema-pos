<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int) $_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombre_producto';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$categoria = isset($_GET['categoria']) ? (int) $_GET['categoria'] : 0;
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

// Si filas es -1, significa "todos los registros"
$es_todos = ($filas == -1);

$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

// Campos válidos para ordenamiento
$campos_validos = ['codigo_barras', 'nombre_producto', 'categoria', 'precio_compras', 'precio_venta', 'stock_actual', 'mostrar_en_tienda'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombre_producto';

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

// Determinar campo de ordenamiento real
switch ($orden_validado) {
    case 'codigo_barras':
        $campo_orden = 'p.codigo_barras';
        break;
    case 'categoria':
        $campo_orden = 'c.nombre_categoria';
        break;
    case 'precio_compras':
        $campo_orden = 'p.precio_compras';
        break;
    case 'precio_venta':
        $campo_orden = 'p.precio_venta';
        break;
    case 'stock_actual':
        $campo_orden = 'p.stock_actual';
        break;
    case 'mostrar_en_tienda':
        $campo_orden = 'p.mostrar_en_tienda';
        break;
    case 'nombre_producto':
    default:
        $campo_orden = 'p.nombre_producto';
        break;
}

// Consulta principal con JOIN a categorías
$where = "WHERE p.activo = 1";
if (!empty($buscar)) {
    $where .= " AND (p.nombre_producto LIKE '%$buscar_escapado%' 
                  OR p.descripcion LIKE '%$buscar_escapado%'
                  OR p.codigo_barras LIKE '%$buscar_escapado%'
                  OR c.nombre_categoria LIKE '%$buscar_escapado%')";
}

if ($categoria > 0) {
    $where .= " AND p.id_categoria = $categoria";
}

// ORDEN CORRECTO de columnas según la vista
$sql = "SELECT 
            p.id_producto,
            p.codigo_barras,
            p.nombre_producto,
            c.nombre_categoria,
            p.precio_compras,
            p.precio_venta,
            p.stock_actual,
            p.mostrar_en_tienda,
            p.stock_minimo
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        $where
        ORDER BY $campo_orden $direccion_validada";

if (!$es_todos) {
    $sql .= " LIMIT $offset, $filas";
}

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "html" => "<tr><td colspan='8' class='text-center text-danger'>Error en consulta: " . $conn->error . "</td></tr>",
        "paginacion" => ""
    ]);
    exit;
}

$html = "";
while ($r = $result->fetch_assoc()) {
    // Guardar en un archivo temporal
    file_put_contents('debug_stock.txt', "Producto: " . $r['nombre_producto'] . " - Stock: " . $r['stock_actual'] . "\n", FILE_APPEND);
    // Formatear precios como moneda
    $precio_compras = '$' . number_format($r['precio_compras'], 0, ',', '.');
    $precio_venta = '$' . number_format($r['precio_venta'], 0, ',', '.');

    // Determinar si el stock es bajo
    $stock_class = '';
    if ($r['stock_actual'] <= $r['stock_minimo']) {
        $stock_class = 'text-danger fw-bold';
    }

    // Para exportación (sin_acciones) usar texto, para vista normal usar ícono
    if ($sin_acciones) {
        $tienda_valor = $r['mostrar_en_tienda'] ? 'Sí' : 'No';
        $stock_celda = (int)$r['stock_actual'];
        $stock_html = "<td>" . $stock_celda . "</td>";
    } else {
        $tienda_valor = $r['mostrar_en_tienda'] ? '✅' : '❌';
        $stock_class = ($r['stock_actual'] <= $r['stock_minimo']) ? 'text-danger fw-bold' : '';
        $stock_html = "<td class='$stock_class'>" . (int)$r['stock_actual'] . "</td>";
    }

    // ORDEN CORRECTO de celdas según la vista
    $html .= "<tr>";
    $html .= "<td>" . htmlspecialchars($r['codigo_barras'] ?: '—') . "</td>";
    $html .= "<td>" . htmlspecialchars($r['nombre_producto']) . "</td>";
    $html .= "<td>" . htmlspecialchars($r['nombre_categoria'] ?: '—') . "</td>";
    $html .= "<td>" . $precio_compras . "</td>";
    $html .= "<td>" . $precio_venta . "</td>";
    $html .= $stock_html;
    $html .= "<td>" . $tienda_valor . "</td>";

    // Solo agregamos la columna de acciones si NO es sin_acciones
    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=producto-ver.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='{$r['id_producto']}'>
                <button type='submit' class='btn btn-sm btn-secondary' title='Ver'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=producto-editar.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='{$r['id_producto']}'>
                <button type='submit' class='btn btn-sm btn-success' title='Editar'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='producto-acciones.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='{$r['id_producto']}'>
                <button type='submit' name='borrar_producto' class='btn btn-sm btn-danger' title='Eliminar' onclick='return confirm(\"¿Eliminar este producto?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }

    $html .= "</tr>";
}

// Total de registros considerando búsqueda y categoría
$sql_total = "SELECT COUNT(DISTINCT p.id_producto) as total 
              FROM productos p
              LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
              $where";
$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = $filas > 0 && !$es_todos ? ceil($total / $filas) : 1;

// Paginación
$paginacion = "";
if ($totalPaginas > 1 && !$es_todos) {
    if ($pagina > 1) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='" . ($pagina - 1) . "'>
            <i class='bi bi-chevron-left'></i> Anterior
        </button>";
    }

    $inicio = max(1, $pagina - 2);
    $fin = min($totalPaginas, $pagina + 2);

    if ($inicio > 1) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='1'>1</button>";
        if ($inicio > 2) {
            $paginacion .= "<span class='btn btn-sm btn-outline-secondary disabled me-1'>...</span>";
        }
    }

    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $pagina) ? 'active' : '';
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1 $active' data-page='$i'>$i</button>";
    }

    if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) {
            $paginacion .= "<span class='btn btn-sm btn-outline-secondary disabled me-1'>...</span>";
        }
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='$totalPaginas'>$totalPaginas</button>";
    }

    if ($pagina < $totalPaginas) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn' data-page='" . ($pagina + 1) . "'>
            Siguiente <i class='bi bi-chevron-right'></i>
        </button>";
    }
}

header('Content-Type: application/json');
echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>