<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombre_categoria';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$es_todos = ($filas == -1);
$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

// Campos válidos para ordenamiento
$campos_validos = ['nombre_categoria', 'descripcion'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombre_categoria';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

// Consulta principal
$where = "WHERE activo = 1";
if (!empty($buscar)) {
    $where .= " AND (nombre_categoria LIKE '%$buscar_escapado%' OR descripcion LIKE '%$buscar_escapado%')";
}

$sql = "SELECT id_categoria, nombre_categoria, descripcion 
        FROM categorias
        $where
        ORDER BY $orden_validado $direccion_validada";

if (!$es_todos) {
    $sql .= " LIMIT $offset, $filas";
}

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "html" => "<tr><td colspan='3' class='text-center text-danger'>Error en consulta: " . $conn->error . "</td></tr>",
        "paginacion" => ""
    ]);
    exit;
}

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombre_categoria']) . "</td>
        <td>" . htmlspecialchars($r['descripcion'] ?: '—') . "</td>";

    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=categoria-ver.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id' value='{$r['id_categoria']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=categoria-editar.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id' value='{$r['id_categoria']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='categoria-acciones.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_categoria' value='{$r['id_categoria']}'>
                <button type='submit' name='borrar_categoria' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }

    $html .= "</tr>";
}

// Total de registros
$sql_total = "SELECT COUNT(*) as total FROM categorias $where";
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

echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>