<?php
session_start();
include 'db.php';


// Capturar idPais y nombrePais desde el formulario y guardarlos en la sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idPais = $_POST['idPais'];
    $nombrePais = $_POST['nombrePais'];
	
    // Guardar en la sesión
    $_SESSION['idPais'] = $idPais;
    $_SESSION['nombrePais'] = $nombrePais;

    if (substr($idPais, 0, 10) == "Seleccione") {
		$titulo = 'Seleccione País';
		$_SESSION['titulo'] = $titulo;
        echo "No ha seleccionado País <br><br>";
        echo "<a href='selector_pais.php'>Volver a Seleccionar País</a>";
        exit;
    }
} else {
    // Recuperar de la sesión
    $idPais = isset($_SESSION['idPais']) ? $_SESSION['idPais'] : null;
    $nombrePais = isset($_SESSION['nombrePais']) ? $_SESSION['nombrePais'] : null;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mantenedor de Regiones</title>
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

<?php

//Verifica que se haya enviado un país
if ($idPais && $nombrePais) {

	if (substr($idPais, 0, 10) == "Seleccione") {
		$titulo = 'Seleccione País';
		$_SESSION['titulo'] = $titulo;
		echo "No ha seleccionado País <br><br>";
	    echo "<a href='selector_pais2.php'>Volver a Seleccionar País</a>";
		exit;
	}

    // Mostrar las regiones del país seleccionado
    $sql = "SELECT * FROM regiones WHERE idPais = $idPais AND vigente = 1";
    $result = $conn->query($sql);

	// Mostrar el formulario para agregar la nueva región
    echo "<h2>Regiones para el país: " . $nombrePais . "</h2>";
	?>
	<div class="card">
		<div class="card-header">
			<h4>Listado de Regiones</h4>
		</div>
		<div class="card-body">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>ID Región</th>
						<th>Nombre Región</th>
						<th>Código Región</th>
						<th>Acciones</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$resultados_por_pagina = 8;
						$pagina_actual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
						$calculo = ($pagina_actual - 1) * $resultados_por_pagina;
						$sql_total = "SELECT COUNT(*) as total FROM regiones WHERE idPais = '$idPais' AND vigente = 1";
						$resultado_total = mysqli_query($conn, $sql_total);
						$fila_total = mysqli_fetch_assoc($resultado_total);
						$total_registros = $fila_total['total'];
						
						// Calcular el total de páginas
						$total_paginas = ceil($total_registros / $resultados_por_pagina);
						
						//Hacer la consulta con LIMIT

						$sql = "SELECT * FROM regiones WHERE idPais = '$idPais' AND vigente = 1 LIMIT $calculo, $resultados_por_pagina";
						$regiones = mysqli_query($conn, $sql);
						if(mysqli_num_rows($regiones) > 0) {
							foreach($regiones as $region) {
							?>
							<tr>
								<td><?php echo $region['idRegion'] ?></td>
								<td><?php echo $region['nombreRegion'] ?></td>
								<td><?php echo $region['codRegion'] ?></td>
								<td>
									<a href="ver_region.php?idRegion=<?php echo $region['idRegion']?>" class="btn btn-secondary btn-sm"><span class="bi bi-eye-fill"></span>&nbsp;Ver</a>
									<a href="editar_region.php?idRegion=<?php echo $region['idRegion']?>" class="btn btn-success btn-sm"><span class="bi bi-pencil-fill"></span>&nbsp;Editar</a>
									
									<form action="eliminar_region.php" method="POST" class="d-inline">
										<button onclick="return confirm('¿Confirma la eliminación de la Región?')" type="submit" name = "idRegion" value="<?php echo $region['idRegion']?>" class="btn btn-danger btn-sm">
										<span class="bi bi-trash3-fill"></span>&nbsp;Eliminar
										</button>
									</form>
								</td>
							</tr>
							
						<?php 
                            } 
                        }
                        else { 
                            echo '<h5>No se encontraron Regiones</h5>';
                            }    
                        ?>

				</tbody>
			</table>	
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

	<?php

    // Formulario para agregar nueva región
	echo '<br>';
    echo "<h3>Agregar Nueva Región</h3>
          <form method='POST' action='agregar_region.php'>
              <input type='hidden' name='idPais' value='$idPais'>
              <label for='nombreRegion'>Nombre de la Región:</label>
              <input type='text' name='nombreRegion' required>
              <label for='codRegion'>Código de la Región:</label>
              <input type='text' name='codRegion' required>
              <input type='submit' value='Agregar Región'>
          </form>";
} else {
	// Si no se ha enviado un país, redirigir al formulario inicial
    echo "<h2>Seleccione un País para gestionar Regiones</h2>";
    echo "<a href='selector_pais.php'>Volver a Seleccionar País</a>";
}

$conn->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="
        sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
		
</body>
</html>
