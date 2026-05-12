<?php
session_start();
include("../conexion.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_usuario'])) {
    $id_reporta = $_SESSION['id_usuario'];
    $id_reportado = (int)$_POST['id_reportado'];
    $motivo = $conexion->real_escape_string($_POST['motivo']);
    $detalles = $conexion->real_escape_string($_POST['detalles']);
    
    // Manejo de la Imagen de Evidencia
    $ruta_evidencia = null;
    if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === 0) {
        $ext = pathinfo($_FILES['evidencia']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "evidencia_" . time() . "_" . $id_reporta . "." . $ext;
        $ruta_db = "uploads/reportes/" . $nombre_archivo;
        
        // Crear carpeta si no existe
        if (!is_dir("../uploads/reportes")) { mkdir("../uploads/reportes", 0777, true); }
        
        if (move_uploaded_file($_FILES['evidencia']['tmp_name'], "../" . $ruta_db)) {
            $ruta_evidencia = $ruta_db;
        }
    }

    // Insertar en la BD (Asegúrate de haber corrido el ALTER TABLE que te di antes)
    $sql = "INSERT INTO reportes_usuarios (id_usuario_reporta, id_usuario_reportado, motivo, detalles, evidencia_ruta) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iisss", $id_reporta, $id_reportado, $motivo, $detalles, $ruta_evidencia);

    if ($stmt->execute()) {
        header("Location: ../publicaciones/perfil_publico.php?id=$id_reportado&reportado=1");
    } else {
        echo "Error al procesar: " . $conexion->error;
    }
}
?>