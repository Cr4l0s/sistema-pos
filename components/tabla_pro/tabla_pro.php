<?php
function tablaPro($endpoint, $ordenDefault = 'id', $columnas = [])
{
    if (empty($endpoint)) {
        echo '<div class="alert alert-danger">Error: Endpoint no definido</div>';
        return;
    }
    
    // Usar constante definida en config.php
    $base_url = defined('BASE_URL_RELATIVE') ? BASE_URL_RELATIVE : '/practica_sventas_desa/';
    ?>
    <div class="table-controls">
        <div></div>
        <div class="d-flex gap-2">
            <button id="exportExcel" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </button>
            <button id="exportPrint" class="btn btn-info btn-sm">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <button id="exportPDF" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </button>
        </div>
    </div>

    <table id="tablaPro" class="table table-striped table-bordered"
        data-endpoint="<?php echo htmlspecialchars($endpoint); ?>"
        data-orden-default="<?php echo htmlspecialchars($ordenDefault); ?>"
        data-base-url="<?php echo $base_url; ?>">
        <thead>
            <tr>
                <?php if (empty($columnas)): ?>
                    <!-- Columnas por defecto -->
                    <th class="sortable" data-col="nombre">Nombre <i class="bi bi-arrow-down-up"></i></th>
                    <th class="sortable" data-col="codigo">Código <i class="bi bi-arrow-down-up"></i></th>
                <?php else: ?>
                    <!-- Columnas personalizadas -->
                    <?php foreach ($columnas as $col => $titulo): ?>
                        <th class="sortable" data-col="<?php echo $col; ?>">
                            <?php echo $titulo; ?> <i class="bi bi-arrow-down-up"></i>
                        </th>
                    <?php endforeach; ?>
                <?php endif; ?>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaProBody">
            <tr>
                <td colspan="<?php echo empty($columnas) ? 3 : count($columnas) + 1; ?>" class="text-center">
                    Cargando datos...
                </td>
            </tr>
        </tbody>
    </table>
    <div id="tablaProPaginacion" class="table-pagination"></div>

    <!-- Script de exportación (integrado en tabla_pro.js) -->
    <?php
}
?>