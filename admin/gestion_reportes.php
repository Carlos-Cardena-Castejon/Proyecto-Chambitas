<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

// Recoger filtro de la URL para la navegación
$filtro = $_GET['ver'] ?? 'todos';

// CONSULTA MAESTRA: Une reportes de usuarios y publicaciones incluyendo la evidencia
$sql = "(SELECT 
            r.id_reporte, r.id_usuario_reporta, r.motivo, r.estado, r.fecha_reporte,
            'Usuario' as tipo_reporte, r.detalles as descripcion_reporte, r.evidencia_ruta,
            u1.nombre as denunciante_n, 
            u2.nombre as reportado_n, u2.apellido_paterno as reportado_a, u2.foto_perfil as reportado_foto, u2.id_usuario as id_u_reportado,
            NULL as titulo_trabajo, NULL as id_pub
        FROM reportes_usuarios r
        INNER JOIN usuarios u1 ON r.id_usuario_reporta = u1.id_usuario
        INNER JOIN usuarios u2 ON r.id_usuario_reportado = u2.id_usuario)
        
        UNION ALL
        
        (SELECT 
            rp.id_reporte, rp.id_usuario_reporta, rp.motivo, rp.estado, rp.fecha_reporte,
            'Publicación' as tipo_reporte, rp.descripcion as descripcion_reporte, rp.evidencia_ruta,
            u3.nombre as denunciante_n,
            u4.nombre as reportado_n, u4.apellido_paterno as reportado_a, u4.foto_perfil as reportado_foto, u4.id_usuario as id_u_reportado,
            p.titulo as titulo_trabajo, p.id_publicacion as id_pub
        FROM reportes_publicaciones rp
        INNER JOIN usuarios u3 ON rp.id_usuario_reporta = u3.id_usuario
        INNER JOIN usuarios u4 ON rp.id_usuario_dueno = u4.id_usuario
        INNER JOIN publicaciones p ON rp.id_publicacion = p.id_publicacion)
        
        ORDER BY CASE WHEN estado = 'Pendiente' THEN 1 ELSE 2 END, fecha_reporte DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justicia Chambitas | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { --primary: #FFD700; --dark: #121212; --bg: #f0f2f5; --danger: #ff4b4b; --success: #2ecc71; --info: #3498db; --gray: #6c757d; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; padding: 20px; }
        
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        /* Barra de Filtros */
        .filter-bar { display: flex; gap: 10px; margin-bottom: 25px; }
        .btn-filter { text-decoration: none; padding: 8px 20px; border-radius: 20px; background: white; color: var(--gray); font-weight: 600; font-size: 0.85rem; border: 1px solid #ddd; transition: 0.3s; }
        .btn-filter.active { background: var(--dark); color: white; border-color: var(--dark); }

        /* Grid de Reportes */
        .report-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        .report-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.05); display: flex; flex-direction: column; border-top: 8px solid #ccc; transition: 0.3s; position: relative; }
        .report-card.pendiente { border-top-color: var(--danger); }
        .report-card.revisado { border-top-color: var(--success); opacity: 0.8; }

        /* Evidencia visual */
        .evidence-preview { width: 100%; height: 180px; background: #e9ecef; overflow: hidden; cursor: pointer; position: relative; }
        .evidence-preview img { width: 100%; height: 100%; object-fit: cover; }
        .no-evidence { display: flex; align-items: center; justify-content: center; height: 100%; color: #adb5bd; font-size: 0.8rem; flex-direction: column; gap: 5px; }

        .date-badge { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 6px; font-size: 0.65rem; }
        .tipo-badge { position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 10px; color: white; font-size: 0.65rem; font-weight: bold; }
        .badge-user { background: #e67e22; }
        .badge-pub { background: var(--info); }

        .card-body { padding: 20px; flex-grow: 1; }
        .user-header { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
        .user-header img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        
        .motivo-tag { background: #fff1f1; color: var(--danger); font-weight: 700; font-size: 0.7rem; padding: 4px 8px; border-radius: 5px; display: inline-block; margin-bottom: 10px; }
        .desc-box { font-size: 0.85rem; color: #444; background: #f8f9fa; padding: 12px; border-radius: 10px; border-left: 3px solid #ddd; }

        .card-footer { padding: 15px 20px; background: #fafafa; border-top: 1px solid #eee; display: flex; justify-content: space-between; gap: 10px; }
        .btn-action { border: none; padding: 10px; border-radius: 10px; cursor: pointer; font-weight: bold; flex: 1; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .btn-delete { background: #6c757d; color: white; }
        .btn-gavel { background: var(--dark); color: white; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1 style="margin:0; font-size: 1.4rem;"><i class="fas fa-shield-alt"></i> Panel de Justicia</h1>
        <p style="color: var(--gray); margin: 0; font-size: 0.8rem;">Gestión de integridad NEXTIC</p>
    </div>
    <a href="../admin_home.php" class="btn-regresar">Volver al Inicio</a>
</div>

<div class="filter-bar">
    <a href="?ver=todos" class="btn-filter <?php echo $filtro == 'todos' ? 'active' : ''; ?>">Todos</a>
    <a href="?ver=Usuario" class="btn-filter <?php echo $filtro == 'Usuario' ? 'active' : ''; ?>">Usuarios</a>
    <a href="?ver=Publicación" class="btn-filter <?php echo $filtro == 'Publicación' ? 'active' : ''; ?>">Publicaciones</a>
</div>

<div class="report-grid">
    <?php while($row = $resultado->fetch_assoc()): 
        if($filtro !== 'todos' && $row['tipo_reporte'] !== $filtro) continue;

        $id_r = $row['id_reporte'];
        $tipo = $row['tipo_reporte'];
        $evidencia = !empty($row['evidencia_ruta']) ? '../'.$row['evidencia_ruta'] : '';
        $foto_u = !empty($row['reportado_foto']) ? '../'.$row['reportado_foto'] : '../img/usuario.png';
        $es_pendiente = ($row['estado'] == 'Pendiente');
    ?>
        <div class="report-card <?php echo $es_pendiente ? 'pendiente' : 'revisado'; ?>" id="fila-<?php echo $id_r; ?>">
            <span class="date-badge"><i class="far fa-clock"></i> <?php echo date("d/m/y H:i", strtotime($row['fecha_reporte'])); ?></span>
            
            <div class="evidence-preview" onclick="verImagen('<?php echo $evidencia; ?>')">
                <?php if($evidencia): ?>
                    <img src="<?php echo $evidencia; ?>">
                <?php else: ?>
                    <div class="no-evidence"><i class="fas fa-image-slash fa-2x"></i><br>Sin evidencia visual</div>
                <?php endif; ?>
                <span class="tipo-badge <?php echo ($tipo == 'Usuario') ? 'badge-user' : 'badge-pub'; ?>">
                    <?php echo strtoupper($tipo); ?>
                </span>
            </div>

            <div class="card-body">
                <div class="user-header">
                    <img src="<?php echo $foto_u; ?>">
                    <div>
                        <h4 style="margin:0;"><?php echo htmlspecialchars($row['reportado_n']); ?></h4>
                        <a href="../publicaciones/perfil_publico.php?id=<?php echo $row['id_u_reportado']; ?>" target="_blank" style="font-size:0.7rem; color: var(--info);">Ver Perfil</a>
                    </div>
                </div>

                <span class="motivo-tag"><?php echo htmlspecialchars($row['motivo']); ?></span>

                <?php if($tipo == 'Publicación'): ?>
                    <p style="font-size: 0.75rem; margin-bottom: 10px;">
                        <b>Chamba:</b> <a href="../publicaciones/trabajo.php?id=<?php echo $row['id_pub']; ?>" target="_blank" style="color:var(--danger);"><?php echo htmlspecialchars($row['titulo_trabajo']); ?></a>
                    </p>
                <?php endif; ?>

                <div class="desc-box">
                    <?php echo !empty($row['descripcion_reporte']) ? '"'.htmlspecialchars($row['descripcion_reporte']).'"' : '<i>Sin descripción detallada.</i>'; ?>
                </div>
            </div>

            <div class="card-footer" id="btn-container-<?php echo $id_r; ?>">
                <?php if($es_pendiente): ?>
                    <button onclick="eliminarReporte(<?php echo $id_r; ?>, '<?php echo $tipo; ?>')" class="btn-action btn-delete">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <button onclick="abrirJuicio(<?php echo $id_r; ?>, <?php echo $row['id_u_reportado']; ?>, '<?php echo addslashes($row['reportado_n']); ?>', <?php echo ($tipo == 'Publicación') ? $row['id_pub'] : 'null'; ?>)" class="btn-action btn-gavel">
                        <i class="fas fa-gavel"></i> Sentencia
                    </button>
                <?php else: ?>
                    <button onclick="eliminarReporte(<?php echo $id_r; ?>, '<?php echo $tipo; ?>')" class="btn-action btn-delete" style="flex:none; width: 100%;">
                        <i class="fas fa-trash"></i> Borrar Historial
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
    function verImagen(ruta) {
        if(!ruta) return;
        Swal.fire({ imageUrl: ruta, imageAlt: 'Evidencia', confirmButtonText: 'Cerrar', confirmButtonColor: '#333' });
    }

    function eliminarReporte(id, tipo) {
        Swal.fire({
            title: '¿Eliminar reporte?',
            text: "Se borrará de la base de datos definitivamente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`acciones_reporte.php?id=${id}&accion=eliminar_fisico&tipo_r=${tipo}`)
                    .then(res => res.text())
                    .then(data => {
                        if(data.trim() === "ok") {
                            const card = document.getElementById('fila-' + id);
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 500);
                        }
                    });
            }
        });
    }

    function abrirJuicio(idReporte, idUsuario, nombre, idPub) {
        let extra = idPub ? `<div style="margin-top:15px; text-align:left; background:#fff1f1; padding:10px; border-radius:8px; border:1px solid #ffcccc;">
            <label style="color:var(--danger); font-weight:bold; font-size:0.8rem; cursor:pointer;">
                <input type="checkbox" id="borrar_p" value="1"> ¿BORRAR PUBLICACIÓN REPORTADA?
            </label></div>` : "";

        Swal.fire({
            title: 'Justicia para ' + nombre,
            html: `<div style="text-align:left;">
                <label style="font-size:0.8rem; font-weight:bold;">Sanción:</label>
                <select id="tipo_s" class="swal2-input" style="width:100%; margin:10px 0;">
                    <option value="advertencia">Solo Advertencia</option>
                    <option value="suspender">Suspensión (7 días)</option>
                    <option value="ban">Baneo Permanente</option>
                </select>
                ${extra}
                <label style="font-size:0.8rem; font-weight:bold;">Motivo:</label>
                <textarea id="razon_s" class="swal2-textarea" style="margin:10px 0;"></textarea>
            </div>`,
            showCancelButton: true,
            confirmButtonText: 'Ejecutar',
            confirmButtonColor: '#dc3545',
            preConfirm: () => {
                const params = new URLSearchParams({
                    id: idReporte, id_u: idUsuario,
                    accion: document.getElementById('tipo_s').value,
                    razon: document.getElementById('razon_s').value,
                    borrar_p: document.getElementById('borrar_p')?.checked ? '1' : '0',
                    id_p: idPub || ''
                });
                return fetch(`acciones_reporte.php?${params.toString()}`).then(res => res.text());
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Éxito', 'Justicia aplicada', 'success').then(() => location.reload());
            }
        });
    }
</script>
</body>
</html>