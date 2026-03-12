<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombre_producto';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';
$sin_acciones = isset($_GET['sin_acciones']) ? true : false;

$offset = ($pagina - 1) * $filas;

$campos_validos = [
    'nombre_producto', 
    'precio_venta', 
    'stock_actual', 
    'categoria',
    'precio_compras',  
    'codigo_barras'    
];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombre_producto';

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE p.activo = 1";
if (!empty($buscar)) {
    $where .= " AND (p.nombre_producto LIKE '%$buscar_escapado%' OR p.codigo_barras LIKE '%$buscar_escapado%')";
}

if ($orden_validado == 'categoria') {
    $campo_orden = 'c.nombre_categoria';
} else {
    $campo_orden = "p.$orden_validado";
}

$sql = "SELECT p.*, c.nombre_categoria 
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
        $where
        ORDER BY $campo_orden $direccion_validada
        LIMIT $offset, $filas";

$result = $conn->query($sql);

// ===== GENERAR SOLO LAS FILAS (SIN ENCABEZADO) =====
$html = "";
if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $flag_tienda = ($r['mostrar_en_tienda'] ?? 1) ? 'Sí' : 'No';
        
        $html .= "<tr>
            <td>" . htmlspecialchars($r['codigo_barras'] ?? '—') . "</td>
            <td>" . htmlspecialchars($r['nombre_producto'] ?? '') . "</td>
            <td>" . htmlspecialchars($r['nombre_categoria'] ?? '—') . "</td>
            <td>$" . number_format($r['precio_compras'] ?? 0, 0, ',', '.') . "</td>
            <td>$" . number_format($r['precio_venta'] ?? 0, 0, ',', '.') . "</td>
            <td>" . ($r['stock_actual'] ?? 0) . "</td>
            <td>" . $flag_tienda . "</td>";
        
        if (!$sin_acciones) {
            $html .= "<td>
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
            </td>";
        }
        
        $html .= "</tr>";
    }
} else {
    $colspan = $sin_acciones ? 7 : 8;
    $html = "<tr><td colspan='$colspan' class='text-center'>No hay productos</td></tr>";
}

// ===== PAGINACIÓN =====
$sql_total = "SELECT COUNT(*) as total FROM productos p WHERE p.activo = 1";
$total_result = $conn->query($sql_total);
$total = 0;
if ($total_result && $total_result->num_rows > 0) {
    $row = $total_result->fetch_assoc();
    $total = $row['total'] ?? 0;
}
$totalPaginas = $filas > 0 ? ceil($total / $filas) : 1;

$paginacion = "";
if ($totalPaginas > 1) {
    for ($i = 1; $i <= $totalPaginas; $i++) {
        $paginacion .= "<button class='btn btn-sm btn-outline-primary pagina-btn' data-page='$i'>$i</button>";
    }
}

// ===== RESPUESTA JSON =====
echo json_encode([
    "html" => $html,
    "paginacion" => $paginacion
]);
?>