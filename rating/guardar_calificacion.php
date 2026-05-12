<?php
include("../conexion.php");
session_start();

$id_contratacion = $_POST['id_contratacion'];
$puntuacion = $_POST['puntuacion'];
$comentario = $_POST['comentario'];

$id_calificador = $_SESSION['id_usuario'];

$sql = "SELECT id_usuario 
        FROM contrataciones 
        WHERE id_contratacion='$id_contratacion'";

$result = $conexion->query($sql);
$row = $result->fetch_assoc();

$id_calificado = $row['id_usuario'];

$sql2 = "INSERT INTO calificaciones 
(id_contratacion,id_calificador,id_calificado,puntuacion,comentario)
VALUES
('$id_contratacion','$id_calificador','$id_calificado','$puntuacion','$comentario')";

$conexion->query($sql2);

header("Location: ../feed.php");
?>