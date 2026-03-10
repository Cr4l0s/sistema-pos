<?php 
if(isset($_SESSION['mensaje'])):
    $tipo = $_SESSION['tipo_mensaje'] ?? 'warning';
?>
<div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['mensaje']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php
    unset($_SESSION['mensaje']);
    unset($_SESSION['tipo_mensaje']);
    endif;
?>