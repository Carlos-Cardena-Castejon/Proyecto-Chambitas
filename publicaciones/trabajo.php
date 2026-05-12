<?php
session_start();
include("../conexion.php");

/* ==========================
1. OBTENER ID DEL TRABAJO
========================== */
if(!isset($_GET['id'])){
    die("Trabajo no encontrado");
}
$id = (int) $_GET['id'];

/* ==========================
2. CONSULTA DEL TRABAJO
========================== */
$sql = "SELECT 
            Publicaciones.*, 
            IFNULL(Servicios.nombre, Publicaciones.servicio_personalizado) AS servicio_nombre, 
            ubicaciones.latitud, 
            ubicaciones.longitud
        FROM Publicaciones
        LEFT JOIN Servicios ON Publicaciones.id_servicio = Servicios.id_servicio
        LEFT JOIN ubicaciones ON Publicaciones.id_publicacion = ubicaciones.id_publicacion
        WHERE Publicaciones.id_publicacion = $id";

$resultado = $conexion->query($sql);
if($resultado->num_rows == 0) die("Trabajo no existe");
$trabajo = $resultado->fetch_assoc();

/* ==========================
3. VERIFICACIONES DE ROL Y ESTADO
========================= */
$id_sesion = $_SESSION['id_usuario'] ?? 0;
$es_dueno = ($id_sesion == $trabajo['id_usuario']);
$se_puede_modificar = ($trabajo['estado'] == 'Activa');

$ya_envio = false;
$datos_mi_propuesta = null;

if($id_sesion > 0 && !$es_dueno){
    $sqlCheck = "SELECT * FROM propuestas WHERE id_publicacion = $id AND id_usuario = $id_sesion";
    $resCheck = $conexion->query($sqlCheck);
    if($resCheck->num_rows > 0){
        $ya_envio = true;
        $datos_mi_propuesta = $resCheck->fetch_assoc();
    }
}

/* ==========================
4. OBTENER IMAGENES
========================== */
$sqlImg = "SELECT * FROM imagenestrabajo WHERE id_publicacion = $id";
$resImg = $conexion->query($sqlImg);
$imagenes = [];
while($img = $resImg->fetch_assoc()) $imagenes[] = $img;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($trabajo['titulo']); ?></title>
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/trabajo_detalle.css">
    <style>
        /* NAVBAR NEGRA */
        .navbar {
            background-color: #000 !important;
            padding: 10px 0;
            border-bottom: 1px solid #333;
        }
        .nav-container {
            width: 90%;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo img { height: 40px; }

        /* MODALES */
        .modal { display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter: blur(3px); }
        .modal-contenido { 
            background:white; 
            margin: 10% auto; 
            padding:25px; 
            width: 85%; 
            max-width:380px; 
            border-radius:20px; 
            box-shadow: 0 15px 30px rgba(0,0,0,0.2); 
        }

        /* MEDIA QUERIES PARA MÓVIL */
        @media (max-width: 768px) {
            .detalle-grid {
                display: flex;
                flex-direction: column;
            }
            .contenido-trabajo { order: 1; }
            .panel-trabajo { order: 2; margin-top: 20px; }
            .modal-contenido { margin: 25% auto; width: 80%; }
            .logo img { height: 30px; }
        }

        /* TUS BOTONES ORIGINALES */
        .cerrar { float:right; font-size:24px; cursor:pointer; color: #888; }
        .btn-editar-oferta { background: #ffa500 !important; color: white !important; margin-top: 10px; width: 100%; padding: 12px; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; }
        .btn-retirar-oferta { background: #f1f2f6 !important; color: #ff4b4b !important; margin-top: 10px; width: 100%; padding: 10px; border: 1px solid #ff4b4b; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .input-propuesta { width:100%; padding:12px; margin:10px 0; border: 2px solid #f1f2f6; border-radius: 10px; box-sizing: border-box; }
        .char-count { display: block; text-align: right; font-size: 0.75rem; color: #888; margin-top: -5px; margin-bottom: 10px; }
        .btn-reportar { background: #fff1f1; color: #ff4b4b; border: 1px solid #ff4b4b; padding: 10px; width: 100%; border-radius: 10px; cursor: pointer; font-weight: bold; margin-top: 10px; transition: 0.3s; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="../feed.php"><img src="../img/logo.png"></a></div>
        <a href="trabajos_disponibles.php" class="volver-btn"><i class="fas fa-chevron-left"></i> Volver</a>
    </div>
</nav>

<div class="container" style="margin-top: 20px;">
    <div class="detalle-grid">
        <div class="contenido-trabajo">
            <h1 class="titulo-trabajo"><?php echo htmlspecialchars($trabajo['titulo']); ?></h1>
            <div class="imagen-principal">
                <img id="imagenPrincipal" src="../<?php echo (count($imagenes)>0) ? $imagenes[0]['ruta_imagen'] : 'uploads/imagenes/default.jpg'; ?>">
            </div>
            <div class="miniaturas">
                <?php foreach($imagenes as $img): ?>
                    <img class="miniatura" src="../<?php echo $img['ruta_imagen']; ?>" onclick="cambiarImagen(this.src)">
                <?php endforeach; ?>
            </div>
            <h3>Descripción del trabajo</h3>
            <p class="descripcion-larga"><?php echo nl2br(htmlspecialchars($trabajo['descripcion'])); ?></p>

            <?php if(!empty($trabajo['latitud'])): ?>
                <h3>Ubicación aproximada</h3>
                <iframe class="mapa" 
                    src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo ($trabajo['longitud']-0.005); ?>%2C<?php echo ($trabajo['latitud']-0.005); ?>%2C<?php echo ($trabajo['longitud']+0.005); ?>%2C<?php echo ($trabajo['latitud']+0.005); ?>&layer=mapnik&marker=<?php echo $trabajo['latitud']; ?>%2C<?php echo $trabajo['longitud']; ?>">
                </iframe>
            <?php endif; ?>
        </div>

        <div class="panel-trabajo">
            <div class="card-panel">
                <p class="estado-tag <?php echo strtolower($trabajo['estado']); ?>">● <?php echo $trabajo['estado']; ?></p>
                <p class="precio-panel">$<?php echo number_format($trabajo['presupuesto'], 2); ?></p>
                <p class="info-line">🧰 <?php echo htmlspecialchars($trabajo['servicio_nombre']); ?></p>
                <p class="info-line">📅 <?php echo date("d M Y", strtotime($trabajo['fecha_publicacion'])); ?></p>

                <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">

                <?php if($id_sesion > 0): ?>
                    <?php if($es_dueno): ?>
                        <div class="admin-actions">
                            <h3 style="margin-bottom:15px; font-size:1.1rem;"><i class="fas fa-user-shield"></i> Panel de Control</h3>
                            <?php if($se_puede_modificar): ?>
                                <a href="editar_publicacion.php?id=<?php echo $id; ?>" class="btn-editar">
                                    <i class="fas fa-pen"></i> Editar Publicación
                                </a>
                                <button class="btn-eliminar-pro" onclick="confirmarEliminar(<?php echo $id; ?>)">
                                    <i class="fas fa-trash-alt"></i> Eliminar Publicación
                                </button>
                            <?php else: ?>
                                <p class="info-lock"><i class="fas fa-lock"></i> Publicación cerrada a cambios.</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php if(!$ya_envio && $se_puede_modificar): ?>
                            <button class="boton-propuesta" onclick="abrirModal()">Enviar propuesta</button>
                        <?php elseif($ya_envio && $se_puede_modificar): ?>
                            <div class="mi-oferta-status">
                                <p style="color:#28a745; font-weight:700; margin-bottom:10px;"><i class="fas fa-check-double"></i> Oferta enviada</p>
                                <button class="btn-editar-oferta" onclick="abrirModalEditar()">Editar mi oferta</button>
                                <button class="btn-retirar-oferta" onclick="confirmarRetirarse(<?php echo $datos_mi_propuesta['id_propuesta']; ?>)">Retirar propuesta</button>
                            </div>
                        <?php endif; ?>
                        
                        <button class="btn-reportar" onclick="abrirModalReporte()">
                            <i class="fas fa-flag"></i> Reportar publicación
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="modalPropuesta" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModal()">×</span>
        <h2>Enviar propuesta</h2>
        <form action="propuestas/guardar_propuesta.php" method="POST">
            <input type="hidden" name="id_publicacion" value="<?php echo $id; ?>">
            <label>¿Cuánto cobras? ($)</label>
            <input type="number" name="precio" class="input-propuesta" min="50" max="100000" required>
            <label>¿Días para terminar? (Máx 99)</label>
            <input type="number" name="tiempo_estimado" class="input-propuesta" min="1" max="99" required oninput="if(this.value.length > 2) this.value = this.value.slice(0,2);">
            <label>Mensaje para el dueño</label>
            <textarea name="mensaje" class="input-propuesta" style="height:100px; resize:none;" maxlength="255" oninput="contar(this, 'c1')"></textarea>
            <span id="c1" class="char-count">0 / 255</span>
            <button type="submit" class="boton-propuesta">Enviar ahora</button>
        </form>
    </div>
</div>

<?php if($ya_envio): ?>
<div id="modalEditarPropuesta" class="modal">
    <div class="modal-contenido">
        <span class="cerrar" onclick="cerrarModalEditar()">×</span>
        <h2>Actualizar mi oferta</h2>
        <form action="propuestas/actualizar_propuesta.php" method="POST">
            <input type="hidden" name="id_propuesta" value="<?php echo $datos_mi_propuesta['id_propuesta']; ?>">
            <input type="hidden" name="id_publicacion" value="<?php echo $id; ?>">
            <label>Nuevo precio ($)</label>
            <input type="number" name="precio" class="input-propuesta" value="<?php echo $datos_mi_propuesta['precio_oferta']; ?>" min="50" max="100000" required>
            <label>Nuevo tiempo estimado (Días)</label>
            <input type="number" name="tiempo_estimado" class="input-propuesta" value="<?php echo $datos_mi_propuesta['tiempo_estimado']; ?>" min="1" max="99" required oninput="if(this.value.length > 2) this.value = this.value.slice(0,2);">
            <label>Mensaje actualizado</label>
            <textarea name="mensaje" class="input-propuesta" style="height:100px; resize:none;" maxlength="255" oninput="contar(this, 'c2')"><?php echo htmlspecialchars($datos_mi_propuesta['mensaje']); ?></textarea>
            <span id="c2" class="char-count">0 / 255</span>
            <button type="submit" class="btn-editar-oferta">Guardar Cambios</button>
        </form>
    </div>
</div>
<?php endif; ?>

<div id="modalReporte" class="modal">
    <div class="modal-contenido" style="border-top: 5px solid #ff4b4b;">
        <span class="cerrar" onclick="cerrarModalReporte()">×</span>
        <h2 style="color: #ff4b4b;"><i class="fas fa-exclamation-triangle"></i> Reportar Trabajo</h2>
        <form action="../auth/guardar_reporte_pub.php" method="POST">
            <input type="hidden" name="id_publicacion" value="<?php echo $id; ?>">
            <input type="hidden" name="id_usuario_dueno" value="<?php echo $trabajo['id_usuario']; ?>">
            <label>Motivo del reporte:</label>
            <select name="motivo" class="input-propuesta" required>
                <option value="Contenido Inapropiado">Contenido Inapropiado</option>
                <option value="Estafa">Fraude o Estafa</option>
                <option value="Spam">Spam / Publicidad falsa</option>
                <option value="Venta de Producto">Venta de objeto/producto</option>
                <option value="Venta de Animales">Venta o tráfico de animales</option>
            </select>
            <label>Detalles adicionales:</label>
            <textarea name="descripcion" class="input-propuesta" style="height:80px; resize:none;" maxlength="255" oninput="contar(this, 'c3')" placeholder="Explica brevemente el problema..."></textarea>
            <span id="c3" class="char-count">0 / 255</span>
            <button type="submit" class="btn-editar-oferta" style="background: #333 !important;">Enviar Reporte</button>
        </form>
    </div>
</div>

<script>
function abrirModal(){ document.getElementById("modalPropuesta").style.display="block"; }
function cerrarModal(){ document.getElementById("modalPropuesta").style.display="none"; }
function abrirModalEditar(){ document.getElementById("modalEditarPropuesta").style.display="block"; }
function cerrarModalEditar(){ document.getElementById("modalEditarPropuesta").style.display="none"; }
function abrirModalReporte(){ document.getElementById("modalReporte").style.display="block"; }
function cerrarModalReporte(){ document.getElementById("modalReporte").style.display="none"; }

window.onclick = function(e){
    if(e.target.className == 'modal') { 
        cerrarModal(); 
        if(document.getElementById("modalEditarPropuesta")) cerrarModalEditar(); 
        cerrarModalReporte();
    }
}
function cambiarImagen(ruta) { document.getElementById("imagenPrincipal").src = ruta; }
function contar(el, id) { document.getElementById(id).innerText = el.value.length + " / 255"; }

function confirmarRetirarse(idProp) {
    Swal.fire({
        title: '¿Retirar propuesta?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ff4b4b',
        confirmButtonText: 'Sí, retirar'
    }).then((res) => { if (res.isConfirmed) window.location.href = "propuestas/eliminar_propuesta.php?id=" + idProp + "&id_publicacion=<?php echo $id; ?>"; });
}

function confirmarEliminar(idPub) {
    Swal.fire({
        title: '¿Eliminar trabajo?',
        text: "Se borrarán todas las propuestas recibidas.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#ff4b4b',
        confirmButtonText: 'Sí, eliminar todo'
    }).then((res) => { if (res.isConfirmed) window.location.href = "eliminar_publicacion.php?id=" + idPub; });
}
</script>
</body>
</html>