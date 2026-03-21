<?php
session_start();
require 'db.php';
require 'validaciones.php';

// ============================================
// CREAR USUARIO
// ============================================
if (isset($_POST['create_usuario'])) {
    $nombres = limpiarInput(trim($_POST['nombres']));
    $apPaterno = limpiarInput(trim($_POST['apPaterno']));
    $apMaterno = limpiarInput(trim($_POST['apMaterno']));
    $username = limpiarInput(trim($_POST['username']));
    $email = limpiarInput(trim($_POST['email']));
    $password = $_POST['password'];
    $fonofijo = limpiarInput(trim($_POST['fonofijo']));
    $fonocelular1 = limpiarInput(trim($_POST['fonocelular1']));
    $fonocelular2 = limpiarInput(trim($_POST['fonocelular2']));
    $vigente = 1;

    // Validaciones...
    if (!validarNombreUsuario($nombres)) {
        $_SESSION['mensaje'] = 'El nombre contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=usuario-crear.php');
        exit;
    }
    
    if (!validarEmail($email)) {
        $_SESSION['mensaje'] = 'El email no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=usuario-crear.php');
        exit;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['mensaje'] = 'La contraseña debe tener al menos 6 caracteres';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=usuario-crear.php');
        exit;
    }

    // Verificar email único
    $check_sql = "SELECT idUsuario FROM usuarios WHERE email = ? AND vigente = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['mensaje'] = 'Ya existe un usuario con el mismo email';
        $_SESSION['tipo_mensaje'] = 'warning';
        header('Location: menu.php?page=inicio-usuarios.php');
        exit;
    }
    $check_stmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombres, ApPaterno, ApMaterno, NombreUsuario, fonofijo, fonocelular1, fonocelular2, email, password, vigente) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $nombres, $apPaterno, $apMaterno, $username, $fonofijo, $fonocelular1, $fonocelular2, $email, $password_hash, $vigente);

    if ($stmt->execute()) {
        $id_usuario = $stmt->insert_id;
        $_SESSION['mensaje'] = "Usuario '$username' creado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        
        // 🔴 REDIRIGIR CON POST
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirigiendo...</title>
        </head>
        <body>
            <form id="redirectForm" action="menu.php" method="POST">
                <input type="hidden" name="page" value="usuario-ver.php">
                <input type="hidden" name="idUsuario" value="<?= $id_usuario ?>">
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        $_SESSION['mensaje'] = 'Error al crear usuario: ' . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header('Location: menu.php?page=usuario-crear.php');
        exit;
    }
}

// ============================================
// ACTUALIZAR USUARIO
// ============================================
if (isset($_POST['update_usuario'])) {
    $usuario_id = intval($_POST['usuario_id']);
    
    if (!validarID($usuario_id)) {
        $_SESSION['mensaje'] = 'ID de usuario no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio-usuarios.php');
        exit;
    }
    
    // 🔴 NUEVO: Obtener nombre actual antes de actualizar
    $sql_nombre = "SELECT NombreUsuario FROM usuarios WHERE idUsuario = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $usuario_id);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $usuario_actual = $result_nombre->fetch_assoc();
    $nombre_original = $usuario_actual ? $usuario_actual['NombreUsuario'] : 'desconocido';
    $stmt_nombre->close();
    
    $nombres = limpiarInput(trim($_POST['nombres']));
    $apPaterno = limpiarInput(trim($_POST['apPaterno']));
    $apMaterno = limpiarInput(trim($_POST['apMaterno']));
    $fonofijo = limpiarInput(trim($_POST['fonofijo']));
    $fonocelular1 = limpiarInput(trim($_POST['fonocelular1']));
    $fonocelular2 = limpiarInput(trim($_POST['fonocelular2']));        
    $username = limpiarInput(trim($_POST['username']));
    $email = limpiarInput(trim($_POST['email']));
    $password = $_POST['password'] ?? '';
    $fecha_update = date('Y-m-d H:i:s');

    // Validaciones...
    if (!validarNombreUsuario($nombres)) {
        $_SESSION['mensaje'] = 'El nombre contiene caracteres no válidos';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=usuario-editar.php?id=$usuario_id");
        exit;
    }
    
    if (!validarEmail($email)) {
        $_SESSION['mensaje'] = 'El email no es válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=usuario-editar.php?id=$usuario_id");
        exit;
    }

    if (!empty($password) && strlen($password) < 6) {
        $_SESSION['mensaje'] = 'La contraseña debe tener al menos 6 caracteres';
        $_SESSION['tipo_mensaje'] = 'danger';
        header("Location: menu.php?page=usuario-editar.php?id=$usuario_id");
        exit;
    }

    // Construir consulta UPDATE
    $sql = "UPDATE usuarios SET nombres = ?, ApPaterno = ?, ApMaterno = ?, NombreUsuario = ?, 
            fonofijo = ?, fonocelular1 = ?, fonocelular2 = ?, email = ?, fecha_cambio = ?";

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $sql .= " WHERE idUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $nombres, $apPaterno, $apMaterno, $username, 
                         $fonofijo, $fonocelular1, $fonocelular2, $email, $fecha_update, $password_hash, $usuario_id);
    } else {
        $sql .= " WHERE idUsuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $nombres, $apPaterno, $apMaterno, $username, 
                         $fonofijo, $fonocelular1, $fonocelular2, $email, $fecha_update, $usuario_id);
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario '$username' actualizado correctamente.";
        $_SESSION['tipo_mensaje'] = 'success';
        $stmt->close();
        
        // 🔴 REDIRIGIR CON POST
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirigiendo...</title>
        </head>
        <body>
            <form id="redirectForm" action="menu.php" method="POST">
                <input type="hidden" name="page" value="usuario-ver.php">
                <input type="hidden" name="idUsuario" value="<?= $usuario_id ?>">
            </form>
            <script>
                document.getElementById('redirectForm').submit();
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        $_SESSION['mensaje'] = "Error al actualizar usuario '$nombre_original': " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
        $stmt->close();
        header("Location: menu.php?page=usuario-editar.php?id=$usuario_id");
        exit;
    }
}

// ============================================
// ELIMINAR USUARIO
// ============================================
if (isset($_POST['borrar_usuario'])) {
    $usuario_id = intval($_POST['borrar_usuario']);
    
    if (!validarID($usuario_id)) {
        $_SESSION['mensaje'] = 'ID de usuario no válido';
        $_SESSION['tipo_mensaje'] = 'danger';
        header('Location: menu.php?page=inicio-usuarios.php');
        exit;
    }
    
    // 🔴 NUEVO: Obtener nombre antes de eliminar
    $sql_nombre = "SELECT NombreUsuario FROM usuarios WHERE idUsuario = ? AND vigente = 1";
    $stmt_nombre = $conn->prepare($sql_nombre);
    $stmt_nombre->bind_param("i", $usuario_id);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $usuario = $result_nombre->fetch_assoc();
    $nombre_usuario = $usuario ? $usuario['NombreUsuario'] : 'desconocido';
    $stmt_nombre->close();
    
    $sql = "UPDATE usuarios SET vigente = 0 WHERE idUsuario = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario '$nombre_usuario' eliminado exitosamente.";
        $_SESSION['tipo_mensaje'] = 'success';
    } else {
        $_SESSION['mensaje'] = "Error al eliminar usuario '$nombre_usuario': " . $stmt->error;
        $_SESSION['tipo_mensaje'] = 'danger';
    }
    $stmt->close();
    
    header('Location: menu.php?page=inicio-usuarios.php');
    exit;
}
?>