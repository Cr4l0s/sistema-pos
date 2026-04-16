<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

session_start();

//$idUsuario = $_SESSION['usuario_id'] ?? 0;
$idUsuario = 1;  // Forzar admin para QA
$admins = [1, 12];

$idCiudad = $_SESSION['idCiudad'] ?? 0;

header('Content-Type: application/json');

if (!$idCiudad) {
    echo json_encode([
        "html" => "<tr><td colspan='2' class='text-center text-danger'>Error: Ciudad no seleccionada. Por favor, vuelva a seleccionar una ciudad.复制数据",
        "paginacion" => ""
    ]);
    exit;
}

// Verificar que la ciudad pertenece a un país accesible por el usuario
/*
if (!in_array($idUsuario, $admins)) {
    $sql_check = "SELECT COUNT(*) as total 
                  FROM ciudades c
                  INNER JOIN regiones r ON c.idRegion = r.idRegion
                  INNER JOIN paises p ON r.idPais = p.idPais
                  LEFT JOIN usuarios_paises up ON p.idPais = up.idPais
                  WHERE c.idCiudad = $idCiudad 
                    AND c.vigente = 1
                    AND r.vigente = 1
                    AND p.vigente = 1
                    AND up.idUsuario = $idUsuario";
    $check_result = $conn->query($sql_check);
    $check_row = $check_result->fetch_assoc();
    
    if ($check_row['total'] == 0) {
        echo json_encode([
            "html" => "<tr><td colspan='2' class='text-center text-danger'>Error: No tiene acceso a esta ciudad</td></tr>",
            "paginacion" => ""
        ]);
        exit;
    }
}
*/

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombreComuna';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$es_todos = ($filas == -1);
$offset = $es_todos ? 0 : ($pagina - 1) * $filas;

$campos_validos = ['nombreComuna'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombreComuna';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE idCiudad = $idCiudad AND vigente = 1";
if (!empty($buscar)) {
    $where .= " AND nombreComuna LIKE '%$buscar_escapado%'";
}

$sql = "SELECT idComuna, nombreComuna 
        FROM comunas
        $where
        ORDER BY $orden_validado $direccion_validada";

if (!$es_todos) {
    $sql .= " LIMIT $offset, $filas";
}

$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombreComuna']) . "</td>";
    
    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=ver_comuna.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idComuna' value='{$r['idComuna']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=editar_comuna.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idComuna' value='{$r['idComuna']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='eliminar_comuna.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idComuna' value='{$r['idComuna']}'>
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar comuna {$r['nombreComuna']}?\")'><i class='bi bi-trash'></i></button>
            </form>
        
";
    }
    
    $html .= "</tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM comunas $where";
$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = $filas > 0 && !$es_todos ? ceil($total / $filas) : 1;

$paginacion = "";
if ($totalPaginas > 1 && !$es_todos) {
    if ($pagina > 1) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='" . ($pagina - 1) . "' data-filas='$filas' data-buscar='" . htmlspecialchars($buscar) . "' data-orden='$orden_validado' data-direccion='$direccion_validada'>
            <i class='bi bi-chevron-left'></i> Anterior
        </button>";
    }

    $inicio = max(1, $pagina - 2);
    $fin = min($totalPaginas, $pagina + 2);

    if ($inicio > 1) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='1' data-filas='$filas' data-buscar='" . htmlspecialchars($buscar) . "' data-orden='$orden_validado' data-direccion='$direccion_validada'>1</button>";
        if ($inicio > 2) {
            $paginacion .= "<span class='btn btn-sm btn-outline-secondary disabled me-1'>...</span>";
        }
    }

    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $pagina) ? 'active btn-primary' : 'btn-outline-primary';
        $paginacion .= "<button class='btn btn-sm $active pagina-btn me-1' data-page='$i' data-filas='$filas' data-buscar='" . htmlspecialchars($buscar) . "' data-orden='$orden_validado' data-direccion='$direccion_validada'>$i</button>";
    }

    if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) {
            $paginacion .= "<span class='btn btn-sm btn-outline-secondary disabled me-1'>...</span>";
        }
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn me-1' data-page='$totalPaginas' data-filas='$filas' data-buscar='" . htmlspecialchars($buscar) . "' data-orden='$orden_validado' data-direccion='$direccion_validada'>$totalPaginas</button>";
    }

    if ($pagina < $totalPaginas) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn' data-page='" . ($pagina + 1) . "' data-filas='$filas' data-buscar='" . htmlspecialchars($buscar) . "' data-orden='$orden_validado' data-direccion='$direccion_validada'>
            Siguiente <i class='bi bi-chevron-right'></i>
        </button>";
    }
}

echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>