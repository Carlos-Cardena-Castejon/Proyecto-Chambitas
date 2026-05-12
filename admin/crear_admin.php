<?php

include("conexion.php");

$nombre = "Admin";
$apellido_paterno = "Sistema";
$apellido_materno = "Principal";
$curp = "ADMS000101HDFXXX01";
$correo = "admin@chambitas.com";
$telefono = "0000000000";
$contrasena = "admin123";

// generar hash de contraseña
$hash = password_hash($contrasena, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios 
(nombre, apellido_paterno, apellido_materno, curp, correo, telefono, contrasena, rol)
VALUES
('$nombre','$apellido_paterno','$apellido_materno','$curp','$correo','$telefono','$hash','admin')";

if($conexion->query($sql)){
    echo "Administrador creado correctamente";
}else{
    echo "Error: " . $conexion->error;
}

?>