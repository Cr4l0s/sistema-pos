<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

session_start();

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'codMoneda';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$es_todos = ($filas == -1);
$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

$campos_validos = ['codMoneda', 'nombreMoneda', 'simbolo'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'codMoneda';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE vigente = 1";
if (!empty($buscar)) {
    $where .= " AND (codMoneda LIKE '%$buscar_escapado%' 
                  OR nombreMoneda LIKE '%$buscar_escapado%'
                  OR simbolo LIKE '%$buscar_escapado%')";
}

$sql = "SELECT idMoneda, codMoneda, nombreMoneda, simbolo 
        FROM monedas
        $where
        ORDER BY $orden_validado $direccion_validada";

if (!$es_todos) {
    $sql .= " LIMIT $offset, $filas";
}

$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>";
    $html .= "<td>" . htmlspecialchars($r['codMoneda']) . "</td>";
    $html .= "<td>" . htmlspecialchars($r['nombreMoneda']) . "</td>";
    $html .= "<td>" . htmlspecialchars($r['simbolo']) . "</td>";
    
    // Solo mostrar acciones si NO es exportación
if (!$sin_acciones) {
    $html .= "<td>
        <form action='menu.php?page=moneda-ver.php' method='POST' style='display:inline;'>
            <input type='hidden' name='idMoneda' value='{$r['idMoneda']}'>
            <button type='submit' class='btn btn-sm btn-secondary' title='Ver'>
                <i class='bi bi-eye'></i>
            </button>
        </form>
        <form action='menu.php?page=moneda-editar.php' method='POST' style='display:inline;'>
            <input type='hidden' name='idMoneda' value='{$r['idMoneda']}'>
            <button type='submit' class='btn btn-sm btn-success' title='Editar'>
                <i class='bi bi-pencil'></i>
            </button>
        </form>
        <form action='acciones-moneda.php' method='POST' style='display:inline;'>
            <input type='hidden' name='borrar_moneda' value='{$r['idMoneda']}'>
            <button type='submit' class='btn btn-sm btn-danger' title='Eliminar' onclick='return confirm(\"¿Eliminar moneda {$r['nombreMoneda']}?\")'>
                <i class='bi bi-trash'></i>
            </button>
        </form>
    
";
}
    
    $html .= "</tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM monedas $where";
$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = $filas > 0 && !$es_todos ? ceil($total / $filas) : 1;

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