<?php

session_start();
include("../conexion.php");

$id_envia = $_SESSION['id_usuario'];
$id_recibe = $_POST['destino'];
$id_publicacion = $_POST['id_publicacion'];
$mensaje = $_POST['mensaje'];

$sql = "INSERT INTO mensajes
(id_usuario_envia, id_usuario_recibe, id_publicacion, mensaje)
VALUES
('$id_envia','$id_recibe','$id_publicacion','$mensaje')";

$conexion->query($sql);

header("Location: chat.php?id_usuario=$id_recibe&id_publicacion=$id_publicacion");