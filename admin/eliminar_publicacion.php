<?php
session_start();
include("../conexion.php");

// 1. Verificación de seguridad
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

// 2. Validar que recibimos un ID válido
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_publicacion = intval($_GET['id']);

    // 3. EJECUCIÓN DEL BORRADO LÓGICO
    // Cambiamos el estado para que no aparezca en los SELECT que usan "WHERE estado != 'Eliminada'"
    $sql = "UPDATE publicaciones SET estado = 'Eliminada' WHERE id_publicacion = $id_publicacion";

    if($conexion->query($sql)) {
        header("Location: publicaciones_admin.php?status=deleted");
        exit(); 
    } else {
        mostrarError("Error del Sistema", "No se pudo actualizar el estado: " . $conexion->error);
    }
} else {
    // Si no hay ID o no es válido, regresamos sin hacer nada
    header("Location: publicaciones_admin.php");
    exit();
}

// 4. FUNCIÓN PARA MOSTRAR ERRORES ESTILIZADOS
function mostrarError($titulo, $mensaje) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error | Chambitas Admin</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { background: #fff; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .error-card { 
                background: #fff; padding: 40px; border-radius: 30px; text-align: center; 
                max-width: 400px; border: 2px solid #121212; box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
                animation: shake 0.4s ease-in-out;
            }
            .icon { font-size: 3.5rem; color: #FFD700; margin-bottom: 20px; }
            h1 { font-weight: 900; letter-spacing: -1.5px; text-transform: uppercase; margin: 0; color: #121212; }
            p { color: #666; margin: 20px 0 30px; line-height: 1.6; }
            .btn { 
                background: #121212; color: #FFD700; text-decoration: none; padding: 15px 35px; 
                border-radius: 14px; font-weight: 800; text-transform: uppercase; display: inline-block; transition: 0.3s; 
            }
            .btn:hover { background: #FFD700; color: #121212; transform: translateY(-3px); }
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
        </style>
    </head>
    <body>
        <div class="error-card">
            <div class="icon"><i class="fas fa-file-circle-exclamation"></i></div>
            <h1><?php echo $titulo; ?></h1>
            <p><?php echo $mensaje; ?></p>
            <a href="publicaciones_admin.php" class="btn">Volver a la lista</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>