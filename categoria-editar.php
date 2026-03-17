<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Recibir ID por POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_categoria'])) {
    $id = intval($_POST['id_categoria']);
} else {
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
                        <form action="categoria-acciones.php" method="POST" onsubmit="return validarFormularioCategoria()">
                            <input type="hidden" name="categoria_id" value="<?= $categoria['id_categoria'] ?>">
                            
                            <div class="mb-3">
                                <label>Nombre de la Categoría <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre_categoria" id="nombre_categoria" 
                                       value="<?= htmlspecialchars($categoria['nombre_categoria']) ?>" required>
                                <small class="text-muted">Solo letras, números y espacios</small>
                            </div>
                            
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3"><?= htmlspecialchars($categoria['descripcion'] ?? '') ?></textarea>
                            </div>

                            <!-- NUEVO: Campo URL de Imagen -->
                            <div class="mb-3">
                                <label>URL de Imagen (opcional)</label>
                                <input type="url" class="form-control" name="url_imagen" id="url_imagen" 
                                       value="<?= htmlspecialchars($categoria['url_imagen'] ?? '') ?>"
                                       placeholder="https://ejemplo.com/imagen-categoria.jpg">
                                <small class="text-muted">Formatos: JPG, PNG, GIF, WEBP</small>
                                <div class="invalid-feedback" id="urlFeedback"></div>
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
        // Validación en tiempo real para URL de imagen
        document.getElementById('url_imagen')?.addEventListener('input', function(e) {
            const url = e.target.value.trim();
            const feedback = document.getElementById('urlFeedback');
            
            if (url === '') {
                e.target.classList.remove('is-invalid', 'is-valid');
                feedback.style.display = 'none';
                return;
            }
            
            if (Validaciones.validarURLImagen(url)) {
                e.target.classList.remove('is-invalid');
                e.target.classList.add('is-valid');
                feedback.style.display = 'none';
            } else {
                e.target.classList.remove('is-valid');
                e.target.classList.add('is-invalid');
                feedback.style.display = 'block';
                feedback.textContent = 'La URL no es una imagen válida (formatos: jpg, png, gif, webp)';
            }
        });

        function validarFormularioCategoria() {
            const nombre = document.getElementById('nombre_categoria')?.value;
            const urlImagen = document.getElementById('url_imagen')?.value;
            
            if (!Validaciones.required(nombre)) {
                alert('El nombre de la categoría es obligatorio');
                return false;
            }
            
            if (urlImagen && !Validaciones.validarURLImagen(urlImagen)) {
                alert('La URL de la imagen no es válida');
                return false;
            }
            
            return true;
        }
    </script>
</body>

</html>