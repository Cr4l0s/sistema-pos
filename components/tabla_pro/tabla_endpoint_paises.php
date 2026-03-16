<?php
require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombrePais';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$offset = ($pagina - 1) * $filas;

// Campos válidos para ordenamiento
$campos_validos = ['siglaPais', 'nombrePais', 'codMoneda', 'simbolo_moneda'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombrePais';

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

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
        WHERE p.vigente = 1";

if (!empty($buscar)) {
    $sql .= " AND (p.nombrePais LIKE '%$buscar_escapado%' OR p.siglaPais LIKE '%$buscar_escapado%' OR m.codMoneda LIKE '%$buscar_escapado%')";
}

$sql .= " ORDER BY p.$orden_validado $direccion_validada
          LIMIT $offset, $filas";

$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['siglaPais']) . "</td>
        <td>" . htmlspecialchars($r['codMoneda'] ?? '—') . "</td>
        <td>" . htmlspecialchars($r['simbolo_moneda'] ?? '$') . "</td>
        <td>" . htmlspecialchars($r['nombrePais']) . "</td>";
    
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
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>";
    }
    
    $html .= "</tr>";
}

// Total de registros (para paginación)
$sql_total = "SELECT COUNT(*) as total FROM paises WHERE vigente = 1";
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