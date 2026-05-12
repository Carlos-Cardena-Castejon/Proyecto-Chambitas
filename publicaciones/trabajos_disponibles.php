<?php
/* =========================
   CONEXIÓN Y LÓGICA PHP
========================= */
include("../conexion.php");
session_start();

// Verificación de sesión
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

/* LÓGICA DE BÚSQUEDA */
$buscar = isset($_GET['buscar']) ? $conexion->real_escape_string($_GET['buscar']) : "";
$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : "";

$sql = "SELECT 
            Publicaciones.*, 
            IFNULL(Servicios.nombre, Publicaciones.servicio_personalizado) AS servicio_nombre, 
            ubicaciones.latitud, 
            ubicaciones.longitud, 
            imagenestrabajo.ruta_imagen
        FROM Publicaciones
        LEFT JOIN Servicios ON Publicaciones.id_servicio = Servicios.id_servicio
        LEFT JOIN ubicaciones ON Publicaciones.id_publicacion = ubicaciones.id_publicacion
        LEFT JOIN imagenestrabajo ON Publicaciones.id_publicacion = imagenestrabajo.id_publicacion
        WHERE Publicaciones.estado = 'Activa'";

if(!empty($buscar)) {
    $sql .= " AND (Publicaciones.titulo LIKE '%$buscar%' 
               OR Servicios.nombre LIKE '%$buscar%' 
               OR Publicaciones.servicio_personalizado LIKE '%$buscar%')";
}

if(!empty($categoria)) {
    $sql .= " AND Publicaciones.id_servicio = $categoria";
}

$sql .= " GROUP BY Publicaciones.id_publicacion ORDER BY fecha_publicacion DESC";
$resultado = $conexion->query($sql);
$total = $resultado->num_rows;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trabajos Disponibles | Chambitas</title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .ubicacion-card {
            margin: 0 20px 15px 20px;
            font-size: 0.85rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        /* Animación suave para cuando el texto aparezca */
        .texto-ubicacion {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body>

<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h1 class="titulo-seccion">Trabajos disponibles</h1>
    <p class="contador"><?php echo $total; ?> trabajos disponibles</p>

    <form class="busqueda" method="GET" style="margin-bottom:20px; display: flex; gap: 10px;">
      <input type="text" name="buscar" placeholder="Buscar trabajos..." value="<?php echo htmlspecialchars($buscar); ?>" 
             style="flex:1; padding:15px; border-radius:8px; border:1px solid #ccc;">
      <button type="submit" style="padding:10px 25px; border-radius:8px; background:#000; color:#fff; border:none; cursor:pointer; font-weight:bold;">
        Buscar
      </button>
    </form>

    <form class="filtros" method="GET" style="margin-bottom:30px; display:flex; gap:10px; flex-wrap:wrap;">
        <input type="hidden" name="buscar" value="<?php echo htmlspecialchars($buscar); ?>">
        <select name="categoria" style="padding:10px; border-radius:6px; border:1px solid #ccc;">
            <option value="">Todas las categorías</option>
            <?php
            $cats = $conexion->query("SELECT id_servicio, nombre FROM Servicios ORDER BY nombre");
            while($c = $cats->fetch_assoc()){
                $sel = ($categoria == $c['id_servicio']) ? "selected" : "";
                echo "<option value='{$c['id_servicio']}' $sel>{$c['nombre']}</option>";
            }
            ?>
        </select>
        <button type="submit" style="padding:10px 20px; border-radius:6px; background:#000; color:#fff; border:none; cursor:pointer;">Filtrar</button>
    </form>

    <div class="feed">
        <?php while($row = $resultado->fetch_assoc()): ?>
            <div class="chambita" onclick="window.location='trabajo.php?id=<?php echo $row['id_publicacion']; ?>'">
                <div class="imagen_chambita">
                    <img src="../<?php echo !empty($row['ruta_imagen']) ? $row['ruta_imagen'] : 'uploads/imagenes/default.jpg'; ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>
                
                <h3><?php echo htmlspecialchars($row['titulo']); ?></h3>
                <p class="precio">$<?php echo number_format($row['presupuesto'], 2); ?></p>
                <p class="servicio"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($row['servicio_nombre']); ?></p>

                <?php if(!empty($row['latitud']) && !empty($row['longitud'])): ?>
                    <div class="ubicacion-card" data-lat="<?php echo $row['latitud']; ?>" data-lon="<?php echo $row['longitud']; ?>">
                        <i class="fas fa-map-marker-alt" style="color: #ff4b4b;"></i> 
                        <span class="texto-ubicacion" style="opacity: 0.6;">Buscando zona...</span>
                    </div>
                <?php else: ?>
                    <div class="ubicacion-card" style="visibility: hidden;">
                        <i class="fas fa-map-marker-alt"></i> Sin ubicación
                    </div>
                <?php endif; ?>

                <a class="boton" href="trabajo.php?id=<?php echo $row['id_publicacion']; ?>">Ver trabajo</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", async function() {
    // Buscamos todas las tarjetas que tengan coordenadas
    const tarjetas = document.querySelectorAll('.ubicacion-card[data-lat][data-lon]');
    
    // Función para no saturar la API gratuita (espera medio segundo)
    const esperar = (ms) => new Promise(resolve => setTimeout(resolve, ms));

    for(let i = 0; i < tarjetas.length; i++) {
        const tarjeta = tarjetas[i];
        const lat = tarjeta.getAttribute('data-lat');
        const lon = tarjeta.getAttribute('data-lon');
        const spanTexto = tarjeta.querySelector('.texto-ubicacion');

        try {
            // Hacemos la consulta a OpenStreetMap
            const respuesta = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
            const data = await respuesta.json();
            
            if(data && data.address) {
                // Sacamos el municipio, ciudad o condado (dependiendo de cómo lo registre el mapa)
                let ciudad = data.address.city || data.address.town || data.address.municipality || data.address.county || "Ubicación registrada";
                
                // Actualizamos el texto en la tarjetita
                spanTexto.textContent = ciudad;
                spanTexto.style.opacity = "1";
            } else {
                spanTexto.textContent = "Ubicación registrada";
                spanTexto.style.opacity = "1";
            }
        } catch (error) {
            // Si el internet falla, mostramos esto por defecto
            spanTexto.textContent = "Ubicación registrada";
            spanTexto.style.opacity = "1";
        }
        
        // Esperamos 600ms antes de consultar el siguiente trabajo
        await esperar(600); 
    }
});
</script>

</body>
</html>