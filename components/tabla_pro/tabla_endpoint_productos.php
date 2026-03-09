<?php
require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombre_producto';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';

$offset = ($pagina - 1) * $filas;

$orden_validado = in_array($orden, ['nombre_producto', 'precio_venta', 'stock_actual']) ? $orden : 'nombre_producto';
$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE p.activo = 1";
if (!empty($buscar)) {
    $where .= " AND (p.nombre_producto LIKE '%$buscar_escapado%' OR p.codigo_barras LIKE '%$buscar_escapado%')";
}

$sql = "SELECT p.*, c.nombre_categoria 
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        $where
        ORDER BY $orden_validado $direccion_validada
        LIMIT $offset, $filas";
$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $html .= "<tr>
        <td>" . htmlspecialchars($r['codigo_barras'] ?: '—') . "</td>
        <td>" . htmlspecialchars($r['nombre_producto']) . "</td>
        <td>" . htmlspecialchars($r['nombre_categoria'] ?: '—') . "</td>
        <td>$" . number_format($r['precio_venta'], 0, ',', '.') . "</td>
        <td>" . $r['stock_actual'] . "</td>
        <td>
            <form action='menu.php?page=producto-ver.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='{$r['id_producto']}'>
                <button type='submit' class='btn btn-sm btn-secondary'><i class='bi bi-eye'></i></button>
            </form>
            <form action='menu.php?page=producto-editar.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='{$r['id_producto']}'>
                <button type='submit' class='btn btn-sm btn-success'><i class='bi bi-pencil'></i></button>
            </form>
            <form action='eliminar_producto.php' method='POST' style='display:inline;'>
                <input type='hidden' name='id_producto' value='{$r['id_producto']}'>
                <button type='submit' class='btn btn-sm btn-danger' onclick='return confirm(\"¿Eliminar?\")'><i class='bi bi-trash'></i></button>
            </form>
        </td>
    </tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM productos p WHERE p.activo = 1";
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