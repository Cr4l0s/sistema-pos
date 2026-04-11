<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

// ============================================
// RECEPCIÓN DE PARÁMETROS
// ============================================

// 🔴 LOG DE TODOS LOS PARÁMETROS RECIBIDOS
error_log("=== GET COMPLETO ===");
error_log(print_r($_GET, true));

$idPais = isset($_GET['idPais']) ? (int) $_GET['idPais'] : 0;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int) $_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombreRegion';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

// Si no hay idPais, devolver error
if ($idPais == 0) {
    error_log("ERROR: idPais es 0");
    echo json_encode([
        "html" => "<tr><td colspan='3' class='text-center text-danger'>Error: No se ha seleccionado un país</td> </tr>",
        "paginacion" => ""
    ]);
    exit;
}

// Si filas es -1, significa "todos los registros"
$es_todos = ($filas == -1);

// Calcular offset correctamente
$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

// 🔴 LOG DE VALORES CALCULADOS
error_log("=== TABLA ENDPOINT REGIONES ===");
error_log("idPais: " . $idPais);
error_log("Página: " . $pagina);
error_log("Filas: " . $filas);
error_log("Offset: " . $offset);
error_log("es_todos: " . ($es_todos ? 'true' : 'false'));

// Campos válidos para ordenamiento
$campos_validos = ['nombreRegion', 'codRegion'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombreRegion';

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

// Filtrar por idPais
$where = "WHERE vigente = 1 AND idPais = $idPais";
if (!empty($buscar)) {
    $where .= " AND (nombreRegion LIKE '%$buscar_escapado%' 
                  OR codRegion LIKE '%$buscar_escapado%')";
}

// Contar total de registros (para paginación)
$sql_total = "SELECT COUNT(*) as total FROM regiones $where";
$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = $filas > 0 && !$es_todos ? ceil($total / $filas) : 1;

error_log("Total registros: " . $total);
error_log("Total páginas: " . $totalPaginas);

// ============================================
// CONSULTA PRINCIPAL
// ============================================

$sql = "SELECT idRegion, nombreRegion, codRegion 
        FROM regiones 
        $where
        ORDER BY $orden_validado $direccion_validada";

// Aplicar LIMIT solo si NO es "todos" Y hay filas > 0
if (!$es_todos && $filas > 0) {
    $sql .= " LIMIT $offset, $filas";
}

error_log("SQL FINAL: " . $sql);

$result = $conn->query($sql);

if (!$result) {
    error_log("ERROR EN SQL: " . $conn->error);
    echo json_encode([
        "html" => "<tr><td colspan='3' class='text-center text-danger'>Error en consulta: " . $conn->error . "</td> </tr>",
        "paginacion" => ""
    ]);
    exit;
}

// ============================================
// GENERAR HTML DE LA TABLA
// ============================================

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombreRegion']) . "</td>
        <td>" . htmlspecialchars($r['codRegion']) . "</td>";

    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=ver_region.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idRegion' value='{$r['idRegion']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php' method='POST' style='display:inline;'>
                <input type='hidden' name='page' value='editar_region.php'>
                <input type='hidden' name='idRegion' value='{$r['idRegion']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='eliminar_region.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idRegion' value='{$r['idRegion']}'>
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Está seguro de eliminar la región " . htmlspecialchars($r['nombreRegion']) . "?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }

    $html .= "</tr>";
}

// ============================================
// GENERAR PAGINACIÓN
// ============================================

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

// ============================================
// RESPUESTA JSON
// ============================================

echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>