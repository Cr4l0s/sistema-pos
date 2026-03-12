<?php
require '../../db.php';
require_once '../../config.php';

session_start();
$idRegion = $_SESSION['idRegion'] ?? 0;

header('Content-Type: application/json');

if (!$idRegion) {
    echo json_encode([
        "html" => "<tr><td colspan='2' class='text-center text-danger'>Error: Región no seleccionada</td></tr>",
        "paginacion" => ""
    ]);
    exit;
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
$orden = $_GET['orden'] ?? 'nombreCiudad';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$offset = ($pagina - 1) * $filas;

$orden_validado = in_array($orden, ['nombreCiudad']) ? $orden : 'nombreCiudad';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE idRegion = $idRegion AND vigente = 1";
if (!empty($buscar)) {
    $where .= " AND nombreCiudad LIKE '%$buscar_escapado%'";
}

$sql = "SELECT * FROM ciudades
        $where
        ORDER BY $orden_validado $direccion_validada
        LIMIT $offset, $filas";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode([
        "html" => "<tr><td colspan='2' class='text-center text-danger'>Error en consulta: " . $conn->error . "</td></tr>",
        "paginacion" => ""
    ]);
    exit;
}

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombreCiudad']) . "</td>";
    
    if (!$sin_acciones) {
        $html .= "<td>
            <form action='menu.php?page=ver_ciudad.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idCiudad' value='{$r['idCiudad']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=editar_ciudad.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idCiudad' value='{$r['idCiudad']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='eliminar_ciudad.php' method='POST' style='display:inline;'>
                <input type='hidden' name='idCiudad' value='{$r['idCiudad']}'>
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }
    
    $html .= "</tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM ciudades $where";
$total_result = $conn->query($sql_total);
$total = $total_result ? $total_result->fetch_assoc()['total'] : 0;
$totalPaginas = ceil($total / $filas);

$paginacion = "";
for ($i = 1; $i <= $totalPaginas; $i++) {
    $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn' data-page='$i'>$i</button>";
}

echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>