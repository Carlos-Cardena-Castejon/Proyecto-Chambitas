<?php
session_start();
include("../conexion.php"); 

if(!isset($_GET['id'])){
    header("Location: ../feed.php");
    exit();
}

$id_consultado = (int)$_GET['id'];
$id_sesion = $_SESSION['id_usuario'] ?? 0;

/* =====================
   DATOS DEL USUARIO
   ===================== */
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_consultado);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if(!$usuario){
    die("Usuario no encontrado.");
}

/* =====================
   REPUTACIÓN Y ESTADÍSTICAS
   ===================== */
$sql_rating = "SELECT AVG(puntuacion) as promedio, COUNT(*) as total FROM calificaciones WHERE id_calificado = ?";
$stmt_rating = $conexion->prepare($sql_rating);
$stmt_rating->bind_param("i", $id_consultado);
$stmt_rating->execute();
$rating = $stmt_rating->get_result()->fetch_assoc();
$promedio = $rating['promedio'] ? round($rating['promedio'], 1) : 0;
$total_reviews = $rating['total'];

$sql_trabajos = "SELECT COUNT(*) as total FROM contrataciones WHERE id_usuario = ? AND estado = 'Finalizado'";
$stmt_trab = $conexion->prepare($sql_trabajos);
$stmt_trab->bind_param("i", $id_consultado);
$stmt_trab->execute();
$total_trabajos = $stmt_trab->get_result()->fetch_assoc()['total'];

$sql_pub = "SELECT * FROM publicaciones WHERE id_usuario = ? AND estado = 'Activa' ORDER BY fecha_publicacion DESC";
$stmt_pub = $conexion->prepare($sql_pub);
$stmt_pub->bind_param("i", $id_consultado);
$stmt_pub->execute();
$publicaciones = $stmt_pub->get_result();

$foto_perfil = !empty($usuario['foto_perfil']) ? "../" . $usuario['foto_perfil'] : "../img/usuario.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($usuario['nombre']); ?> | Chambitas</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../css/perfil_publico.css">
    <link rel="stylesheet" href="../css/modal_reporte.css">
    

</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container-main">
    <header class="profile-header">
        <div class="profile-cover"></div>
        <div class="profile-info-main">
            <div class="avatar-container">
                <img src="<?php echo $foto_perfil; ?>" alt="Foto de perfil">
            </div>
            <div class="user-details">
                <span class="badge-role">TRABAJADOR VERIFICADO</span>
                <h1><?php echo htmlspecialchars($usuario['nombre']." ".$usuario['apellido_paterno']); ?></h1>
                
                <div class="rating-box">
                    <div class="stars">
                        <?php
                        $estrellas = floor($promedio);
                        for($i=1;$i<=5;$i++){
                            if($i <= $estrellas) echo '<i class="fas fa-star"></i>';
                            elseif($i == $estrellas + 1 && ($promedio - $estrellas) >= 0.5) echo '<i class="fas fa-star-half-alt"></i>';
                            else echo '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                    <span class="rating-text"><?php echo $promedio ?> / 5.0 (<?php echo $total_reviews ?> reseñas)</span>
                </div>

                <div class="quick-stats">
                    <span><i class="fas fa-map-marker-alt"></i> México</span>
                    <span><i class="fas fa-calendar-check"></i> <?php echo $total_trabajos; ?> Éxitos</span>
                    <span><i class="fas fa-user-clock"></i> Desde <?php echo date("Y", strtotime($usuario['fecha_registro'])); ?></span>
                </div>
            </div>

            <?php if($id_sesion != $id_consultado): ?>
            <div class="actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="../mensajes/chat.php?receptor=<?php echo $id_consultado; ?>" class="btn-primary">
                    <i class="fas fa-envelope"></i> Enviar Mensaje
                </a>
                <button type="button" onclick="abrirModalReporte()" class="btn-report">
                    <i class="fas fa-flag"></i> Reportar
                </button>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="layout-grid">
        <section class="column-left">
            <div class="card">
                <h3><i class="fas fa-info-circle"></i> Acerca de mí</h3>
                <?php if(!empty($usuario['descripcion'])): ?>
                    <div class="descripcion-container">
                        <p id="texto-desc" class="descripcion-texto colapsado">
                            <?php echo nl2br(htmlspecialchars($usuario['descripcion'])); ?>
                        </p>
                        <button id="btn-desc" class="btn-mostrar-mas">
                            Leer más <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <p class="descripcion-texto">Este usuario aún no ha agregado una descripción a su perfil.</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3><i class="fas fa-star"></i> Reseñas de Clientes</h3>
                <div class="review-preview">
                    <p>La confianza es lo primero. Mira lo que otros dicen de <?php echo htmlspecialchars($usuario['nombre']); ?>.</p>
                    <a href="../ver_reviews.php?id_usuario=<?php echo $id_consultado; ?>" class="btn-outline">
                        Ver todas las reseñas
                    </a>
                </div>
            </div>
        </section>

        <aside class="column-right">
            <div class="card">
                <h3><i class="fas fa-briefcase"></i> Trabajos Disponibles</h3>
                <?php if($publicaciones->num_rows > 0): ?>
                    <ul class="job-list">
                        <?php while($pub = $publicaciones->fetch_assoc()): ?>
                        <li>
                            <a href="../publicaciones/trabajo.php?id=<?php echo $pub['id_publicacion']; ?>">
                                <span class="job-title"><?php echo htmlspecialchars($pub['titulo']); ?></span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="empty-msg">No hay publicaciones activas por ahora.</p>
                <?php endif; ?>
            </div>
            
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver atrás
            </a>
        </aside>
    </div>
</div>

<div id="modalReporte" class="modal-overlay">
    <div class="modal-content" style="border-top: 5px solid #ff4b4b;">
        <div class="modal-header">
            <h3><i class="fas fa-user-shield"></i> Reportar Usuario</h3>
            <button onclick="cerrarModalReporte()" class="btn-close-modal">&times;</button>
        </div>
        
        <form id="formReporte" class="modal-form" action="../auth/procesar_reporte_usuario.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_reportado" value="<?php echo $id_consultado; ?>">
            
            <label>Motivo del reporte:</label>
            <select name="motivo" required style="width: 100%; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                <option value="">Selecciona un motivo...</option>
                <option value="Acoso / Mensajes Ofensivos">Acoso / Mensajes Ofensivos</option>
                <option value="Spam / Fraude">Spam / Fraude</option>
                <option value="Venta de Productos">Venta de Productos (Solo servicios)</option>
                <option value="Perfil Falso">Perfil Falso</option>
                <option value="Otro">Otro...</option>
            </select>

            <label>Detalles de lo sucedido (Máx. 255):</label>
            <textarea name="detalles" id="detallesReporte" rows="4" maxlength="255" 
                      oninput="contarU(this, 'c-user')" 
                      placeholder="Describe brevemente el problema..." required></textarea>
            <span id="c-user" class="char-count" style="display:block; text-align:right; font-size:0.75rem; color:#888; margin-bottom:10px;">0 / 255</span>

            <label><i class="fas fa-camera"></i> Adjuntar Evidencia (Captura/Imagen):</label>
            <input type="file" name="evidencia" accept="image/*" style="font-size: 0.8rem; margin-bottom: 15px;">
            <p style="font-size: 0.65rem; color: #666; margin-top: -10px;">* Sube una captura del chat o comportamiento indebido.</p>

            <div class="modal-actions">
                <button type="button" onclick="cerrarModalReporte()" class="btn-cancel">Cancelar</button>
                <button type="submit" class="btn-send" style="background: #ff4b4b;">Enviar Reporte</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/reporte_usuario.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const texto = document.getElementById("texto-desc");
    const btn = document.getElementById("btn-desc");

    if(texto && btn) {
        setTimeout(() => {
            if (texto.scrollHeight <= texto.clientHeight) {
                btn.style.display = 'none';
            }
        }, 50);

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