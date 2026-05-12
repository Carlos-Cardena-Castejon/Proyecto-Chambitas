<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('America/Monterrey'); 
session_start();
include("../conexion.php");

$correo = $conexion->real_escape_string($_POST['correo']);
$contrasena = $_POST['contrasena'];

$sql = "SELECT * FROM usuarios WHERE correo='$correo'";
$resultado = $conexion->query($sql);

if($resultado && $resultado->num_rows > 0){
    $usuario = $resultado->fetch_assoc();

    // Verificamos que ponga la contraseña correcta
    if(password_verify($contrasena, $usuario['contrasena'])){
        
        // --- INICIO DEL BLOQUE DE JUSTICIA ---
        $estado_usuario = strtolower(trim($usuario['estado']));
        
        // Atrapamos al infractor: Ya sea que el estado diga '0', 'suspendido' o 'baneado'
        if ($estado_usuario === '0' || $estado_usuario === 'suspendido' || $estado_usuario === 'baneado') {
            
            $fecha_actual = new DateTime();
            $fecha_fin = !empty($usuario['fin_suspension']) ? new DateTime($usuario['fin_suspension']) : null;
            $razon = !empty($usuario['razon_bloqueo']) ? $usuario['razon_bloqueo'] : "Incumplimiento de las normas de la comunidad.";

            // Si hay fecha límite y aún no llegamos a ella -> Es Suspensión Temporal
            if ($fecha_fin && $fecha_actual < $fecha_fin) {
                $intervalo = $fecha_actual->diff($fecha_fin);
                $tiempo_restante = $intervalo->format('%d días y %h horas');
                mostrarError("CUENTA SUSPENDIDA", "Estarás inhabilitado por: <b>$tiempo_restante</b>. <br><br><b>Motivo:</b> $razon");
            } 
            // Si ya pasó la fecha, le regresamos su cuenta (Lo ponemos en '1' o 'Activo')
            elseif ($fecha_fin && $fecha_actual >= $fecha_fin) {
                $conexion->query("UPDATE usuarios SET estado = '1', razon_bloqueo = NULL, fin_suspension = NULL WHERE id_usuario = " . $usuario['id_usuario']);
                $usuario['estado'] = '1'; 
            } 
            // Si no tiene fecha límite -> Es un Baneo Permanente
            else {
                mostrarError("ACCESO DENEGADO", "Tu cuenta ha sido expulsada permanentemente. <br><br><b>Motivo:</b> $razon");
            }
        }
        // --- FIN DEL BLOQUE DE JUSTICIA ---

        // Si pasó todos los filtros, le damos sus variables de sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];

        if(strtolower(trim($usuario['rol'])) == "admin"){
            header("Location: ../admin_home.php");
        } else {
            header("Location: ../feed.php");
        }
        exit();
    } else {
        mostrarError("Contraseña incorrecta", "Verifica tus credenciales e intenta de nuevo.");
    }
} else {
    mostrarError("Usuario no encontrado", "El correo ingresado no coincide con ninguna cuenta activa.");
}

function mostrarError($titulo, $subtitulo) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Acceso Denegado | Chambitas</title>
                <link rel="icon" type="image/png" href="../img/favicon.png">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
          :root {
                --primary: #FFD700;
                --dark: #121212;
                --error-red: #ff4757; 
            }

            body {
                background: #FFFFFF;
                font-family: 'Inter', sans-serif;
                margin: 0;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            body::before {
                content: "";
                position: absolute;
                width: 300px;
                height: 300px;
                background: var(--primary);
                filter: blur(150px);
                opacity: 0.1;
                z-index: -1;
            }

            .error-card {
                background: white;
                padding: 50px;
                border-radius: 32px;
                text-align: center;
                max-width: 420px;
                width: 90%;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
                border: 1px solid rgba(0,0,0,0.05);
            }

            .icon-wrapper {
                width: 80px;
                height: 80px;
                background: var(--dark); 
                color: var(--error-red); 
                border-radius: 22px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 3rem; 
                margin: 0 auto 30px;
                transform: rotate(-5deg); 
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            }

            h1 {
                font-weight: 900;
                letter-spacing: -1.5px;
                color: var(--dark);
                margin: 0;
                font-size: 1.8rem;
                text-transform: uppercase;
            }

            p {
                color: #666;
                font-size: 1rem;
                margin: 15px 0 35px;
                line-height: 1.6;
            }

            .btn-retry {
                background: var(--dark);
                color: var(--primary);
                text-decoration: none;
                padding: 18px 35px;
                border-radius: 16px;
                font-weight: 700;
                display: inline-block;
                transition: all 0.3s ease;
                border: 2px solid var(--dark);
            }

            .btn-retry:hover {
                background: transparent;
                color: var(--dark);
                transform: translateY(-3px);
            }
        </style>
    </head>
    <body>

   <div class="error-card">
        <div class="icon-wrapper">
            <i class="fas fa-times"></i> </div>
        <h1><?php echo $titulo; ?></h1>
        <p><?php echo $subtitulo; ?></p>
        <a href="../login.php" class="btn-retry">Reintentar acceso</a>
    </div>
    </body>
    </html>
    <?php
    exit();
}
?>