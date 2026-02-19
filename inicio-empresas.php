<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .paginacion {
            margin: 20px 0;
            text-align: center;
        }
        .paginacion a {
            padding: 5px 10px;
            margin: 0 5px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #666;
        }
        .paginacion .actual {
            padding: 5px 10px;
            margin: 0 5px;
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensaje.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Listado de Empresas
                            <a href="menu.php" class="btn btn-danger float-end">Volver al Menú</a>
                            <a href="empresa-crear.php" class="btn btn-primary float-end">Agregar Empresa</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>RUT</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Comuna</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $resultados_por_pagina = 8;
                                $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                $calculo = ($pagina_actual - 1) * $resultados_por_pagina;
                                
                                $sql_total = "SELECT COUNT(*) as total FROM empresas WHERE vigente = 1";
                                $stmt_total = $conn->prepare($sql_total);
                                $stmt_total->execute();
                                $total_registros = $stmt_total->get_result()->fetch_assoc()['total'];
                                $total_paginas = ceil($total_registros / $resultados_por_pagina);
                                
                                $sql = "SELECT e.*, c.nomComuna 
                                        FROM empresas e
                                        LEFT JOIN comunas c ON e.idComuna = c.idComuna
                                        WHERE e.vigente = 1 
                                        LIMIT ?, ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ii", $calculo, $resultados_por_pagina);
                                $stmt->execute();
                                $empresas = $stmt->get_result();
                                
                                if ($empresas->num_rows > 0) {
                                    while ($empresa = $empresas->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?= $empresa['idEmpresa'] ?></td>
                                    <td><?= $empresa['rut'] ?></td>
                                    <td><?= $empresa['nombreEmpresa'] ?></td>
                                    <td><?= $empresa['direccion'] ?></td>
                                    <td><?= $empresa['nomComuna'] ?? '—' ?></td>
                                    <td><?= $empresa['telefono'] ?></td>
                                    <td><?= $empresa['email'] ?></td>
                                    <td>
                                        <a href="empresa-ver.php?id=<?= $empresa['idEmpresa'] ?>" class="btn btn-secondary btn-sm">Ver</a>
                                        <a href="empresa-editar.php?id=<?= $empresa['idEmpresa'] ?>" class="btn btn-success btn-sm">Editar</a>
                                        <form action="empresa-acciones.php" method="POST" class="d-inline">
                                            <button onclick="return confirm('¿Eliminar empresa?')" type="submit" name="borrar_empresa" value="<?= $empresa['idEmpresa'] ?>" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="8" class="text-center">No hay empresas registradas</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="paginacion">
        <?php if ($pagina_actual > 1): ?>
            <a href="?pagina=<?= $pagina_actual-1 ?>">Anterior</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <?php if ($i == $pagina_actual): ?>
                <span class="actual"><?= $i ?></span>
            <?php else: ?>
                <a href="?pagina=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($pagina_actual < $total_paginas): ?>
            <a href="?pagina=<?= $pagina_actual+1 ?>">Siguiente</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>