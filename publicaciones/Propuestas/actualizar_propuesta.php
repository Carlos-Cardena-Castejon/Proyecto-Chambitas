<?php
session_start();
// Subimos dos niveles: uno para salir de 'propuestas' y otro para salir de 'publicaciones'
include("../../conexion.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validamos que existan los datos necesarios
    $id_propuesta = isset($_POST['id_propuesta']) ? (int)$_POST['id_propuesta'] : 0;
    $id_publicacion = isset($_POST['id_publicacion']) ? (int)$_POST['id_publicacion'] : 0;
    $precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
    $tiempo = isset($_POST['tiempo_estimado']) ? (int)$_POST['tiempo_estimado'] : 0;
    $mensaje = isset($_POST['mensaje']) ? $conexion->real_escape_string($_POST['mensaje']) : '';
    
    $id_usuario = $_SESSION['id_usuario'] ?? 0;

    if ($id_propuesta > 0 && $id_usuario > 0) {
        // Actualizamos asegurándonos que la propuesta pertenezca al usuario logueado
        $sql = "UPDATE propuestas 
                SET precio_oferta = '$precio', 
                    tiempo_estimado = '$tiempo', 
                    mensaje = '$mensaje' 
                WHERE id_propuesta = $id_propuesta AND id_usuario = $id_usuario";

        if ($conexion->query($sql)) {
            // Regresamos un nivel (a la carpeta publicaciones) al archivo del detalle
            header("Location: ../trabajo.php?id=$id_publicacion&msj=actualizado");
            exit();
        } else {
            echo "Error en la base de datos: " . $conexion->error;
        }
    } else {
        echo "Sesión expirada o datos inválidos.";
    }
} else {
    header("Location: ../../feed.php");
    exit();
}
?>
