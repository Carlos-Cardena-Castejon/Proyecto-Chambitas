<?php
session_start();

// 1. SEGURIDAD: Verificar sesión y rol de administrador
if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit();
}

include("conexion.php"); // Conexión en la raíz

$nombre_admin = $_SESSION['nombre'];

/* =====================
   CONSULTAS DE ESTADÍSTICAS
   ===================== */
$usuarios_total = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$publicaciones_total = $conexion->query("SELECT COUNT(*) as total FROM publicaciones")->fetch_assoc()['total'];
$servicios_total = $conexion->query("SELECT COUNT(*) as total FROM servicios")->fetch_assoc()['total'];

// Nueva consulta para Reportes Pendientes
$sql_reportes = "SELECT COUNT(*) as total FROM reportes_usuarios WHERE estado = 'Pendiente'";
$res_reportes = $conexion->query($sql_reportes);
$reportes_pendientes = ($res_reportes) ? $res_reportes->fetch_assoc()['total'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio | Chambitas Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #FFD700;
            --dark: #121212;
            --danger: #dc3545;
            --light-bg: #FFFFFF;
            --sidebar-width: 260px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-bg);
            margin: 0;
            display: flex;
        }

        /* --- SIDEBAR --- */
        .sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            background: var(--dark); 
            color: white; 
            position: fixed; 
            padding: 30px 20px; 
            box-sizing: border-box; 
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 { 
            color: var(--primary); 
            text-align: center; 
            font-weight: 900; 
            margin-bottom: 40px; 
            text-transform: uppercase;
            letter-spacing: -1.5px;
        }

        .user-box { border: 1px solid #333; padding: 15px; border-radius: 12px; margin-bottom: 30px; }
        .user-box span { font-size: 0.7rem; color: #888; text-transform: uppercase; }
        .user-box p { margin: 5px 0 0; color: var(--primary); font-weight: 600; }
        
        .nav-menu a { 
            display: flex; 
            align-items: center; 
            color: white; 
            text-decoration: none; 
            padding: 12px; 
            border-radius: 8px; 
            margin-bottom: 10px; 
            transition: 0.3s; 
            position: relative;
        }
        .nav-menu a i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-menu a:hover { background: var(--primary); color: black; font-weight: bold; }
        
        /* Notificación roja en el menú */
        .badge-notify {
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            position: absolute;
            right: 10px;
            font-weight: 800;
        }

        .nav-menu a.logout { color: #ff6666; margin-top: 20px; }

        /* --- MAIN CONTENT --- */
        .content {
            margin-left: var(--sidebar-width);
            padding: 50px;
            width: calc(100% - var(--sidebar-width));
        }

        .welcome-header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            border-left: 6px solid var(--primary);
            padding-left: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 24px;
            border: 1px solid #eee;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card:hover {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            transform: translateY(-5px);
        }

        /* Estilo especial para cuando hay reportes */
        .stat-card.alert { border-color: #ffbaba; background: #fff8f8; }
        .stat-card.alert:hover { border-color: var(--danger); }

        .stat-info h3 { font-size: 0.9rem; color: #666; margin: 0; }
        .stat-info .number { font-size: 2.2rem; font-weight: 800; margin-top: 5px; }
        .stat-icon { 
            font-size: 1.3rem; 
            color: var(--dark); 
            background: var(--primary); 
            width: 45px; height: 45px; 
            display: flex; align-items: center; justify-content: center; 
            border-radius: 12px; 
        }
        .stat-icon.danger { background: var(--danger); color: white; }

        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 20px 10px; }
            .sidebar h2, .user-box, .nav-menu a span, .badge-notify { display: none; }
            .content { margin-left: 70px; padding: 20px; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <h2>CHAMBITAS</h2>
    <div class="user-box">
        <span>Administrador</span>
        <p><?php echo htmlspecialchars($nombre_admin); ?></p>
    </div>
    
    <nav class="nav-menu">
        <a href="admin_home.php"><i class="fas fa-house"></i> <span>Inicio</span></a>
        <a href="admin/dashboard.php"><i class="fas fa-chart-line"></i> <span>Dashboard</span></a>
        <a href="admin/usuarios_admin.php"><i class="fas fa-user-gear"></i> <span>Usuarios</span></a>
        
        <a href="admin/gestion_reportes.php">
            <i class="fas fa-shield-alt"></i> <span>Reportes</span>
            <?php if($reportes_pendientes > 0): ?>
                <span class="badge-notify"><?php echo $reportes_pendientes; ?></span>
            <?php endif; ?>
        </a>

        <a href="admin/publicaciones_admin.php"><i class="fas fa-file-pen"></i> <span>Publicaciones</span></a>
        <a href="admin/servicios.php"><i class="fa-solid fa-briefcase"></i> <span>Servicios</span></a>
        <a href="logout.php" class="logout"><i class="fas fa-power-off"></i> <span>Salir</span></a>
    </nav>
</aside>

<main class="content">
    <div class="welcome-header">
        <h1>Bienvenido al Panel de <span>Control</span></h1>
        <p style="color: #666; margin-top: 10px;">Monitor de actividad y seguridad de la plataforma.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Usuarios</h3>
                <div class="number"><?php echo $usuarios_total; ?></div>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>Publicaciones</h3>
                <div class="number"><?php echo $publicaciones_total; ?></div>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-paper-plane"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>Servicios</h3>
                <div class="number"><?php echo $servicios_total; ?></div>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-bolt"></i></div>
        </div>

        <div class="stat-card <?php echo ($reportes_pendientes > 0) ? 'alert' : ''; ?>">
            <div class="stat-info">
                <h3>Reportes Pendientes</h3>
                <div class="number" style="<?php echo ($reportes_pendientes > 0) ? 'color: var(--danger);' : ''; ?>">
                    <?php echo $reportes_pendientes; ?>
                </div>
            </div>
            <div class="stat-icon <?php echo ($reportes_pendientes > 0) ? 'danger' : ''; ?>">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>
</main>

</body>
</html>