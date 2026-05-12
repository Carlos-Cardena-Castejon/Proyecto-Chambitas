<?php
session_start();
// Subimos dos niveles para llegar a la raíz (chambitas/conexion.php)
include("../../conexion.php");

if (isset($_GET['id']) && isset($_SESSION['id_usuario'])) {
    $id_propuesta = (int)$_GET['id'];
    $id_usuario = $_SESSION['id_usuario'];
    
    // Recibimos el ID de la publicación para saber a qué página regresar
    $id_publicacion = isset($_GET['id_publicacion']) ? (int)$_GET['id_publicacion'] : 0;

    // Solo eliminamos si la propuesta pertenece al usuario logueado (Seguridad)
    $sql = "DELETE FROM propuestas WHERE id_propuesta = $id_propuesta AND id_usuario = $id_usuario";

    if ($conexion->query($sql)) {
        // Redirección con JavaScript para que el SweetAlert de éxito funcione en la otra página
        // Subimos UN nivel (../) porque estamos en /propuestas/ y el archivo está en /publicaciones/
        echo "<script>
            window.location.href = '../trabajo.php?id=$id_publicacion&msj=eliminado';
        </script>";
        exit();
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    // Si algo falla, lo mandamos al feed principal (subiendo dos niveles)
    header("Location: ../../feed.php");
    exit();
}
?>