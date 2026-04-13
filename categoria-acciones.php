<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';
require 'validaciones.php';

// ============================================
// CREAR CATEGORÍA
// ============================================
if (isset($_POST['create_categoria'])) {
    $nombre = limpiarInput(trim($_POST['nombre_categoria']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;

    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre de la categoría contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: categoria-crear.php');
        exit;
    }

    $sql = "INSERT INTO categorias (nombre_categoria, descripcion, activo) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $descripcion);

    if ($stmt->execute()) {
        $id_categoria = $stmt->insert_id;
        $_SESSION['mensaje'] = "Categoría '$nombre' creada exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        
        // 🔴 REDIRIGIR CON POST SIN ID EN URL
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirigiendo...</title>
        </head>
        <body>
            <form id="redirectForm" action="menu.php" method="POST">
                <input type="hidden" name="page" value="categoria-ver.php">
                <input type="hidden" name="id" value="<?= $id_categoria ?>">
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        $_SESSION['mensaje'] = 'Error al crear la categoría: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header('Location: categoria-crear.php');
        exit;
    }
}

// ============================================
// ACTUALIZAR CATEGORÍA
// ============================================
if (isset($_POST['update_categoria'])) {
    $id = intval($_POST['categoria_id']);
    
    if (!validarID($id)) {
        $_SESSION['mensaje'] = 'ID de categoría no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=categorias.php');
        exit;
    }
    
    $nombre = limpiarInput(trim($_POST['nombre_categoria']));
    $descripcion = !empty(trim($_POST['descripcion'])) ? limpiarInput(trim($_POST['descripcion'])) : null;

    if (!validarNombreProducto($nombre)) {
        $_SESSION['mensaje'] = 'El nombre de la categoría contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=categoria-editar.php&id=$id");
        exit;
    }

    $sql = "UPDATE categorias SET nombre_categoria = ?, descripcion = ? WHERE id_categoria = ? AND activo = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Categoría '$nombre' actualizada correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        
        // 🔴 REDIRIGIR CON POST SIN ID EN URL
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirigiendo...</title>
        </head>
        <body>
            <form id="redirectForm" action="menu.php" method="POST">
                <input type="hidden" name="page" value="categoria-ver.php">
                <input type="hidden" name="id" value="<?= $id ?>">
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar la categoría: " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header("Location: menu.php?page=categoria-editar.php&id=$id");
        exit;
    }
}

// ============================================
// ELIMINAR CATEGORÍA
// ============================================
if (isset($_POST['borrar_categoria'])) {
    $id = intval($_POST['id_categoria']);
    
    if (!validarID($id)) {
        $_SESSION['mensaje'] = 'ID de categoría no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=categorias.php');
        exit;
    }
    
    // Obtener nombre antes de eliminar
    $sql_nombre = "SELECT nombre_categoria FROM categorias WHERE id_categoria = ? AND activo = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $id);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $categoria = $result_nombre->fetch_assoc();
    $nombre_categoria = $categoria ? $categoria['nombre_categoria'] : 'desconocido';
    $stmt_nombre->close();
    
    // =============================================
    // REGLA 5: Verificar si la categoría tiene productos activos
    // =============================================
    $sql_check_productos = "SELECT COUNT(*) as total FROM productos WHERE id_categoria = ? AND activo = 1";
    $stmt_check = $conn->prepare($sql_check_productos);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();
    
    if ($row['total'] > 0) {
        $_SESSION['mensaje'] = "No se puede eliminar la categoría '$nombre_categoria' porque tiene {$row['total']} producto(s) activo(s).";
        $_SESSION['tipo_mensaje'] = 'warning';
        $stmt_check->close();
        $conn->close();
        header('Location: menu.php?page=categorias.php');
        exit;
    }
    $stmt_check->close();

    $sql = "UPDATE categorias SET activo = 0 WHERE id_categoria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Categoría '$nombre_categoria' eliminada exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "Error al eliminar la categoría: " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    
    header('Location: menu.php?page=categorias.php');
    exit;
}
?>