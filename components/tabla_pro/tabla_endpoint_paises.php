<?php
require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombrePais';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';

$offset = ($pagina - 1) * $filas;

$orden_validado = in_array($orden, ['siglaPais', 'codMoneda', 'nombrePais']) ? $orden : 'nombrePais';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE vigente = 1";
if (!empty($buscar)) {
    $where .= " AND (siglaPais LIKE '%$buscar_escapado%' OR nombrePais LIKE '%$buscar_escapado%')";
}

$sql = "SELECT * FROM paises
        $where
        ORDER BY $orden_validado $direccion_validada
        LIMIT $offset, $filas";
$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['siglaPais']) . "</td>
        <td>" . htmlspecialchars($r['codMoneda']) . "</td>
        <td>" . htmlspecialchars($r['nombrePais']) . "</td>
        <td>
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
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>
    </tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM paises $where";
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