<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST (desde menu.php) o GET
$id = 0;

if (isset($_POST['id_categoria'])) {
    $id = intval($_POST['id_categoria']);
} elseif (isset($_GET['id_categoria'])) {
    $id = intval($_GET['id_categoria']);
} elseif (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if ($id == 0) {
    $_SESSION['mensaje'] = 'ID de categoría no proporcionado';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: categorias.php');
    exit;
}

// Obtener datos de la categoría
$sql = "SELECT * FROM categorias WHERE id_categoria = ? AND activo = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['mensaje'] = 'Categoría no encontrada';
    $_SESSION['tipo_mensaje'] = 'danger';
    header('Location: categorias.php');
    exit;
}

$categoria = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorías - Editar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-5">
        <?php include('mensaje.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Editar Categoría
                            <a href="menu.php?page=categorias.php" class="btn btn-danger float-end">
                                <span class="bi bi-arrow-left"></span>&nbsp;Volver
                            </a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="categoria-acciones.php" method="POST">
                            <input type="hidden" name="categoria_id" value="<?= $categoria['id_categoria'] ?>">
                            
                            <div class="mb-3">
                                <label>Nombre de la Categoría <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre_categoria" id="nombre_categoria" value="<?= htmlspecialchars($categoria['nombre_categoria']) ?>" autocomplete="off" required>
                                <small class="text-muted">Solo letras, números y espacios</small>
                            </div>
                            
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" name="update_categoria" class="btn btn-primary">Actualizar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="validaciones.js"></script>
    <script>
        function validarFormularioCategoria() {
            const nombre = document.getElementById('nombre_categoria')?.value;
            
            if (!Validaciones.required(nombre)) {
                alert('El nombre de la categoría es obligatorio');
                return false;
            }
            
            return true;
        }
    </script>
</body>

</html>