<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

/* =====================
   DATOS DEL USUARIO
   ===================== */
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

/* =====================
   RATING / CALIFICACIONES
   ===================== */
$sql_rating = "SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM calificaciones WHERE id_calificado = ?";
$stmt_rating = $conexion->prepare($sql_rating);
$stmt_rating->bind_param("i", $id_usuario);
$stmt_rating->execute();
$rating = $stmt_rating->get_result()->fetch_assoc();
$promedio = $rating['promedio'] ? round($rating['promedio'],1) : 0;
$total_reviews = $rating['total'];

/* =====================
   TRABAJOS COMPLETADOS
   ===================== */
$sql_trabajos = "SELECT COUNT(*) as total FROM contrataciones WHERE id_usuario = ? AND estado = 'Finalizado'";
$stmt_trab = $conexion->prepare($sql_trabajos);
$stmt_trab->bind_param("i", $id_usuario);
$stmt_trab->execute();
$total_trabajos = $stmt_trab->get_result()->fetch_assoc()['total'];

/* =====================
   MIS PUBLICACIONES
   ===================== */
$sql_pub = "SELECT * FROM publicaciones WHERE id_usuario = ? ORDER BY fecha_publicacion DESC";
$stmt_pub = $conexion->prepare($sql_pub);
$stmt_pub->bind_param("i", $id_usuario);
$stmt_pub->execute();
$publicaciones = $stmt_pub->get_result();

/* =====================
   LÓGICA DE FOTO DEFAULT
   ===================== */
$fotoMostrar = "img/usuario.png"; 
// Validamos que exista el archivo físicamente antes de mostrarlo
if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])) {
    $fotoMostrar = $usuario['foto_perfil'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil | <?php echo htmlspecialchars($usuario['nombre']); ?></title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/perfil.css">
    
    <style>
        /* Estilos específicos para visualización limpia */
        .avatar-wrapper img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .info-header h1 {
            font-size: 26px;
            color: #111;
            margin-bottom: 8px;
        }

        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #555;
        }

        .stats-item strong {
            color: #000;
        }

        .descripcion-texto.colapsado {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="main-container">
    <section class="profile-card">
        <div class="avatar-wrapper">
            <img src="<?php echo $fotoMostrar; ?>" alt="Foto de perfil">
        </div>

        <div class="info-header">
            <span class="user-tag" style="background:#f1c40f; color:#000;">PERFIL VERIFICADO</span>
            <h1><?php echo htmlspecialchars($usuario['nombre']." ".$usuario['apellido_paterno']." ".$usuario['apellido_materno']); ?></h1>
            
            <div class="stats-row">
                <div class="stats-item">
                    <span style="color:#f1c40f;">
                        <?php
                        $estrellas = floor($promedio);
                        for($i=1;$i<=5;$i++){
                            if($i <= $estrellas) echo '<i class="fas fa-star"></i>';
                            elseif($i == $estrellas + 1 && ($promedio - $estrellas) >= 0.5) echo '<i class="fas fa-star-half-alt"></i>';
                            else echo '<i class="far fa-star"></i>';
                        }
                        ?>
                    </span>
                    <strong><?php echo $promedio ?></strong> Reputación
                </div>
                <div class="stats-item">
                    <strong><?php echo $total_trabajos ?></strong> Trabajos finalizados
                </div>
            </div>

            <div class="contact-grid">
                <div class="contact-item"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($usuario['correo']); ?></div>
                <div class="contact-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($usuario['telefono']); ?></div>
                <div class="contact-item"><i class="fas fa-calendar-alt"></i> Miembro desde <?php echo date("F Y", strtotime($usuario['fecha_registro'])); ?></div>
            </div>
        </div>
    </section>

    <div class="main-content">
        <div class="content-box">
            <h2><i class="fas fa-info-circle"></i> Acerca de mi</h2>
            <?php if(!empty($usuario['descripcion'])): ?>
                <div class="descripcion-container">
                    <p id="texto-desc" class="descripcion-texto colapsado">
                        <?php echo nl2br(htmlspecialchars($usuario['descripcion'])); ?>
                    </p>
                    <button id="btn-desc" class="btn-mostrar-mas" style="border:none; background:none; color:#f1c40f; font-weight:700; cursor:pointer; margin-top:8px;">
                        Leer más <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            <?php else: ?>
                <p class="empty-text">El usuario no ha proporcionado una descripción detallada todavía.</p>
            <?php endif; ?>
        </div>

        <div class="content-box">
            <h2><i class="fas fa-layer-group"></i> Servicios publicados</h2>
            <?php if($publicaciones->num_rows > 0): ?>
                <div class="servicios-grid" style="display:grid; gap:12px;">
                    <?php while($pub = $publicaciones->fetch_assoc()): ?>
                        <a href="publicaciones/trabajo.php?id=<?php echo $pub['id_publicacion']; ?>" class="btn-action" style="justify-content:space-between; display:flex; padding:15px; background:#f9f9f9; border-radius:10px; text-decoration:none; color:inherit;">
                            <span><?php echo htmlspecialchars($pub['titulo']); ?></span>
                            <span style="font-weight:700; color:#27ae60;">$<?php echo number_format($pub['presupuesto'], 2); ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="empty-text">No se encontraron publicaciones activas.</p>
            <?php endif; ?>
        </div>
    </div>

   <aside class="sidebar">
    <h3 style="margin-bottom:15px; font-size:16px;">Opciones de cuenta</h3>
    <ul class="nav-list">
        <li><a href="editar_perfil.php" class="btn-action btn-edit"><i class="fas fa-user-edit"></i> Editar Perfil</a></li>
        <li><a href="mis_trabajos.php" class="btn-action"><i class="fas fa-briefcase"></i> Mis Trabajos</a></li>
        <li><a href="ver_reviews.php?id_usuario=<?php echo $id_usuario; ?>" class="btn-action"><i class="fas fa-star"></i> Reseña de clientes</a></li>
    </ul>
</aside>
</main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const texto = document.getElementById("texto-desc");
    const btn = document.getElementById("btn-desc");

    if(texto && btn) {
        // Solo mostrar el botón si el texto es realmente largo
        if (texto.scrollHeight <= texto.clientHeight) {
            btn.style.display = 'none';
        }

        btn.addEventListener("click", function() {
            if (texto.classList.contains("colapsado")) {
                texto.classList.remove("colapsado");
                btn.innerHTML = 'Mostrar menos <i class="fas fa-chevron-up"></i>';
            } else {
                texto.classList.add("colapsado");
                btn.innerHTML = 'Leer más <i class="fas fa-chevron-down"></i>';
            }
        });
    }
});
</script>
</body>
</html>