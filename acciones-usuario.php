<?php
session_start();
require 'db.php';

// CREAR USUARIO
if (isset($_POST['create_usuario'])) {
    $nombres = trim($_POST['nombres']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $apPaterno = trim($_POST['apPaterno']);
    $apMaterno = trim($_POST['apMaterno']);
    $username = trim($_POST['username']);
    $fonofijo = trim($_POST['fonofijo']);
    $fonocelular1 = trim($_POST['fonocelular1']);
    $fonocelular2 = trim($_POST['fonocelular2']);

    // Verificar si el email ya existe
    $check_sql = "SELECT idUsuario FROM usuarios WHERE email = ? AND vigente = 1 LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['mensaje'] = 'Ya existe un usuario con el mismo email.';
        $check_stmt->close();
        header('Location: inicio.php');
        exit;
    }
    $check_stmt->close();

    // Insertar usuario
    $sql = "INSERT INTO usuarios (nombres, ApPaterno, ApMaterno, NombreUsuario, fonofijo, fonocelular1, fonocelular2, email, password, vigente) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $nombres, $apPaterno, $apMaterno, $username, $fonofijo, $fonocelular1, $fonocelular2, $email, $password);

    if ($stmt->execute()) {
        $usuario_id = $stmt->insert_id;

        // Insertar roles seleccionados
        if (isset($_POST['roles']) && is_array($_POST['roles'])) {
            $rol_sql = "INSERT INTO usuarios_roles (idUsuario, id_rol) VALUES (?, ?)";
            $rol_stmt = $conn->prepare($rol_sql);
            foreach ($_POST['roles'] as $id_rol) {
                $id_rol = intval($id_rol);
                $rol_stmt->bind_param("ii", $usuario_id, $id_rol);
                $rol_stmt->execute();
            }
            $rol_stmt->close();
        }

        $_SESSION['mensaje'] = 'Usuario creado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al crear usuario: ' . $conn->error;
    }
    $stmt->close();
    header('Location: inicio.php');
    exit;
}

// ACTUALIZAR USUARIO
if (isset($_POST['update_usuario'])) {
    $usuario_id = intval($_POST['usuario_id']);
    $nombres = trim($_POST['nombres']);
    $apPaterno = trim($_POST['apPaterno']);
    $apMaterno = trim($_POST['apMaterno']);
    $username = trim($_POST['username']);
    $fonofijo = trim($_POST['fonofijo']);
    $fonocelular1 = trim($_POST['fonocelular1']);
    $fonocelular2 = trim($_POST['fonocelular2']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $fecha_update = date('Y-m-d H:i:s');

    // Construir consulta base
    $sql = "UPDATE usuarios SET 
            nombres = ?, 
            ApPaterno = ?, 
            ApMaterno = ?, 
            NombreUsuario = ?, 
            fonofijo = ?, 
            fonocelular1 = ?, 
            fonocelular2 = ?, 
            email = ?, 
            fecha_cambio = ?";

    // Si hay nueva contraseña, agregarla
    if (!empty($password)) {
        $sql .= ", password = ?";
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE idUsuario = ? AND vigente = 1";

    $stmt = $conn->prepare($sql);

    // Bind de parámetros según si hay password o no
    if (!empty($password)) {
        $stmt->bind_param("ssssssssssi", 
            $nombres, $apPaterno, $apMaterno, $username, 
            $fonofijo, $fonocelular1, $fonocelular2, $email, 
            $fecha_update, $hashed_password, $usuario_id
        );
    } else {
        $stmt->bind_param("sssssssssi", 
            $nombres, $apPaterno, $apMaterno, $username, 
            $fonofijo, $fonocelular1, $fonocelular2, $email, 
            $fecha_update, $usuario_id
        );
    }

    if ($stmt->execute()) {
        // Actualizar roles: eliminar existentes e insertar nuevos
        $del_sql = "DELETE FROM usuarios_roles WHERE idUsuario = ?";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("i", $usuario_id);
        $del_stmt->execute();
        $del_stmt->close();

        if (isset($_POST['roles']) && is_array($_POST['roles'])) {
            $rol_sql = "INSERT INTO usuarios_roles (idUsuario, id_rol) VALUES (?, ?)";
            $rol_stmt = $conn->prepare($rol_sql);
            foreach ($_POST['roles'] as $id_rol) {
                $id_rol = intval($id_rol);
                $rol_stmt->bind_param("ii", $usuario_id, $id_rol);
                $rol_stmt->execute();
            }
            $rol_stmt->close();
        }

        $_SESSION['mensaje'] = 'Usuario actualizado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar usuario.';
    }
    $stmt->close();
    header('Location: inicio.php');
    exit;
}

// ELIMINAR USUARIO (borrado lógico)
if (isset($_POST['borrar_usuario'])) {
    $usuario_id = intval($_POST['borrar_usuario']);

    $sql = "UPDATE usuarios SET vigente = 0 WHERE idUsuario = ? AND vigente = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['mensaje'] = 'El usuario ha sido eliminado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'El usuario no se pudo eliminar.';
    }
    $stmt->close();
    header('Location: inicio.php');
    exit;
}
?>