<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Monterrey'); // Que cuadre perfecto con el login
session_start();
include("../conexion.php");

// 1. RECIBIR DATOS (Ahora sí capturamos el id_u que nos manda el botón)
$id_reporte   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_infractor = isset($_GET['id_u']) ? (int)$_GET['id_u'] : 0; // ¡ESTE ERA EL ESLABÓN PERDIDO!
$id_p         = isset($_GET['id_p']) ? (int)$_GET['id_p'] : 0;
$accion       = $_GET['accion'] ?? '';
$razon        = $conexion->real_escape_string($_GET['razon'] ?? 'Incumplimiento de normas');
$borrar_p     = isset($_GET['borrar_p']) ? $_GET['borrar_p'] : '0';

if ($id_reporte > 0 && $id_infractor > 0) {
    
    // A. Obtener el nombre del infractor directamente de la tabla usuarios
    $nombre_infractor = "el usuario";
    $resInf = $conexion->query("SELECT nombre, apellido_paterno FROM usuarios WHERE id_usuario = $id_infractor");
    if($resInf && $rowInf = $resInf->fetch_assoc()) {
        $nombre_infractor = $rowInf['nombre'] . " " . $rowInf['apellido_paterno'];
    }

    // B. Buscar al reportador para darle las gracias (Revisamos ambas tablas de reportes)
    $id_denunciante = 0;
    if ($id_p > 0) {
        $resDen = $conexion->query("SELECT id_usuario_reporta FROM reportes_publicaciones WHERE id_reporte = $id_reporte");
        if($resDen && $rowDen = $resDen->fetch_assoc()) $id_denunciante = $rowDen['id_usuario_reporta'];
    } else {
        $resDen = $conexion->query("SELECT id_usuario_reporta FROM reportes_usuarios WHERE id_reporte = $id_reporte");
        if($resDen && $rowDen = $resDen->fetch_assoc()) $id_denunciante = $rowDen['id_usuario_reporta'];
    }

    // C. NOTIFICACIÓN AL INFRACTOR (Solo si le borraron el post y no lo banearon)
    if ($borrar_p == '1' && $accion !== 'ban') {
        $msj_infractor = "Estimado usuario, su publicación fue eliminada por incumplir nuestras normas comunitarias: $razon.";
        $conexion->query("INSERT INTO notificaciones (id_usuario, mensaje, tipo, leida) 
                          VALUES ($id_infractor, '$msj_infractor', 'sancion', 0)");
    }

    // D. EL GOLPE DE JUSTICIA: EJECUTAMOS LAS SANCIONES
    if ($accion === 'ban') {
        $conexion->query("UPDATE usuarios SET estado = 'Baneado', razon_bloqueo = '$razon' WHERE id_usuario = $id_infractor");
    } elseif ($accion === 'suspender') {
        // Calculamos 7 días exactos desde este momento
        $fin = date('Y-m-d H:i:s', strtotime("+7 days"));
        $conexion->query("UPDATE usuarios SET estado = 'Suspendido', fin_suspension = '$fin', razon_bloqueo = '$razon' WHERE id_usuario = $id_infractor");
    }

    // E. BORRADO FÍSICO Y EN CASCADA DE LA PUBLICACIÓN
    if ($borrar_p == '1' && $id_p > 0) {
        $resFotos = $conexion->query("SELECT ruta_imagen FROM imagenestrabajo WHERE id_publicacion = $id_p");
        if($resFotos) {
            while($foto = $resFotos->fetch_assoc()){
                $ruta = "../" . $foto['ruta_imagen'];
                if(file_exists($ruta) && !is_dir($ruta)) unlink($ruta);
            }
        }
        $conexion->query("DELETE FROM imagenestrabajo WHERE id_publicacion = $id_p");
        $conexion->query("DELETE FROM calificaciones WHERE id_contratacion IN (SELECT id_contratacion FROM contrataciones WHERE id_publicacion = $id_p)");
        $conexion->query("DELETE FROM mensajes WHERE id_publicacion = $id_p");
        $conexion->query("DELETE FROM contrataciones WHERE id_publicacion = $id_p");
        $conexion->query("DELETE FROM propuestas WHERE id_publicacion = $id_p");
        $conexion->query("DELETE FROM ubicaciones WHERE id_publicacion = $id_p");
        $conexion->query("DELETE FROM notificaciones WHERE link LIKE '%id=$id_p%'");
        $conexion->query("DELETE FROM publicaciones WHERE id_publicacion = $id_p");
    }

    // F. NOTIFICACIÓN DE AGRADECIMIENTO AL REPORTADOR
    if ($id_denunciante > 0 && ($borrar_p == '1' || $accion != 'advertencia')) {
        $msj_gracias = "Hemos tomado medidas respecto a tu reporte sobre $nombre_infractor. ¡Muchas gracias por ayudar a Chambitas a ser un lugar más seguro!";
        $conexion->query("INSERT INTO notificaciones (id_usuario, mensaje, tipo, leida) 
                          VALUES ($id_denunciante, '$msj_gracias', 'info', 0)");
    }

    // G. ACTUALIZAR ESTADO DEL REPORTE
    $conexion->query("UPDATE reportes_publicaciones SET estado = 'Revisado' WHERE id_reporte = $id_reporte");
    $conexion->query("UPDATE reportes_usuarios SET estado = 'Revisado' WHERE id_reporte = $id_reporte");

    echo "ok";
} else {
    echo "Error: Datos incompletos.";
}
?>
