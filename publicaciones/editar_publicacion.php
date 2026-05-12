<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])) { header("Location: ../login.php"); exit(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_usuario = $_SESSION['id_usuario'];

// Obtener datos actuales para comparar
$stmt = $conexion->prepare("SELECT p.*, u.latitud, u.longitud FROM publicaciones p LEFT JOIN ubicaciones u ON p.id_publicacion = u.id_publicacion WHERE p.id_publicacion = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$trabajo = $stmt->get_result()->fetch_assoc();

if (!$trabajo || $trabajo['id_usuario'] != $id_usuario) { header("Location: ../feed.php"); exit(); }

if(isset($_POST['guardar'])){
    $nuevo_titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
    $nueva_desc = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $nuevo_pres = (float)$_POST['presupuesto'];
    $n_lat = (double)$_POST['latitud'];
    $n_lon = (double)$_POST['longitud'];

    // --- LÓGICA DE NOTIFICACIONES FILTRADA ---
    $cambios = [];
    // Título NO se agrega a la lista de cambios para notificar
    if($nuevo_pres != $trabajo['presupuesto']) $cambios[] = "el presupuesto a $" . number_format($nuevo_pres, 2);
    if($nueva_desc != $trabajo['descripcion']) $cambios[] = "la descripción";
    if($n_lat != $trabajo['latitud'] || $n_lon != $trabajo['longitud']) $cambios[] = "la ubicación";

    // Actualización de Base de Datos (Siempre ocurre)
    $upd = $conexion->prepare("UPDATE publicaciones SET titulo=?, descripcion=?, presupuesto=? WHERE id_publicacion=?");
    $upd->bind_param("ssdi", $nuevo_titulo, $nueva_desc, $nuevo_pres, $id);
    $upd->execute();

    $upd_u = $conexion->prepare("UPDATE ubicaciones SET latitud=?, longitud=? WHERE id_publicacion=?");
    $upd_u->bind_param("ddi", $n_lat, $n_lon, $id);
    $upd_u->execute();

    // Solo enviar notificaciones si cambió algo más que el título
    if(!empty($cambios)){
        $msg_final = "Se actualizó " . implode(", ", $cambios) . " en la chamba: " . $nuevo_titulo;
        $resInt = $conexion->query("SELECT id_usuario FROM propuestas WHERE id_publicacion = $id");
        $insNotif = $conexion->prepare("INSERT INTO notificaciones (id_usuario, mensaje, tipo, link) VALUES (?, ?, 'edicion', ?)");
        $link = "trabajo.php?id=" . $id;
        
        while($row = $resInt->fetch_assoc()){
            $insNotif->bind_param("iss", $row['id_usuario'], $msg_final, $link);
            $insNotif->execute();
        }
    }
    
    header("Location: trabajo.php?id=$id&edit=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Chambita | Chambitas</title>
        <link rel="icon" type="image/png" href="../img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/ver_editar.css">
</head>
<body>

<div class="main-wrapper">
    <form method="POST" id="editForm" style="display: contents;">
        
        <div class="form-column">
            <div class="form-card-modern">
                <div class="section-header">
                    <div class="icon-box"><i class="fas fa-edit"></i></div>
                    <h2>Editar Información</h2>
                </div>

                <div class="input-grid-modern">
                    <div class="field">
                        <label>Título (Editable)</label>
                        <input type="text" name="titulo" id="titulo" maxlength="50" value="<?php echo htmlspecialchars($trabajo['titulo']); ?>" required>
                        <span id="count-titulo" class="char-count"></span>
                    </div>
                    <div class="field">
                        <label>Presupuesto ($MXN)</label>
                        <input type="number" name="presupuesto" id="presupuesto" value="<?php echo $trabajo['presupuesto']; ?>" required>
                        <div id="presupuesto-error" class="error-inline"></div>
                    </div>
                </div>

                <div class="field">
                    <label>Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="6" maxlength="500" required><?php echo htmlspecialchars($trabajo['descripcion']); ?></textarea>
                    <span id="count-desc" class="char-count"></span>
                </div>

<div class="actions-group" style="margin-top: 30px; display: flex; flex-direction: column; gap: 12px;">
    <button type="submit" name="guardar" class="btn-save" id="btnSubmit">
        <span>GUARDAR CAMBIOS</span> <i class="fas fa-check"></i>
    </button>
    
    <a href="trabajo.php?id=<?php echo $id; ?>" class="btn-cancel-modern">
        <i class=""></i> Cancelar
    </a>
</div>            </div>
        </div>

        <div class="map-panel">
            <div class="form-card-modern">
                <div class="section-header">
                    <div class="icon-box"><i class="fas fa-map-marker-alt"></i></div>
                    <h3>Ubicación</h3>
                </div>
                <div class="search-box-modern">
                    <input type="text" id="direccion" placeholder="Ej: Centro, Monterrey...">
                    <button type="button" onclick="buscarDireccion()"><i class="fas fa-search"></i></button>
                </div>
                <div id="mapa"></div>
                <input type="hidden" name="latitud" id="latitud" value="<?php echo $trabajo['latitud']; ?>">
                <input type="hidden" name="longitud" id="longitud" value="<?php echo $trabajo['longitud']; ?>">
                <div id="mapa-error" class="error-inline"></div>
            </div>
        </div>

    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../js/editar_publicacion.js"></script>
</body>
</html>