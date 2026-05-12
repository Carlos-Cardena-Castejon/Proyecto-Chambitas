<?php

session_start();
include("../conexion.php");

$id_propuesta = $_GET['id'];

$sql = "SELECT * FROM propuestas 
WHERE id_propuesta = '$id_propuesta'";

$result = $conexion->query($sql);
$propuesta = $result->fetch_assoc();

$id_publicacion = $propuesta['id_publicacion'];
$id_usuario = $propuesta['id_usuario'];

/* CREAR CONTRATACION */

$sqlInsert = "INSERT INTO contrataciones
(id_publicacion,id_usuario,estado)
VALUES
('$id_publicacion','$id_usuario','Aceptado')";

$conexion->query($sqlInsert);

/* ACTUALIZAR ESTADO DE PROPUESTA */

$sqlUpdate = "UPDATE propuestas
SET estado = 'Aceptada'
WHERE id_propuesta = '$id_propuesta'";

$conexion->query($sqlUpdate);

header("Location: ../mis_publicaciones.php");
exit;

?>