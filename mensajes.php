<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

function setMensaje($texto, $tipo = "success") {
    $_SESSION['flash_mensaje'] = [
        "texto" => $texto,
        "tipo"  => $tipo
    ];
}

function mostrarMensaje() {

    if (!isset($_SESSION['flash_mensaje'])) {
        return;
    }

    $mensaje = $_SESSION['flash_mensaje'];
    $texto   = htmlspecialchars($mensaje['texto']);
    $tipo    = $mensaje['tipo'];

    echo "
    <div id='flash-mensaje' class='alert alert-$tipo alert-dismissible fade show'>
        $texto
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>

    <script>
        setTimeout(function() {
            let mensaje = document.getElementById('flash-mensaje');
            if (mensaje) {
                let alert = new bootstrap.Alert(mensaje);
                alert.close();
            }
        }, " . (FLASH_TIEMPO * 1000) . ");
    </script>
    ";

    unset($_SESSION['flash_mensaje']);
}
?>
