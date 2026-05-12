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
VERIFICAR PARAMETRO
========================== */
if(!isset($_GET['id_publicacion'])){
    die("Publicación no encontrada");
}

$id_publicacion = (int) $_GET['id_publicacion'];
$id_usuario = $_SESSION['id_usuario'];

/* ==========================
VERIFICAR QUE EL TRABAJO SEA DEL USUARIO
========================== */
$sqlCheck = "SELECT id_publicacion 
             FROM publicaciones
             WHERE id_publicacion = ? 
             AND id_usuario = ?";

$stmtCheck = $conexion->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $id_publicacion, $id_usuario);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if($resCheck->num_rows == 0){
    die("No tienes permiso para finalizar este trabajo");
}

/* ==========================
OBTENER CONTRATACION ACTIVA
========================== */
$sqlContratacion = "SELECT id_contratacion
                    FROM contrataciones
                    WHERE id_publicacion = ?
                    AND estado != 'Finalizado'
                    LIMIT 1";

$stmtContr = $conexion->prepare($sqlContratacion);
$stmtContr->bind_param("i", $id_publicacion);
$stmtContr->execute();
$resContr = $stmtContr->get_result();

if($resContr->num_rows == 0){
    die("No hay contratación activa");
}

$contr = $resContr->fetch_assoc();
$id_contratacion = $contr['id_contratacion'];

/* ==========================
FINALIZAR CONTRATACION
========================== */
$sql1 = "UPDATE contrataciones
         SET estado='Finalizado'
         WHERE id_contratacion=?";

$stmt1 = $conexion->prepare($sql1);
$stmt1->bind_param("i", $id_contratacion);
$stmt1->execute();

/* ==========================
FINALIZAR PUBLICACION
========================== */
$sql2 = "UPDATE publicaciones
         SET estado='Finalizado'
         WHERE id_publicacion=?";

$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("i", $id_publicacion);
$stmt2->execute();

/* ==========================
REDIRIGIR A CALIFICACION
========================== */
header("Location: ../rating/calificar.php?id_contratacion=".$id_contratacion);
exit();
?>