<div class="sidebar">
    <h2>CHAMBITAS</h2>
    <div class="user-box">
        <span>Administrador</span>
        <link rel="stylesheet" href="estilo.css">
        <p><?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Admin'); ?></p>
    </div>

    <nav class="nav-menu">
        <a href="../admin_home.php"><i class="fa-solid fa-house"></i> Inicio</a>
        
        <a href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
        
        <a href="usuarios_admin.php"><i class="fa-solid fa-user-gear"></i> Usuarios</a>
        <a href="publicaciones_admin.php"><i class="fa-solid fa-file-pen"></i> Publicaciones</a>
        <a href="servicios.php"><i class="fa-solid fa-briefcase"></i> Servicios</a>
        <a href="../logout.php" class="logout"><i class="fa-solid fa-power-off"></i> Salir</a>
    </nav>
</div>