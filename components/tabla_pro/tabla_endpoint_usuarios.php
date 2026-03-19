<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombres';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

// Si filas es -1, significa "todos los registros"
$es_todos = ($filas == -1);

$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

// Campos válidos para ordenamiento
$campos_validos = ['nombres', 'apellidos', 'NombreUsuario', 'email'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombres';

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

// Consulta principal
$where = "WHERE vigente = 1";
if (!empty($buscar)) {
    $where .= " AND (nombres LIKE '%$buscar_escapado%' 
                  OR email LIKE '%$buscar_escapado%' 
                  OR ApPaterno LIKE '%$buscar_escapado%' 
                  OR ApMaterno LIKE '%$buscar_escapado%'
                  OR NombreUsuario LIKE '%$buscar_escapado%')";
}

// Manejo especial para ordenar por apellidos
if ($orden_validado == 'apellidos') {
    $campo_orden = "ApPaterno $direccion_validada, ApMaterno $direccion_validada";
    $sql = "SELECT idUsuario, nombres, ApPaterno, ApMaterno, NombreUsuario, email 
            FROM usuarios
            $where
            ORDER BY $campo_orden";
} else {
    $sql = "SELECT idUsuario, nombres, ApPaterno, ApMaterno, NombreUsuario, email 
            FROM usuarios
            $where
            ORDER BY $orden_validado $direccion_validada";
}

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
    $apellidos = trim(($r['ApPaterno'] ?? '') . ' ' . ($r['ApMaterno'] ?? ''));
    // SIEMPRE incluimos las celdas de datos
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombres']) . "</td>
        <td>" . htmlspecialchars($apellidos ?: '—') . "</td>
        <td>" . htmlspecialchars($r['NombreUsuario']) . "</td>
        <td>" . htmlspecialchars($r['email']) . "</td>";
    
    // Solo agregamos la columna de acciones si NO es sin_acciones
    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=usuario-ver.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idUsuario' value='{$r['idUsuario']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=usuario-editar.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idUsuario' value='{$r['idUsuario']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='acciones-usuario.php' method='POST' style='display:inline;'>
                <input type='hidden' name='borrar_usuario' value='{$r['idUsuario']}'>
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }
    
    $html .= "</tr>";
}

// Total de registros considerando búsqueda
$sql_total = "SELECT COUNT(*) as total FROM usuarios $where";
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