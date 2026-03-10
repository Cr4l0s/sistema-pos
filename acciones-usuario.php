<?php
session_start();
require 'db.php';

// CREAR USUARIO
if (isset($_POST['create_usuario'])) {
    $nombres = mysqli_real_escape_string($conn, trim($_POST['nombres']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = isset($_POST['password']) ? mysqli_real_escape_string($conn, password_hash(trim($_POST['password']), PASSWORD_DEFAULT)) : '';
    $apPaterno = mysqli_real_escape_string($conn, trim($_POST['apPaterno']));
    $apMaterno = mysqli_real_escape_string($conn, trim($_POST['apMaterno']));
    $username =  mysqli_real_escape_string($conn, trim($_POST['username']));
    $fonofijo = mysqli_real_escape_string($conn, trim($_POST['fonofijo']));
    $fonocelular1 = mysqli_real_escape_string($conn, trim($_POST['fonocelular1']));
    $fonocelular2 = mysqli_real_escape_string($conn, trim($_POST['fonocelular2']));
    $vigente = 1;

    // Verificar email único
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND vigente = 1 LIMIT 1";
    mysqli_query($conn, $sql);

    if(mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensaje'] = 'Ya existe un usuario con el mismo email, por lo que no se agregará.';
        $_SESSION['tipo_mensaje'] = 'warning';  // 🟡 Amarillo
        header('Location: menu.php?page=inicio-usuarios.php');
        exit;
    }

    $sql = "INSERT INTO usuarios (nombres, ApPaterno, ApMaterno, NombreUsuario, fonofijo, fonocelular1, fonocelular2, email, password, vigente) 
            VALUES ('$nombres', '$apPaterno', '$apMaterno', '$username', '$fonofijo', '$fonocelular1', '$fonocelular2', '$email', '$password', '1')";

    mysqli_query($conn, $sql);

    if(mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensaje'] = 'Usuario creado en forma exitosa.';
        $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
    } else {
        $_SESSION['mensaje'] = 'Usuario no se pudo crear.';
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }
    header('Location: menu.php?page=inicio-usuarios.php');
    exit;
}
 
// ACTUALIZAR USUARIO
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

    $sql = "UPDATE usuarios SET nombres = '$nombres', ApPaterno = '$apPaterno', ApMaterno = '$apMaterno', 
            NombreUsuario = '$username', fonofijo = '$fonofijo', fonocelular1 = '$fonocelular1', 
            fonocelular2 = '$fonocelular2', email = '$email', fecha_cambio = '$fecha_update'";

    if(!empty($password)) {
        $sql.= ", password = '" . password_hash($password, PASSWORD_DEFAULT) . "'";
    }

    $sql.= " WHERE idUsuario = '$usuario_id' AND vigente = 1";

    mysqli_query($conn, $sql);

    if(mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensaje'] = 'Usuario actualizado en forma exitosa.';
        $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
    } else {
        $_SESSION['mensaje'] = 'Usuario no se pudo actualizar.';
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }
    header('Location: menu.php?page=inicio-usuarios.php');
    exit;
}

// ELIMINAR USUARIO
if (isset($_POST['borrar_usuario'])) {
    $usuario_id = mysqli_real_escape_string($conn, $_POST['borrar_usuario']);
    
    $sql = "UPDATE usuarios SET vigente = 0 WHERE idUsuario = '$usuario_id' AND vigente = 1";
    mysqli_query($conn, $sql);
    
    if(mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensaje'] = 'El usuario ha sido eliminado exitosamente.';
        $_SESSION['tipo_mensaje'] = 'success';  // 🟢 Verde
    } else {
        $_SESSION['mensaje'] = 'El usuario no se pudo eliminar.';
        $_SESSION['tipo_mensaje'] = 'danger';  // 🔴 Rojo
    }
    header('Location: menu.php?page=inicio-usuarios.php');
    exit;
}
?>