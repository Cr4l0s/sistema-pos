<?php
require '../../db.php';
require_once '../../config.php';

session_start();
$idCiudad = $_SESSION['idCiudad'] ?? 0;

if (!$idCiudad) {
    echo json_encode(["html" => "<tr><td colspan='2' class='text-center'>Error: Ciudad no seleccionada</td></tr>", "paginacion" => ""]);
    exit;
}

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombreComuna';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$offset = ($pagina - 1) * $filas;

$orden_validado = in_array($orden, ['nombreComuna']) ? $orden : 'nombreComuna';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE idCiudad = $idCiudad AND vigente = 1";
if (!empty($buscar)) {
    $where .= " AND nombreComuna LIKE '%$buscar_escapado%'";
}

$sql = "SELECT * FROM comunas
        $where
        ORDER BY $orden_validado $direccion_validada
        LIMIT $offset, $filas";
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
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }
    
    $html .= "</tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM comunas $where";
$total = $conn->query($sql_total)->fetch_assoc()['total'];
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