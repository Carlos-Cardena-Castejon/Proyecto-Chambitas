<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    exit("Acceso denegado");
}

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    // USAMOS DELETE para que desaparezca de verdad
    $sql = "DELETE FROM servicios WHERE id_servicio = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        header("Location: servicios.php?success=deleted");
    } else {
        // Si hay error (como llaves foráneas), avisamos al admin
        echo "<script>
                alert('No se puede borrar: Este servicio está asignado a publicaciones activas. Primero elimina las publicaciones asociadas.');
                window.location.href='servicios.php';
              </script>";
    }
}
?>