<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
error_log("=== usuario-crear.php está siendo incluido ===");
?>
<div class="container mt-5">
    <?php include('mensaje.php'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Agregar Usuario
                        <a href="menu.php?page=inicio-usuarios.php" class="btn btn-danger float-end">
                            <span class="bi bi-arrow-left"></span>&nbsp;Volver
                        </a>
                    </h4>
                </div>
                <div class="card-body">
                    <form action="acciones-usuario.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombres" required>
                                <small class="text-muted">Mínimo 3 caracteres</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellido Paterno</label>
                                <input type="text" class="form-control" name="apPaterno">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Apellido Materno</label>
                                <input type="text" class="form-control" name="apMaterno">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nombre de Usuario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" required>
                                <small class="text-muted">Mínimo 3 caracteres</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono Fijo</label>
                                <input type="text" class="form-control" name="fonofijo">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Teléfono Celular 1</label>
                                <input type="text" class="form-control" name="fonocelular1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono Celular 2</label>
                                <input type="text" class="form-control" name="fonocelular2">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>

                        <div class="mb-3">
                            <button type="submit" name="create_usuario" class="btn btn-primary">Grabar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="validaciones.js"></script>
<script>
    function validarFormularioUsuario() {
        const nombres = document.querySelector('[name="nombres"]')?.value;
        const username = document.querySelector('[name="username"]')?.value;
        const email = document.querySelector('[name="email"]')?.value;
        const password = document.querySelector('[name="password"]')?.value;
        
        if (!Validaciones.required(nombres)) {
            alert('Los nombres son obligatorios');
            return false;
        }
        
        if (!Validaciones.required(username)) {
            alert('El nombre de usuario es obligatorio');
            return false;
        }
        
        if (!Validaciones.required(email)) {
            alert('El email es obligatorio');
            return false;
        }
        
        if (!Validaciones.validarEmail(email)) {
            alert('El email no es válido');
            return false;
        }
        
        if (!Validaciones.required(password)) {
            alert('La contraseña es obligatoria');
            return false;
        }
        
        if (password.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres');
            return false;
        }
        
        return true;
    }
</script>