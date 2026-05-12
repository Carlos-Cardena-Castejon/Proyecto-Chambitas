<?php
// Aseguramos de tener el ID del usuario
$id_nav = $_SESSION['id_usuario'] ?? 0;

if ($id_nav > 0) {
    // 1. Consultar Notificaciones no leídas
    $resNotifNav = $conexion->query("SELECT COUNT(*) as total FROM notificaciones WHERE id_usuario = $id_nav AND leida = 0");
    $numNotifNav = $resNotifNav ? $resNotifNav->fetch_assoc()['total'] : 0;

    // 2. Consultar Mensajes no leídos
    $resMsjNav = $conexion->query("SELECT COUNT(*) as total FROM mensajes WHERE id_usuario_recibe = $id_nav AND leido = 0");
    $numMsjNav = $resMsjNav ? $resMsjNav->fetch_assoc()['total'] : 0;
} else {
    $numNotifNav = 0;
    $numMsjNav = 0;
}

// LÓGICA DE RUTAS: Detectamos en qué carpeta estamos para ajustar la ruta base
$ruta_base = (basename(dirname($_SERVER['PHP_SELF'])) == 'chambitas' || basename(dirname($_SERVER['PHP_SELF'])) == 'htdocs') ? '' : '../';
?>

<link rel="stylesheet" href="<?php echo $ruta_base; ?>css/navbar.css">

<header class="navbar">  
  <div class="nav-container">  
    <div class="logo">  
      <a href="<?php echo $ruta_base; ?>feed.php"><img src="<?php echo $ruta_base; ?>img/logo.png" alt="Chambitas"></a>  
    </div>  
  
    <div class="hamburger" onclick="toggleMenu()">  
      <i class="fas fa-bars"></i>  
    </div>  
  
    <nav class="menu">  
      <a class="publicar" href="<?php echo $ruta_base; ?>publicaciones/publicar.php">  
        <i class="fas fa-plus-circle"></i> Publicar chambita  
      </a>  

            <a href="/chambitas/feed.php">
        <i class="fas fa-tasks"></i> Mis trabajos
      </a>
  
      <a class="disponibles" href="<?php echo $ruta_base; ?>publicaciones/trabajos_disponibles.php">  
        <i class="fas fa-briefcase"></i> Disponibles  
      </a>  
  
      <a href="<?php echo $ruta_base; ?>mensajes/conversaciones.php">  
        <i class="fas fa-comment-alt"></i> Mensajes  
        <?php if($numMsjNav > 0): ?>  
          <span class="badge"><?php echo $numMsjNav; ?></span>  
        <?php endif; ?>  
      </a>  

      <a href="<?php echo $ruta_base; ?>publicaciones/notificaciones.php" class="notif-link">
          <i class="fas fa-bell"></i> Notificaciones
          <?php if($numNotifNav > 0): ?>
            <span class="badge-red"><?php echo $numNotifNav; ?></span>
          <?php endif; ?>
      </a>
  
      <a href="<?php echo $ruta_base; ?>perfil.php">  
        <i class="fas fa-user-circle"></i> Mi Perfil  
      </a>  
  
      <a href="<?php echo $ruta_base; ?>logout.php" class="logout-link">  
        <i class="fas fa-sign-out-alt"></i> Salir  
      </a>  
    </nav>  
  </div> 
</header>

<script>  
function toggleMenu() {  
  const menu = document.querySelector('.navbar .menu');  
  menu.classList.toggle('active');  
}  
</script>
