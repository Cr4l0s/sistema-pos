<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Usuarios</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="
        sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
                border: 1 px solid #ddd;
                color: #666;
            }

            .paginacion .actual {
                padding: 5px 10px;
                margin: 0 5px;
                background-color: #007bff;
                color: white;
                border: 1px solid #007bff;
            }

            .paginacion a:hover {
                background-color: #f5f5f5;
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
                            <h4>Listado de Usuarios
                                <a href="usuario-crear.php" class="btn btn-primary float-end"><span class="bi bi-plus-circle-fill"></span>&nbsp;Agregar Usuario</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombres</th>
                                        <th>Apellido Paterno</th>
                                        <th>Apellido Materno</th>
                                        <th>Nombre de Usuario</th>
                                        <th>Teléfono Fijo</th>
                                        <th>Teléfono Celular1</th>
                                        <th>Teléfono Celular2</th>                                                                                
                                        <th>Correo Electrónico</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $resultados_por_pagina = 8;
                                        $pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
                                        $calculo = ($pagina_actual - 1) * $resultados_por_pagina;
                                        $sql_total = 'SELECT COUNT(*) as total FROM usuarios WHERE vigente = 1';
                                        $resultado_total = mysqli_query($conn, $sql_total);
                                        $fila_total = mysqli_fetch_assoc($resultado_total);
                                        $total_registros = $fila_total['total'];
                                        
                                        // Calcular el total de páginas
                                        $total_paginas = ceil($total_registros / $resultados_por_pagina);
                                        
                                        //Hacer la consulta con LIMIT

                                        $sql = "SELECT * FROM usuarios WHERE vigente = 1 LIMIT $calculo, $resultados_por_pagina";
                                        $usuarios = mysqli_query($conn, $sql);
                                        if(mysqli_num_rows($usuarios) > 0) {
                                            foreach($usuarios as $usuario) {
                                        ?>
                                        <tr>
                                            <td><?php echo $usuario['idUsuario']?></td>
                                            <td><?php echo $usuario['nombres']?></td>
                                            <td><?php echo $usuario['ApPaterno']?></td>
                                            <td><?php echo $usuario['ApMaterno']?></td>
                                            <td><?php echo $usuario['NombreUsuario']?></td>
                                            <td><?php echo $usuario['fonofijo']?></td>
                                            <td><?php echo $usuario['fonocelular1']?></td>
                                            <td><?php echo $usuario['fonocelular2']?></td>
                                            <td><?php echo $usuario['email']?></td>
                                            <td>
                                                <a href="usuario-ver.php?idUsuario=<?php echo $usuario['idUsuario']?>" class="btn btn-secondary btn-sm"><span class="bi bi-eye-fill"></span>&nbsp;Ver</a>
                                                <a href="usuario-editar.php?idUsuario=<?php echo $usuario['idUsuario']?>" class="btn btn-success btn-sm"><span class="bi bi-pencil-fill"></span>&nbsp;Editar</a>
                                                <form action="acciones-usuario.php" method="POST" class="d-inline">
                                                    <button onclick="return confirm('¿Confirma la eliminación del usuario?')" type="submit" name="borrar_usuario" value="<?php echo $usuario['idUsuario']?>" class="btn btn-danger btn-sm">
                                                    <span class="bi bi-trash3-fill"></span>&nbsp;Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php 
                                    } 
                                }
                                    else { 
                                        echo '<h5>No se encontraron usuarios</h5>';
                                    }    
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        
        <!-- Enlaces de paginación -->
         <div class="paginacion">
            <?php if($pagina_actual > 1): ?>
                <a href="?pagina=<?php echo ($pagina_actual-1); ?>">Anterior</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if($i == $pagina_actual): ?>
                    <span class="actual"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif;?>
            <?php endfor; ?>
            
            <?php if($pagina_actual < $total_paginas): ?>
                <a href="?pagina=<?php echo ($pagina_actual + 1); ?>">Siguiente</a>
            <?php endif; ?>
         </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="
        sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
