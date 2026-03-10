<?php
require '../../db.php';
require_once '../../config.php';

$pagina = $_GET['pagina'] ?? 1;
$filas = $_GET['filas'] ?? 10;
$orden = $_GET['orden'] ?? 'nombres';
$direccion = $_GET['direccion'] ?? 'ASC';
$buscar = $_GET['buscar'] ?? '';

$offset = ($pagina - 1) * $filas;

// ===== CORRECCIÓN: Agregar 'apellidos' a los campos válidos =====
$campos_validos = ['nombres', 'apellidos', 'NombreUsuario', 'email'];
$orden_validado = in_array($orden, $campos_validos) ? $orden : 'nombres';
// ===============================================================

$direccion_validada = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';
$buscar_escapado = $conn->real_escape_string($buscar);

$where = "WHERE vigente = 1";
if (!empty($buscar)) {
    $where .= " AND (nombres LIKE '%$buscar_escapado%' OR email LIKE '%$buscar_escapado%' OR ApPaterno LIKE '%$buscar_escapado%' OR ApMaterno LIKE '%$buscar_escapado%')";
}

// ===== MEJORA: Manejar ordenamiento por apellidos =====
if ($orden_validado == 'apellidos') {
    // Ordenar por apellido paterno + materno
    $campo_orden = "ApPaterno $direccion_validada, ApMaterno $direccion_validada";
    $sql = "SELECT * FROM usuarios
            $where
            ORDER BY $campo_orden
            LIMIT $offset, $filas";
} else {
    $sql = "SELECT * FROM usuarios
            $where
            ORDER BY $orden_validado $direccion_validada
            LIMIT $offset, $filas";
}
// =====================================================

$result = $conn->query($sql);

$html = "";
while ($r = $result->fetch_assoc()) {
    $apellidos = trim(($r['ApPaterno'] ?? '') . ' ' . ($r['ApMaterno'] ?? ''));
    $html .= "<tr>
        <td>" . htmlspecialchars($r['nombres']) . "</td>
        <td>" . htmlspecialchars($apellidos) . "</td>
        <td>" . htmlspecialchars($r['NombreUsuario']) . "</td>
        <td>" . htmlspecialchars($r['email']) . "</td>
        <td>
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
        </td>
    </tr>";
}

$sql_total = "SELECT COUNT(*) as total FROM usuarios $where";
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