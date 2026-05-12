<?php
session_start();
include("../conexion.php");

if(!isset($_GET['id_publicacion'])){
    die("Publicación no encontrada");
}

$id_publicacion = (int) $_GET['id_publicacion'];

// Agregamos una subconsulta para traer el promedio de calificación rápido
$sql = "SELECT p.*, u.nombre, u.apellido_paterno, u.foto_perfil,
        (SELECT AVG(puntuacion) FROM calificaciones WHERE id_calificado = u.id_usuario) as rating
        FROM propuestas p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE p.id_publicacion = $id_publicacion
        ORDER BY p.fecha DESC";

$resultado = $conexion->query($sql);

$sqlCheck = "SELECT * FROM propuestas WHERE id_publicacion = $id_publicacion AND estado='Aceptada'";
$resCheck = $conexion->query($sqlCheck);
$trabajo_asignado = $resCheck->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuestas Recibidas</title>
        <link rel="icon" type="image/png" href="../img/favicon.png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/propuestas.css">
    <style>
        /* Estilo suave para el link del perfil */
        .user-link { text-decoration: none; color: inherit; display: flex; align-items: center; gap: 12px; transition: 0.2s; }
        .user-link:hover { opacity: 0.8; }
        .rating-mini { color: #f1c40f; font-size: 0.85rem; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <header class="header-section">
        <a class="boton-volver" href="../feed.php">
            <i class="fas fa-chevron-left arrow"></i> <span>Volver al feed</span>
        </a>
        <div class="main-title">
            <div class="barrita"></div>
            <h2>Propuestas Recibidas</h2>
        </div>
        <p class="subtitle">Haz clic en el nombre del candidato para ver su reputación</p>
    </header>

    <div class="grid-propuestas">
    <?php while($row = $resultado->fetch_assoc()): 
        $foto_path = !empty($row['foto_perfil']) ? '../'.$row['foto_perfil'] : '../img/usuario.png';
        $rating = $row['rating'] ? round($row['rating'], 1) : "S/C";
    ?>
        <div class="card-propuesta <?php echo ($row['estado'] == 'Aceptada') ? 'is-accepted' : ''; ?>">
            <div class="card-header">
                <a href="perfil_publico.php?id=<?php echo $row['id_usuario']; ?>" class="user-link" title="Ver perfil de <?php echo $row['nombre']; ?>">
                    
                    <div class="user-info">
    <a href="perfil_publico.php?id=<?php echo $row['id_usuario']; ?>" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
        
        <img src="<?php echo $foto_path; ?>" class="avatar-img" alt="Foto" title="Click para ver perfil">
        
        <div class="user-data">
            <h3 style="color: #2c3e50; margin: 0;"><?php echo htmlspecialchars($row['nombre'] . " " . $row['apellido_paterno']); ?></h3>
            <span style="font-size: 0.75rem; color: #f1c40f; font-weight: bold;">
                <i class="fas fa-star"></i> Ver reputación
            </span>
            <br>
            <span class="fecha-post"><i class="far fa-calendar-alt"></i> <?php echo date("d/m/Y", strtotime($row['fecha'])); ?></span>
        </div>
        
    </a>
</div>
                </a>
                
                <div class="badge-container">
                    <?php if($row['estado'] == "Aceptada"): ?>
                        <span class="badge status-aceptada"><i class="fas fa-check-circle"></i> Aceptada</span>
                    <?php else: ?>
                        <span class="badge status-pendiente"><i class="fas fa-spinner fa-spin-hover"></i> <?php echo $row['estado']; ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body">
                <div class="info-tags">
                    <div class="tag price">
                        <i class="fas fa-money-bill-wave"></i>
                        <div class="tag-content">
                            <label>Presupuesto</label>
                            <span>$<?php echo number_format($row['precio_oferta'], 2); ?></span>
                        </div>
                    </div>
                    <div class="tag time">
                        <i class="fas fa-clock"></i>
                        <div class="tag-content">
                            <label>Tiempo estimado</label>
                            <span><?php echo htmlspecialchars($row['tiempo_estimado']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="mensaje-container">
                    <i class="fas fa-quote-left quote-icon"></i>
                    <p class="mensaje-texto">
                        <?php echo htmlspecialchars($row['mensaje']); ?>
                    </p>
                </div>
            </div>

            <div class="card-footer">
                <div class="propuesta-botones">
                    <?php if(!$trabajo_asignado && $row['estado'] == "Pendiente"): ?>
                        <a href="aceptar_propuesta.php?id_propuesta=<?php echo $row['id_propuesta']; ?>&id_publicacion=<?php echo $row['id_publicacion']; ?>" 
                        class="btn btn-accept">
                            <i class="fas fa-handshake"></i> Aceptar Propuesta
                        </a>
                    <?php endif; ?>

                    <a href="../mensajes/chat.php?id_usuario=<?php echo $row['id_usuario']; ?>&id_publicacion=<?php echo $row['id_publicacion']; ?>" 
                    class="btn btn-chat">
                        <i class="fas fa-comment-dots"></i> Enviar Mensaje
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
</div>

</body>
</html>