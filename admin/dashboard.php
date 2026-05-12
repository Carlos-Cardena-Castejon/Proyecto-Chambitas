<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

// Consultas para los totales
$usuarios_count = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$publicaciones_count = $conexion->query("SELECT COUNT(*) as total FROM publicaciones WHERE estado != 'Eliminada'")->fetch_assoc()['total'];
$servicios_count = $conexion->query("SELECT COUNT(*) as total FROM servicios")->fetch_assoc()['total'];

// Gráfica por Categoría
$sql_cat = "SELECT c.nombre, COUNT(s.id_servicio) as total 
            FROM categorias c 
            LEFT JOIN servicios s ON c.id_categoria = s.id_categoria 
            GROUP BY c.id_categoria";
$res_cat_stats = $conexion->query($sql_cat);
$labels_cat = []; $data_cat = [];
while($row = $res_cat_stats->fetch_assoc()){
    $labels_cat[] = $row['nombre'];
    $data_cat[] = $row['total'];
}

// Actividad Reciente
$recientes = $conexion->query("SELECT titulo, fecha_publicacion FROM publicaciones ORDER BY fecha_publicacion DESC LIMIT 5");

// Reporte detallado para Excel - TODO COMPLETO
$lista_usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY nombre ASC");
$lista_publicaciones = $conexion->query("SELECT p.*, u.nombre as autor FROM publicaciones p INNER JOIN usuarios u ON p.id_usuario = u.id_usuario ORDER BY p.fecha_publicacion DESC");
$lista_servicios_det = $conexion->query("SELECT s.*, c.nombre as categoria FROM servicios s INNER JOIN categorias c ON s.id_categoria = c.id_categoria ORDER BY s.nombre ASC");

$nombre_admin = $_SESSION['nombre'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Chambitas Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { 
            --primary: #FFD700; 
            --dark-header: #121212; 
            --light-header: #FFFBEF; 
            --sidebar-width: 260px; 
        }
        
        body { font-family: 'Inter', sans-serif; background: #fff; margin: 0; display: flex; }

        /* --- TU MENU (SIN CAMBIOS) --- */
        .sidebar { width: 260px; height: 100vh; background: #121212; color: white; position: fixed; padding: 30px 20px; box-sizing: border-box; z-index: 1000; }
        .sidebar h2 { color: var(--primary); text-align: center; font-weight: 800; margin-bottom: 40px; }
        .user-box { border: 1px solid #333; padding: 15px; border-radius: 12px; margin-bottom: 30px; }
        .user-box span { font-size: 0.7rem; color: #888; text-transform: uppercase; }
        .user-box p { margin: 5px 0 0; color: var(--primary); font-weight: 600; }
        .nav-menu a { display: flex; align-items: center; color: white; text-decoration: none; padding: 12px; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .nav-menu a i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-menu a:hover, .nav-menu a.active { background: var(--primary); color: black; font-weight: bold; }
        .nav-menu a.logout { color: #ff6666; margin-top: 20px; }
        
        .content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); box-sizing: border-box; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }

        .btn-pro { 
            background: #000; 
            color: var(--primary); 
            padding: 12px 25px; 
            border-radius: 10px; 
            font-weight: 800; 
            border: none; 
            cursor: pointer; 
            transition: 0.3s; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .btn-pro:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }

        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 20px; border: 1px solid #eee; transition: 0.3s; }
        .stat-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .stat-card h5 { margin: 0; text-transform: uppercase; color: #aaa; font-size: 0.75rem; letter-spacing: 1px; }
        .stat-card h2 { margin: 10px 0 0; font-size: 2.5rem; font-weight: 800; color: #000; }

        .charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
        .chart-box { background: white; padding: 30px; border-radius: 24px; border: 1px solid #eee; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .chart-box h4 { margin: 0 0 20px 0; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .recent-item { padding: 12px 0; border-bottom: 1px solid #f5f5f5; display: flex; flex-direction: column; }
        .recent-item span { font-weight: 700; color: #000; font-size: 0.9rem; }
        .recent-item small { color: #bbb; }
    </style>
</head>
<body>

<?php 
    $sidebar_path = "sidebar.php";
    if (file_exists($sidebar_path)) { include($sidebar_path); } 
?>

<div class="content">
    <div class="header-flex">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 6px; height: 40px; background: var(--primary); border-radius: 2px;"></div>
            <h2 style="margin:0; font-weight:800; font-size: 2.2rem;">Análisis del <span>Sistema</span></h2>
        </div>
        <button class="btn-pro" onclick="exportarExcelCompleto()">
            <i class="fas fa-file-excel"></i> Exportar Reporte
        </button>
    </div>

    <div class="grid-stats">
        <div class="stat-card"><h5>Usuarios</h5><h2><?php echo $usuarios_count; ?></h2></div>
        <div class="stat-card"><h5>Publicaciones</h5><h2><?php echo $publicaciones_count; ?></h2></div>
        <div class="stat-card"><h5>Servicios</h5><h2><?php echo $servicios_count; ?></h2></div>
    </div>

    <div class="charts-grid">
        <div class="chart-box">
            <h4><i class="fas fa-chart-bar" style="color: var(--primary);"></i> Actividad General</h4>
            <div style="height: 350px;"><canvas id="mainChart"></canvas></div>
        </div>

        <div class="chart-box">
            <h4><i class="fas fa-bolt" style="color: var(--primary);"></i> Recientes</h4>
            <?php while($r = $recientes->fetch_assoc()): ?>
            <div class="recent-item">
                <span><?php echo htmlspecialchars($r['titulo']); ?></span>
                <small><?php echo date('d M, h:i a', strtotime($r['fecha_publicacion'])); ?></small>
            </div>
            <?php endwhile; ?>
            <div style="height: 180px; margin-top: 20px;"><canvas id="pieChart"></canvas></div>
        </div>
    </div>
</div>

<div id="dataReport" style="display:none;">
    <table id="tblReporteFull">
        <tr><th colspan="6" style="background:#FFD700; font-size: 1.5rem;">REPORTE INTEGRAL CHAMBITAS</th></tr>
        <tr><td>Fecha de Reporte:</td><td><?php echo date('d/m/Y H:i'); ?></td></tr>
        
        <tr><th colspan="6" style="background:#000; color:#fff;">RESUMEN</th></tr>
        <tr><td>Total Usuarios:</td><td><?php echo $usuarios_count; ?></td></tr>
        <tr><td>Total Publicaciones:</td><td><?php echo $publicaciones_count; ?></td></tr>
        <tr><td>Total Servicios:</td><td><?php echo $servicios_count; ?></td></tr>

        <tr><th colspan="6" style="background:#000; color:#fff;">LISTADO DE USUARIOS</th></tr>
        <tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Dirección</th><th>Fecha Registro</th></tr>
        <?php while($u = $lista_usuarios->fetch_assoc()): ?>
        <tr>
            <td><?php echo $u['id_usuario']; ?></td>
            <td><?php echo htmlspecialchars($u['nombre']); ?></td>
            <td><?php echo htmlspecialchars($u['correo']); ?></td>
            <td><?php echo htmlspecialchars($u['telefono']); ?></td>
            <td><?php echo htmlspecialchars($u['direccion'] ?? 'N/A'); ?></td>
            <td><?php echo $u['fecha_registro']; ?></td>
        </tr>
        <?php endwhile; ?>

        <tr><th colspan="6" style="background:#000; color:#fff;">LISTADO DE PUBLICACIONES</th></tr>
        <tr><th>ID</th><th>Título</th><th>Autor</th><th>Presupuesto</th><th>Estado</th><th>Fecha</th></tr>
        <?php while($p = $lista_publicaciones->fetch_assoc()): ?>
        <tr>
            <td><?php echo $p['id_publicacion']; ?></td>
            <td><?php echo htmlspecialchars($p['titulo']); ?></td>
            <td><?php echo htmlspecialchars($p['autor']); ?></td>
            <td><?php echo $p['presupuesto']; ?></td>
            <td><?php echo $p['estado']; ?></td>
            <td><?php echo $p['fecha_publicacion']; ?></td>
        </tr>
        <?php endwhile; ?>

        <tr><th colspan="6" style="background:#000; color:#fff;">CATÁLOGO DE SERVICIOS</th></tr>
        <tr><th>ID</th><th>Servicio</th><th>Categoría</th><th>Descripción</th><th>Estado</th></tr>
        <?php while($s = $lista_servicios_det->fetch_assoc()): ?>
        <tr>
            <td><?php echo $s['id_servicio']; ?></td>
            <td><?php echo htmlspecialchars($s['nombre']); ?></td>
            <td><?php echo htmlspecialchars($s['categoria']); ?></td>
            <td><?php echo htmlspecialchars($s['descripcion'] ?? '-'); ?></td>
            <td><?php echo ($s['estado'] == 1 ? 'Activo' : 'Inactivo'); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
    new Chart(document.getElementById('mainChart'), {
        type: 'bar',
        data: {
            labels: ['Usuarios', 'Publicaciones', 'Servicios'],
            datasets: [{
                data: [<?php echo $usuarios_count ?>, <?php echo $publicaciones_count ?>, <?php echo $servicios_count ?>],
                backgroundColor: ['#000000', '#FFD700', '#555'],
                borderRadius: 10
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels_cat); ?>,
            datasets: [{
                data: <?php echo json_encode($data_cat); ?>,
                backgroundColor: ['#000000', '#FFD700', '#eee', '#444'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
    });

    function exportarExcelCompleto() {
        let excelContent = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="UTF-8"><style>th{border:1px solid #000;}td{border:1px solid #ccc;}</style></head><body>${document.getElementById('tblReporteFull').outerHTML}</body></html>`;
        const blob = new Blob([excelContent], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'Reporte_General_Chambitas.xls';
        a.click();
    }
</script>

</body>
</html>