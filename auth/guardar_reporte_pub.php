<?php
session_start();
include("../conexion.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
    
    if (!$conexion) {
        die("Error: No se pudo conectar a la base de datos.");
    }

    $id_reporta = $_SESSION['id_usuario'];
    $id_publicacion = (int)$_POST['id_publicacion'];
    $id_usuario_dueno = (int)$_POST['id_usuario_dueno'];
    $motivo = $conexion->real_escape_string($_POST['motivo']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);

    $sql = "INSERT INTO reportes_publicaciones (id_publicacion, id_usuario_reporta, id_usuario_dueno, motivo, descripcion) 
            VALUES ($id_publicacion, $id_reporta, $id_usuario_dueno, '$motivo', '$descripcion')";

    if ($conexion->query($sql)) {
        // Redireccionamos de vuelta a la página del trabajo enviando el aviso por URL
        header("Location: ../publicaciones/trabajo.php?id=" . $id_publicacion . "&reportado=1");
        exit();
    } else {
        echo "Error en SQL: " . $conexion->error;
    }
} else {
    echo "No autorizado o sesión expirada";
}
?>
