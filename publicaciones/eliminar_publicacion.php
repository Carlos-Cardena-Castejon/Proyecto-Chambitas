<?php
session_start();
include("../conexion.php");

// 1. Verificación de seguridad básica
if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    header("Location: ../feed.php");
    exit();
}

$id_publicacion = (int)$_GET['id'];
$id_usuario_sesion = $_SESSION['id_usuario'];

/* 2. VERIFICAR PERMISOS Y ESTADO 
   Solo el dueño puede borrar y solo si el trabajo está 'Activa' 
*/
$sql_check = "SELECT id_usuario, estado FROM Publicaciones WHERE id_publicacion = $id_publicacion";
$res_check = $conexion->query($sql_check);
$datos = $res_check->fetch_assoc();

if (!$datos || $datos['id_usuario'] != $id_usuario_sesion) {
    die("No tienes permiso para eliminar esta publicación.");
}

if ($datos['estado'] != 'Activa') {
    die("No puedes eliminar un trabajo que ya está en proceso o finalizado.");
}

/* 3. OBTENER RUTAS DE IMÁGENES PARA BORRAR ARCHIVOS FÍSICOS 
*/
$sql_fotos = "SELECT ruta_imagen FROM imagenestrabajo WHERE id_publicacion = $id_publicacion";
$res_fotos = $conexion->query($sql_fotos);
$rutas_a_borrar = [];
while ($row = $res_fotos->fetch_assoc()) {
    $rutas_a_borrar[] = "../" . $row['ruta_imagen'];
}

/* 4. PROCESO DE ELIMINACIÓN (Usando Transacción) 
*/
$conexion->begin_transaction();

try {
    // A. Borrar propuestas recibidas (si las hay)
    $conexion->query("DELETE FROM propuestas WHERE id_publicacion = $id_publicacion");

    // B. Borrar registros de imágenes en la BD
    $conexion->query("DELETE FROM imagenestrabajo WHERE id_publicacion = $id_publicacion");

    // C. Borrar ubicación
    $conexion->query("DELETE FROM ubicaciones WHERE id_publicacion = $id_publicacion");

    // D. Borrar la publicación principal
    $conexion->query("DELETE FROM Publicaciones WHERE id_publicacion = $id_publicacion");

    // Si todo salió bien, confirmamos los cambios en la BD
    $conexion->commit();

    // E. BORRAR LOS ARCHIVOS FÍSICOS DEL SERVIDOR
    // Solo lo hacemos después de confirmar que se borraron de la BD
    foreach ($rutas_a_borrar as $archivo) {
        if (file_exists($archivo)) {
            unlink($archivo); // Esta función borra el archivo real
        }
    }

    header("Location: ../feed.php?msg=eliminado");
    exit();

} catch (Exception $e) {
    // Si algo falla, cancelamos todo el borrado en la BD
    $conexion->rollback();
    die("Error al eliminar: " . $e->getMessage());
}
?>