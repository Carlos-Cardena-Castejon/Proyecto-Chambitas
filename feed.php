<?php  
session_start();  
include("conexion.php");  
  
if(!isset($_SESSION['id_usuario'])){  
    header("Location: login.php");  
    exit();  
}  

$id_usuario = $_SESSION['id_usuario'];  
$nombre_usuario = $_SESSION['nombre'];  

/* SALUDO DINÁMICO CON ICONOS PRO */
date_default_timezone_set('America/Monterrey');
$hora = date('G');
if ($hora >= 5 && $hora < 12) {
    $texto_tiempo = "Buenos días";
    $icono_tiempo = "<i class='fas fa-sun' style='color: #FFD700; margin-left: 8px;'></i>";
} elseif ($hora >= 12 && $hora < 19) {
    $texto_tiempo = "Buenas tardes";
    $icono_tiempo = "<i class='fas fa-cloud-sun' style='color: #ffa500; margin-left: 8px;'></i>";
} else {
    $texto_tiempo = "Buenas noches";
    $icono_tiempo = "<i class='fas fa-moon' style='color: #5c6bc0; margin-left: 8px;'></i>";
}
  
//* MIS PUBLICACIONES CON CONTADOR DE PROPUESTAS (Solo PENDIENTES) */  
$sql = "SELECT    
            Publicaciones.id_publicacion,   
            Publicaciones.titulo,   
            Publicaciones.descripcion,   
            Publicaciones.presupuesto,   
            Publicaciones.fecha_publicacion,   
            Publicaciones.estado AS estado_publicacion,
            Publicaciones.servicio_personalizado,
            Publicaciones.colonia,
            IFNULL(Servicios.nombre, Publicaciones.servicio_personalizado) AS servicio_nombre, 
            ubicaciones.latitud,   
            ubicaciones.longitud,   
            contrataciones.id_contratacion,   
            contrataciones.estado AS estado_contratacion,
            (SELECT ruta_imagen FROM imagenestrabajo WHERE id_publicacion = Publicaciones.id_publicacion LIMIT 1) as imagen_principal,
            (SELECT COUNT(*) FROM propuestas WHERE id_publicacion = Publicaciones.id_publicacion AND estado = 'Pendiente') AS total_propuestas
        FROM Publicaciones  
        LEFT JOIN Servicios ON Publicaciones.id_servicio = Servicios.id_servicio 
        LEFT JOIN ubicaciones ON Publicaciones.id_publicacion = ubicaciones.id_publicacion   
        LEFT JOIN contrataciones ON Publicaciones.id_publicacion = contrataciones.id_publicacion   
        WHERE Publicaciones.id_usuario = ?   
            AND Publicaciones.estado != 'Eliminada'   
        ORDER BY Publicaciones.fecha_publicacion DESC";
  
$stmt = $conexion->prepare($sql);  
$stmt->bind_param("i", $id_usuario);  
$stmt->execute();  
$resultado = $stmt->get_result();  
?>  
  
<!DOCTYPE html>  
<html lang="es">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Feed | Chambitas</title>  
<link rel="stylesheet" href="css/estilos.css">  
<link rel="icon" href="img/favicon.png">  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">  
<style>
    .ubicacion_link {
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .ubicacion_link:hover {
        color: #FFD700;
    }
    .img-ajustada {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    /* Estilo para el circulito de propuestas */
    .badge-propuestas {
        background-color: #ff4b4b;
        color: white;
        font-size: 0.75rem;
        font-weight: bold;
        padding: 3px 7px;
        border-radius: 50px;
        margin-left: 6px;
        display: inline-block;
        box-shadow: 0 2px 5px rgba(255, 75, 75, 0.4);
    }
    /* Sello de finalizado */
    .btn-finalizado {
        background-color: #28a745 !important;
        color: white !important;
        cursor: default !important;
        pointer-events: none;
    }
</style>
</head>  
<body>  
  
<?php include 'includes/navbar.php'; ?>
  
<div class="container">  
  <div class="encabezado_feed">  
    <h2 style="display: flex; align-items: center; gap: 8px;">
        <?php echo $texto_tiempo . ", " . htmlspecialchars($nombre_usuario); ?>
        <?php echo $icono_tiempo; ?>
    </h2> 
    <h1>Mis chambitas publicadas</h1>  
    <p class="descripcion_feed">  
      Administra los trabajos que has publicado y revisa las propuestas de trabajadores interesados.  
    </p>  
  </div>  
  
  <div class="feed">  
    <?php if($resultado->num_rows > 0): ?>  
      <?php while($row = $resultado->fetch_assoc()): ?>  
      <div class="chambita">  
        
        <div class="imagen_chambita">  
          <?php 
             $ruta_foto = !empty($row['imagen_principal']) ? $row['imagen_principal'] : 'uploads/imagenes/default.jpg';
          ?>
          <img src="<?php echo htmlspecialchars($ruta_foto); ?>" class="img-ajustada" loading="lazy" alt="Foto de chambita">  
        </div>  
  
        <div class="contenido_chambita">  
          <h3 class="titulo_chambita"><?php echo htmlspecialchars($row['titulo']); ?></h3>  
          <p class="servicio_chambita">Servicio: <?php echo htmlspecialchars($row['servicio_nombre']); ?></p> 
          <p class="descripcion_chambita"><?php echo htmlspecialchars(substr($row['descripcion'],0,120)); ?>...</p>  
          <p class="precio_chambita">Presupuesto: $<?php echo htmlspecialchars($row['presupuesto']); ?></p>  
  
          <?php if(!empty($row['latitud']) && !empty($row['longitud'])): ?>  
            <p class="ubicacion_chambita">  
              <i class="fas fa-map-marker-alt" style="color: #ff4b4b; margin-right: 5px;"></i>
              <a class="ubicacion_link" target="_blank" href="https://www.google.com/maps?q=<?php echo $row['latitud']; ?>,<?php echo $row['longitud']; ?>">
                  <?php 
                    if (!empty($row['colonia'])) {
                        echo htmlspecialchars($row['colonia']);
                    } else {
                        echo "Ubicación registrada";
                    }
                  ?>
              </a>  
            </p>  
          <?php endif; ?> 
  
          <p class="fecha_chambita">Publicado: <?php echo htmlspecialchars(date("d M Y", strtotime($row['fecha_publicacion']))); ?></p>  
  
          <div class="acciones_chambita">
              <?php if($row['estado_publicacion'] == 'Finalizado' || $row['estado_contratacion'] == 'Finalizado'): ?>
                  <span class="boton_accion btn-finalizado"><i class="fas fa-check-circle"></i> Finalizado</span>
                  <a class="boton_secundario" href="publicaciones/trabajo.php?id=<?php echo $row['id_publicacion']; ?>">Ver detalles</a>
              
              <?php elseif($row['estado_contratacion'] == 'Aceptado'): ?>
                  <a class="boton_finalizar" href="publicaciones/finalizar_trabajo.php?id_publicacion=<?php echo $row['id_publicacion']; ?>">Finalizar trabajo</a>
                  <a class="boton_secundario" href="publicaciones/trabajo.php?id=<?php echo $row['id_publicacion']; ?>">Ver detalles</a>
              
              <?php else: ?>
                  <a class="boton_accion" href="publicaciones/ver_propuestas.php?id_publicacion=<?php echo $row['id_publicacion']; ?>" style="display: inline-flex; align-items: center;">
                      Ver propuestas
                      <?php if($row['total_propuestas'] > 0): ?>
                          <span class="badge-propuestas"><?php echo $row['total_propuestas']; ?></span>
                      <?php endif; ?>
                  </a>
                  <a class="boton_secundario" href="publicaciones/trabajo.php?id=<?php echo $row['id_publicacion']; ?>">Ver detalles</a>
              <?php endif; ?>
          </div>
        </div>  
      </div>  
      <?php endwhile; ?>  
    <?php else: ?>  
      <div class="sin_publicaciones">  
        <h3>No tienes chambitas publicadas</h3>  
        <p>Publica tu primer trabajo para comenzar a recibir propuestas.</p> <br>  
        <a class="boton_accion" href="publicaciones/publicar.php">Publicar una chambita</a>  
      </div>  
    <?php endif; ?>  
  </div>  
</div>  
  
</body>  
</html>