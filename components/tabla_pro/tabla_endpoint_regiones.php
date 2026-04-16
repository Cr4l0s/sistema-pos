<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

session_start();

// Obtener usuario logueado
$idUsuario = $_SESSION['usuario_id'] ?? 0;
$admins = [1, 12];

$idPais = isset($_GET['idPais']) ? (int) $_GET['idPais'] : 0;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int) $_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombreRegion';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

if ($idPais == 0) {
    echo json_encode([
        "html" => "<td><td colspan='3' class='text-center text-danger'>Error: No se ha seleccionado un país</td></table>",
        "paginacion" => ""
    ]);
    exit;
}

// Verificar que el país pertenece al usuario (a menos que sea administrador)
/*
if (!in_array($idUsuario, $admins)) {
    $sql_check = "SELECT COUNT(*) as total 
                  FROM paises p
                  LEFT JOIN usuarios_paises up ON p.idPais = up.idPais
                  WHERE p.idPais = $idPais 
                    AND p.vigente = 1
                    AND up.idUsuario = $idUsuario";
    $check_result = $conn->query($sql_check);
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['total'] == 0) {
        echo json_encode([
            "html" => "<td><td colspan='3' class='text-center text-danger'>Error: No tiene acceso a este país</td></tr>",
            "paginacion" => ""
        ]);
        exit;
    }
}
*/
$es_todos = ($filas == -1);
$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

$campos_validos = ['nombreRegion', 'codRegion'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombreRegion';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE vigente = 1 AND idPais = $idPais";
if (!empty($buscar)) {
    $where .= " AND (nombreRegion LIKE '%$buscar_escapado%' 
                  OR codRegion LIKE '%$buscar_escapado%')";
}

$sql_total = "SELECT COUNT(*) as total FROM regiones $where";
$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = $filas > 0 && !$es_todos ? ceil($total / $filas) : 1;

$sql = "SELECT idRegion, nombreRegion, codRegion 
        FROM regiones 
        $where
        ORDER BY $orden_validado $direccion_validada";

if (!$es_todos && $filas > 0) {
    $sql .= " LIMIT $offset, $filas";
}

$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($r['nombreRegion']) . '</td>';
    $html .= '<td>' . htmlspecialchars($r['codRegion']) . '</td>';
    
    if (!$sin_acciones) {
        $html .= '<td>';
        $html .= '<form action="menu.php?page=ver_region.php" method="POST" style="display:inline;">
                    <input type="hidden" name="idRegion" value="' . $r['idRegion'] . '">
                    <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></button>
                  </form>';
        $html .= '<form action="menu.php" method="POST" style="display:inline;">
                    <input type="hidden" name="page" value="editar_region.php">
                    <input type="hidden" name="idRegion" value="' . $r['idRegion'] . '">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-pencil"></i></button>
                  </form>';
        $html .= '<form action="eliminar_region.php" method="POST" style="display:inline;">
                    <input type="hidden" name="idRegion" value="' . $r['idRegion'] . '">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Está seguro de eliminar la región ' . htmlspecialchars($r['nombreRegion']) . '?\')"><i class="bi bi-trash"></i></button>
                  </form>';
        $html .= '</td>';
    }
    
    $html .= '</tr>';
}

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