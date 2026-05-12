<?php
session_start();
include("../conexion.php");

// 1. Seguridad básica
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

if(isset($_GET['id'])) {
    $id_a_eliminar = intval($_GET['id']);
    $admin_actual = $_SESSION['id_usuario'];

    // 2. No dejar que el admin se borre a sí mismo
    if($id_a_eliminar == $admin_actual) {
        mostrarMensaje("¡Operación Cancelada!", "No puedes eliminar tu propia cuenta de administrador.", "error");
        exit();
    }

    // 3. PROCESO DE ELIMINACIÓN TOTAL (CASCADA MANUAL)
    // Iniciamos transacción: O se borra TODO o no se borra nada.
    $conexion->begin_transaction();

    try {
        // A. Borramos sus publicaciones
        $conexion->query("DELETE FROM publicaciones WHERE id_usuario = $id_a_eliminar");

        // B. Borramos sus mensajes (enviados o recibidos)
        $conexion->query("DELETE FROM mensajes WHERE id_emisor = $id_a_eliminar OR id_receptor = $id_a_eliminar");

        // C. Borramos valoraciones o reseñas (si tienes esa tabla)
        // $conexion->query("DELETE FROM resenas WHERE id_usuario = $id_a_eliminar");

        // D. FINALMENTE: Borramos al usuario de la base de datos
        $sql_usuario = "DELETE FROM usuarios WHERE id_usuario = $id_a_eliminar";
        
        if($conexion->query($sql_usuario)) {
            $conexion->commit(); // ÉXITO: Confirmamos los cambios
            header("Location: usuarios_admin.php?msg=eliminado_total");
            exit();
        } else {
            throw new Exception("Error al borrar el perfil principal.");
        }

    } catch (Exception $e) {
        $conexion->rollback(); // FALLO: Deshacemos todo para no romper la base de datos
        mostrarMensaje("Error de Integridad", "No se pudo eliminar al usuario. Detalles: " . $e->getMessage(), "error");
    }
} else {
    header("Location: usuarios_admin.php");
}

// --- FUNCIÓN PARA MOSTRAR MENSAJE ESTILO CHAMBITAS ---
function mostrarMensaje($titulo, $mensaje, $tipo) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $titulo; ?> | Chambitas</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { background: #fff; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .card { background: #f8f9fa; padding: 40px; border-radius: 25px; text-align: center; max-width: 420px; border: 2px solid #121212; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
            .icon { font-size: 3.5rem; color: #ff4d4d; margin-bottom: 20px; }
            h1 { font-weight: 900; text-transform: uppercase; margin: 0; font-size: 1.8rem; letter-spacing: -1px; }
            p { color: #666; margin: 20px 0; line-height: 1.5; }
            .btn { background: #121212; color: #FFD700; text-decoration: none; padding: 15px 30px; border-radius: 12px; font-weight: 800; display: inline-block; }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icon"><i class="fas fa-trash-can"></i></div>
            <h1><?php echo $titulo; ?></h1>
            <p><?php echo $mensaje; ?></p>
            <a href="usuarios_admin.php" class="btn">VOLVER AL PANEL</a>
        </div>
    </body>
    </html>
    <?php
}
?>