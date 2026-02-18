<?php
session_start();
require 'db.php';

if (isset($_POST['create_usuario'])) {
    $nombres = mysqli_real_escape_string($conn, trim($_POST['nombres']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, password_hash(trim($_POST['password']), PASSWORD_DEFAULT)) : '';
    $apPaterno = mysqli_real_escape_string($conn, trim($_POST['apPaterno']));
    $apMaterno = mysqli_real_escape_string($conn, trim($_POST['apMaterno']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $fonofijo = mysqli_real_escape_string($conn, trim($_POST['fonofijo']));
    $fonocelular1 = mysqli_real_escape_string($conn, trim($_POST['fonocelular1']));
    $fonocelular2 = mysqli_real_escape_string($conn, trim($_POST['fonocelular2']));
    $vigente = 1;

    // Al tratarse de una condición de email único, vamos a verificar si ya existe un usuario con el mismo email.  Nos basta con un solo caso.
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND vigente = 1 LIMIT 1";

    mysqli_query($conn, $sql);

    if (isset($_POST['create_usuario'])) {
        $nombres = mysqli_real_escape_string($conn, trim($_POST['nombres']));
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $apPaterno = mysqli_real_escape_string($conn, trim($_POST['apPaterno']));
        $apMaterno = mysqli_real_escape_string($conn, trim($_POST['apMaterno']));
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $fonofijo = mysqli_real_escape_string($conn, trim($_POST['fonofijo']));
        $fonocelular1 = mysqli_real_escape_string($conn, trim($_POST['fonocelular1']));
        $fonocelular2 = mysqli_real_escape_string($conn, trim($_POST['fonocelular2']));

        // Insertar usuario (SIN id_rol)
        $sql = "INSERT INTO usuarios (nombres, ApPaterno, ApMaterno, NombreUsuario, fonofijo, fonocelular1, fonocelular2, email, password, vigente) 
            VALUES ('$nombres', '$apPaterno', '$apMaterno', '$username', '$fonofijo', '$fonocelular1', '$fonocelular2', '$email', '$password', 1)";

        if (mysqli_query($conn, $sql)) {
            $usuario_id = mysqli_insert_id($conn);

            // Insertar roles seleccionados en usuarios_roles
            if (isset($_POST['roles']) && is_array($_POST['roles'])) {
                foreach ($_POST['roles'] as $id_rol) {
                    $id_rol = mysqli_real_escape_string($conn, $id_rol);
                    $conn->query("INSERT INTO usuarios_roles (idUsuario, id_rol) VALUES ($usuario_id, $id_rol)");
                }
            }

            $_SESSION['mensaje'] = 'Usuario creado exitosamente.';
        } else {
            $_SESSION['mensaje'] = 'Error al crear usuario.';
        }

        header('Location: inicio.php');
        exit;
    }
}

if (isset($_POST['update_usuario'])) {
    $usuario_id = mysqli_real_escape_string($conn, $_POST['usuario_id']);
    $nombres = mysqli_real_escape_string($conn, trim($_POST['nombres']));
    $apPaterno = mysqli_real_escape_string($conn, trim($_POST['apPaterno']));
    $apMaterno = mysqli_real_escape_string($conn, trim($_POST['apMaterno']));
    $fonofijo = mysqli_real_escape_string($conn, trim($_POST['fonofijo']));
    $fonocelular1 = mysqli_real_escape_string($conn, trim($_POST['fonocelular1']));
    $fonocelular2 = mysqli_real_escape_string($conn, trim($_POST['fonocelular2']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $fecha_update = date('Y-m-d H:i:s');

    $sql = "UPDATE usuarios SET 
            nombres = '$nombres', 
            ApPaterno = '$apPaterno', 
            ApMaterno = '$apMaterno', 
            NombreUsuario = '$username', 
            fonofijo = '$fonofijo', 
            fonocelular1 = '$fonocelular1', 
            fonocelular2 = '$fonocelular2', 
            email = '$email', 
            fecha_cambio = '$fecha_update'";

    if (!empty($password)) {
        $sql .= ", password = '" . password_hash($password, PASSWORD_DEFAULT) . "'";
    }

    $sql .= " WHERE idUsuario = '$usuario_id' AND vigente = 1";

    if (mysqli_query($conn, $sql)) {
        // ACTUALIZAR ROLES 
        $conn->query("DELETE FROM usuarios_roles WHERE idUsuario = '$usuario_id'");

        if (isset($_POST['roles']) && is_array($_POST['roles'])) {
            foreach ($_POST['roles'] as $id_rol) {
                $id_rol = mysqli_real_escape_string($conn, $id_rol);
                $conn->query("INSERT INTO usuarios_roles (idUsuario, id_rol) VALUES ($usuario_id, $id_rol)");
            }
        }

        $_SESSION['mensaje'] = 'Usuario actualizado exitosamente.';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar usuario.';
    }

    header('Location: inicio.php');
    exit;
}

if (isset($_POST['borrar_usuario'])) {
    $usuario_id = mysqli_real_escape_string($conn, $_POST['borrar_usuario']);

    //$sql = "DELETE FROM usuarios WHERE idUsuario = '$usuario_id'";
    $sql = "UPDATE usuarios SET vigente = 0 WHERE idUsuario = '$usuario_id' AND vigente = 1"; // Cambiamos el campo vigente a 0 para simular la eliminación lógica.

    mysqli_query($conn, $sql);
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensaje'] = 'El usuario ha sido eliminado exitosamente.';
        header('Location: inicio.php');
        exit;
    } else {
        $_SESSION['mensaje'] = 'El usuario no se pudo eliminar.';
        header('Location: inicio.php');
        exit;
    }
}
?>