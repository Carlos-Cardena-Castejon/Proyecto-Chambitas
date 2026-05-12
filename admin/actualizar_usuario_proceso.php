<?php
session_start();
include("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellido_p = $_POST['apellido_paterno'];
    $apellido_m = $_POST['apellido_materno'];
    $curp = strtoupper($_POST['curp']);
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];
    
    // 1. Manejo de Foto
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_foto = "perfil_" . $id . "_" . time() . "." . $ext;
        $ruta_destino = "uploads/perfiles/" . $nombre_foto;
        
        if(move_uploaded_file($_FILES['foto']['tmp_name'], "../" . $ruta_destino)) {
            $sql_foto = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
            $st_f = $conexion->prepare($sql_foto);
            $st_f->bind_param("si", $ruta_destino, $id);
            $st_f->execute();
        }
    }

    // 2. Manejo de Contraseña (Solo si se escribió algo)
    if(!empty($_POST['nueva_contrasena'])) {
        $pass_hash = password_hash($_POST['nueva_contrasena'], PASSWORD_DEFAULT);
        $sql_pass = "UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?";
        $st_p = $conexion->prepare($sql_pass);
        $st_p->bind_param("si", $pass_hash, $id);
        $st_p->execute();
    }

    // 3. Actualización de datos generales
    $sql = "UPDATE usuarios SET 
            nombre = ?, 
            apellido_paterno = ?, 
            apellido_materno = ?, 
            curp = ?, 
            correo = ?, 
            telefono = ?, 
            rol = ?, 
            estado = ?, 
            descripcion = ? 
            WHERE id_usuario = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssssisi", 
        $nombre, $apellido_p, $apellido_m, $curp, 
        $correo, $telefono, $rol, $estado, $descripcion, $id
    );

    if($stmt->execute()) {
        header("Location: usuarios_admin.php?msg=editado");
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}