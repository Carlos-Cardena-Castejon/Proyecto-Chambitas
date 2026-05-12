<?php

session_start();
include("../conexion.php");

/* ==========================
VERIFICAR SESION
========================== */

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

/* ==========================
VERIFICAR PARAMETROS
========================== */

if(!isset($_GET['id_propuesta']) || !isset($_GET['id_publicacion'])){
    die("Datos incompletos");
}

$id_propuesta = (int) $_GET['id_propuesta'];
$id_publicacion = (int) $_GET['id_publicacion'];


/* ==========================
VERIFICAR SI YA HAY PROPUESTA ACEPTADA
========================== */

$sqlCheck = "SELECT id_propuesta 
FROM propuestas
WHERE id_publicacion = $id_publicacion
AND estado = 'Aceptada'";

$resCheck = $conexion->query($sqlCheck);

if($resCheck->num_rows > 0){

echo "<script>
alert('Este trabajo ya tiene una propuesta aceptada');
window.location.href='ver_propuestas.php?id_publicacion=$id_publicacion';
</script>";

exit();

}


/* ==========================
OBTENER USUARIO DE LA PROPUESTA
========================== */

$sqlUser = "SELECT id_usuario 
FROM propuestas 
WHERE id_propuesta = $id_propuesta";

$resUser = $conexion->query($sqlUser);

if($resUser->num_rows == 0){
    die("No se encontró la propuesta");
}

$rowUser = $resUser->fetch_assoc();
$id_usuario_trabajador = $rowUser['id_usuario'];


/* ==========================
ACEPTAR PROPUESTA
========================== */

$sql1 = "UPDATE propuestas 
SET estado='Aceptada'
WHERE id_propuesta = $id_propuesta";

$conexion->query($sql1);


/* ==========================
RECHAZAR LAS DEMAS
========================== */

$sql2 = "UPDATE propuestas
SET estado='Rechazada'
WHERE id_publicacion = $id_publicacion
AND id_propuesta != $id_propuesta";

$conexion->query($sql2);


/* ==========================
CREAR CONTRATACION
========================== */

$sqlContratacion = "INSERT INTO contrataciones
(id_propuesta, id_publicacion, id_usuario, estado)
VALUES
($id_propuesta, $id_publicacion, $id_usuario_trabajador, 'Aceptado')";

if(!$conexion->query($sqlContratacion)){
    die("Error al crear contratación: " . $conexion->error);
}


/* ==========================
ACTUALIZAR ESTADO DEL TRABAJO
========================== */

$sql3 = "UPDATE Publicaciones
SET estado='En progreso'
WHERE id_publicacion = $id_publicacion";

$conexion->query($sql3);


/* ==========================
REDIRECCION
========================== */

header("Location: ver_propuestas.php?id_publicacion=".$id_publicacion);
exit();

?>