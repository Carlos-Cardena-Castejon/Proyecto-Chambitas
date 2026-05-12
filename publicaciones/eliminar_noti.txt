<?php
session_start();
include("../conexion.php");

// Verificamos que el usuario esté logueado y que venga un ID
if(!isset($_SESSION['id_usuario']) || !isset($_GET['id'])){
    exit("error");
}

$id_noti = (int)$_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

// Solo permitimos borrar si la notificación le pertenece al usuario logueado (SEGURIDAD)
$sql = "DELETE FROM notificaciones WHERE id_notificacion = ? AND id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_noti, $id_usuario);

if($stmt->execute()){
    echo "ok";
} else {
    echo "error";
}
?>