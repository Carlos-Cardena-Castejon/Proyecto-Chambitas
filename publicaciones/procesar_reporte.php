<?php
session_start();
// Cambiamos a ../ porque solo subimos una carpeta desde 'publicaciones'
include("../conexion.php"); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
    
    // Verificamos que la conexión exista antes de usarla
    if (!$conexion) {
        die("Error: No se pudo conectar a la base de datos.");
    }

    $id_reporta = $_SESSION['id_usuario'];
    $id_reportado = (int)$_POST['id_reportado'];
    $motivo = $conexion->real_escape_string($_POST['motivo']);
    $detalles = $conexion->real_escape_string($_POST['detalles']);

    $sql = "INSERT INTO reportes_usuarios (id_usuario_reporta, id_usuario_reportado, motivo, detalles) 
            VALUES ($id_reporta, $id_reportado, '$motivo', '$detalles')";

    if ($conexion->query($sql)) {
        echo "success";
    } else {
        echo "Error en SQL: " . $conexion->error;
    }
} else {
    echo "No autorizado o sesión expirada";
}
?>