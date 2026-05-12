<?php
include("../conexion.php");
$sql = "SELECT id_servicio, nombre FROM servicios WHERE estado = 1 ORDER BY nombre ASC";
$servicios = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicar Chambita | Chambitas</title>
        <link rel="icon" type="image/png" href="../img/favicon.png">


    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="../css/publicar.css"/>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Efecto Glassmorphism */
        .container-form {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .char-count { font-size: 0.75rem; color: #888; text-align: right; display: block; margin-top: 4px; }
        #mapa { height: 300px; border-radius: 12px; border: 2px solid #eee; margin-top: 10px; z-index: 1; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #grupoOtroServicio { animation: fadeIn 0.3s ease-in-out; }
    </style>
</head>
<body>

<div class="container-form">
    <div class="header-form">
        <h1><i class="fas fa-hammer"></i> Nueva Chambita</h1>
        <p>Solo disponible para el estado de <b>Nuevo León</b>.</p>
    </div>

    <form id="formChambita" action="guardar_publicacion.php" method="POST" enctype="multipart/form-data" class="chambitas-form" onsubmit="return confirmarPublicacion(event);">
        
        <div class="form-section">
            <label><i class="fas fa-tag"></i> Título de la chamba</label>
            <input type="text" name="titulo" id="titulo" placeholder="Ej. Reparar regadera eléctrica" maxlength="50" required>
            <span class="char-count" id="count-titulo">50 caracteres restantes</span>

            <label><i class="fas fa-align-left"></i> Descripción del problema</label>
            <textarea name="descripcion" id="descripcion" rows="4" placeholder="Cuéntanos más detalles..." maxlength="500" required></textarea>
            <span class="char-count" id="count-desc">500 caracteres restantes</span>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fas fa-list"></i> Tipo de Servicio</label>
                <select name="servicio" id="selectServicio" required onchange="verificarOtroServicio()">
                    <option value="">Selecciona una categoría...</option>
                    <?php 
                    if ($servicios && $servicios->num_rows > 0) {
                        while($fila = $servicios->fetch_assoc()) {
                            echo '<option value="'.$fila['id_servicio'].'">'.htmlspecialchars($fila['nombre']).'</option>';
                        }
                    }
                    ?>
                    <option value="otro">Otro (Especificar...)</option>
                </select>
            </div>

            <div class="form-group">
                <label><i class="fas fa-dollar-sign"></i> Presupuesto Estimado</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 15px; font-weight: bold; color: #333;">$</span>
                    <input type="number" name="presupuesto" id="presupuesto" placeholder="0.00" min="50" max="100000" step="0.01" required 
                           style="padding-left: 30px !important; font-weight: 700; color: #27ae60; font-size: 1.1rem;">
                </div>
            </div>
        </div>

        <div id="grupoOtroServicio" style="display: none; margin-bottom: 15px;">
            <label><i class="fas fa-pen"></i> ¿Cuál servicio necesitas?</label>
            <input type="text" name="otro_servicio_nombre" id="otro_servicio" maxlength="15" placeholder="Ej. Evento, Cocina...">
            <small id="charCount" style="color: #888; font-size: 12px;">Máximo 15 caracteres</small>
        </div>

        <div class="form-section" style="margin-top: 20px;">
            <label><i class="fas fa-camera"></i> Fotos del problema</label>
            <div class="file-input-wrapper" id="dropzone">
                <input type="file" name="imagenes[]" id="imagenes" multiple required accept="image/*" onchange="actualizarContadorFotos()">
                <div class="upload-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p id="foto-text">Toca para subir fotos o arrastra aquí</p>
                    <span>Máximo 5 imágenes</span>
                </div>
            </div>
            <div id="previsualizacion-fotos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 10px; margin-top: 15px;"></div>
        </div>

        <div class="map-section" style="margin-top: 20px;">
            <label><i class="fas fa-map-marker-alt"></i> Ubicación en Nuevo León</label>
            <div class="busqueda-wrapper">
                <input type="text" id="direccion" placeholder="Colonia o Municipio (Ej: Cumbres)">
                <button type="button" class="buscar-btn" id="btnBuscar" onclick="buscarDireccion()">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
            <div id="mapa"></div>
            <input type="hidden" name="latitud" id="latitud">
            <input type="hidden" name="longitud" id="longitud">
            <input type="hidden" name="municipio" id="municipio">
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-btn" id="btnPublicar">
                <i class="fas fa-paper-plane"></i> Publicar Chambita
            </button>
            <a href="../feed.php" class="btn-cancel">
                <i class="fas fa-times"></i> Cancelar y volver
            </a>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../js/publicar.js"></script>
</body>
</html>
</body>
</html>