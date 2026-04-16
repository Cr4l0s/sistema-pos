<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';
session_start();

$idUsuario = 12; //$_SESSION['usuario_id'] ?? 12;

// ============================================
// Lista de administradores
// ============================================
$admins = [1, 12];

// Si es administrador, mostrar todos los países; si no, filtrar por sus países
if (in_array($idUsuario, $admins)) {
    $join_usuario = "";
    $where_usuario = "";
} elseif ($idUsuario > 0) {
    $join_usuario = "INNER JOIN usuarios_paises up ON p.idPais = up.idPais";
    $where_usuario = "AND up.idUsuario = $idUsuario";
} else {
    $join_usuario = "";
    $where_usuario = "";
    // Si no hay usuario, no mostrar nada
    $where_usuario = "AND 1=0";
}

$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int) $_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombrePais';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

// Si filas es -1, significa "todos los registros"
$es_todos = ($filas == -1);

$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

// Campos válidos para ordenamiento
$campos_validos = ['siglaPais', 'nombrePais', 'codMoneda', 'simbolo_moneda'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombrePais';

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

// Determinar campo de ordenamiento según la columna seleccionada
switch ($orden_validado) {
    case 'codMoneda':
        $campo_orden = 'm.codMoneda';
        break;
    case 'simbolo_moneda':
        $campo_orden = 'm.simbolo';
        break;
    case 'siglaPais':
        $campo_orden = 'p.siglaPais';
        break;
    case 'nombrePais':
    default:
        $campo_orden = 'p.nombrePais';
        break;
}

// Consulta con JOIN para obtener la moneda principal
$sql = "SELECT 
            p.idPais,
            p.siglaPais,
            p.nombrePais,
            m.codMoneda,
            m.simbolo as simbolo_moneda
        FROM paises p
        LEFT JOIN paises_monedas pm ON p.idPais = pm.idPais AND pm.es_principal = 1
        LEFT JOIN monedas m ON pm.idMoneda = m.idMoneda
        $join_usuario
        WHERE p.vigente = 1 $where_usuario";

if (!empty($buscar)) {
    $sql .= " AND (p.nombrePais LIKE '%$buscar_escapado%' 
                  OR p.siglaPais LIKE '%$buscar_escapado%' 
                  OR m.codMoneda LIKE '%$buscar_escapado%'
                  OR m.simbolo LIKE '%$buscar_escapado%')";
}

$sql .= " ORDER BY $campo_orden $direccion_validada";

// Solo aplicar LIMIT si NO es "todos los registros"
if (!$es_todos) {
    $sql .= " LIMIT $offset, $filas";
}

$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "html" => "<tr><td colspan='5' class='text-center text-danger'>Error en consulta: " . $conn->error . "</td></tr>",
        "paginacion" => ""
    ]);
    exit;
}

$html = "";
while ($r = $result->fetch_assoc()) {
    // SIEMPRE incluimos las celdas de datos
    $html .= "<tr>
        <td>" . htmlspecialchars($r['siglaPais']) . "</td>
        <td>" . htmlspecialchars($r['codMoneda'] ?? '—') . "</td>
        <td>" . htmlspecialchars($r['simbolo_moneda'] ?? '$') . "</td>
        <td>" . htmlspecialchars($r['nombrePais']) . "</td>";

    // Solo agregamos la columna de acciones si NO es sin_acciones
    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=pais-ver.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idPais' value='{$r['idPais']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=pais-editar.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idPais' value='{$r['idPais']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='eliminar_pais.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idPais' value='{$r['idPais']}'>
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Está seguro de eliminar el país " . htmlspecialchars($r['nombrePais']) . "?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }

    $html .= "</tr>";
}

// Total de registros considerando búsqueda
$sql_total = "SELECT COUNT(DISTINCT p.idPais) as total 
              FROM paises p
              LEFT JOIN paises_monedas pm ON p.idPais = pm.idPais AND pm.es_principal = 1
              LEFT JOIN monedas m ON pm.idMoneda = m.idMoneda
              $join_usuario
              WHERE p.vigente = 1 $where_usuario";

if (!empty($buscar)) {
    $sql_total .= " AND (p.nombrePais LIKE '%$buscar_escapado%' 
                        OR p.siglaPais LIKE '%$buscar_escapado%' 
                        OR m.codMoneda LIKE '%$buscar_escapado%'
                        OR m.simbolo LIKE '%$buscar_escapado%')";
}

$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = $filas > 0 && !$es_todos ? ceil($total / $filas) : 1;

// Paginación con botones Anterior/Siguiente (solo si no es "todos")
$paginacion = "";
if ($totalPaginas > 1 && !$es_todos) {
    // Botón Anterior
    if ($pagina > 1) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='" . ($pagina - 1) . "'>
            <i class='bi bi-chevron-left'></i> Anterior
        </button>";
    }

    // Rango de páginas a mostrar (máximo 5 alrededor de la actual)
    $inicio = max(1, $pagina - 2);
    $fin = min($totalPaginas, $pagina + 2);

    // Mostrar primera página si está fuera del rango
    if ($inicio > 1) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='1'>1</button>";
        if ($inicio > 2) {
            $paginacion .= "<span class='btn btn-sm btn-outline-secondary disabled me-1'>...</span>";
        }
    }

    // Páginas del rango
    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $pagina) ? 'active' : '';
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1 $active' data-page='$i'>$i</button>";
    }

    // Mostrar última página si está fuera del rango
    if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) {
            $paginacion .= "<span class='btn btn-sm btn-outline-secondary disabled me-1'>...</span>";
        }
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='$totalPaginas'>$totalPaginas</button>";
    }

    // Botón Siguiente
    if ($pagina < $totalPaginas) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn' data-page='" . ($pagina + 1) . "'>
            Siguiente <i class='bi bi-chevron-right'></i>
        </button>";
    }
}

// Devolver JSON
echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>