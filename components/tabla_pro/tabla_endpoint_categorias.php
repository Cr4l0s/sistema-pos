<?php
require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombre_categoria';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';

$offset = ($pagina - 1) * $filas;

// Validar orden
$orden_validado = in_array($orden, ['nombre_categoria']) ? $orden : 'nombre_categoria';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE activo = 1";
if (!empty($buscar)) {
    $where .= " AND (nombre_categoria LIKE '%$buscar_escapado%' OR descripcion LIKE '%$buscar_escapado%')";
}

// Obtener datos
$sql = "SELECT * FROM categorias
        $where
        ORDER BY $orden_validado $direccion_validada
        LIMIT $offset, $filas";
$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombre_categoria']) . "</td>
        <td>" . htmlspecialchars($r['descripcion'] ?: '—') . "</td>
        <td>
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
        </td>
    </tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM categorias $where";
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