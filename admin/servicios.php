<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

$nombre_admin = $_SESSION['nombre'] ?? 'Administrador';

// CONSULTA: Traemos servicios y categorías
$sql = "SELECT s.*, c.nombre as categoria_nombre 
        FROM servicios s 
        INNER JOIN categorias c ON s.id_categoria = c.id_categoria 
        ORDER BY s.id_servicio DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Servicios | Chambitas Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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

        /* --- CONTENIDO --- */
        .main-content { margin-left: var(--sidebar-width); padding: 50px; width: calc(100% - var(--sidebar-width)); box-sizing: border-box; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .titulo-seccion { display: flex; align-items: center; gap: 15px; }
        .titulo-seccion .barrita { width: 6px; height: 40px; background: var(--primary); border-radius: 2px; }
        .titulo-seccion h1 { margin: 0; font-size: 2.2rem; font-weight: 900; }

        /* --- BARRA DE FILTROS --- */
        .barra-filtros { 
            background: #fff; padding: 20px; border-radius: 15px; margin-bottom: 25px; 
            display: flex; gap: 15px; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 0.65rem; font-weight: 800; color: #999; text-transform: uppercase; margin-left: 5px; }
        .barra-filtros input, .barra-filtros select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 10px; font-family: 'Inter'; outline: none; font-size: 0.9rem;}

        /* TABLA */
        .table-container { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--dark); color: var(--primary); padding: 20px; text-align: left; font-size: 0.75rem; text-transform: uppercase; font-weight: 800; }
        td { padding: 18px 20px; border-bottom: 1px solid #f8f8f8; color: #444; }

        /* SWITCH TOGGLE */
        .switch { position: relative; display: inline-block; width: 40px; height: 20px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #28a745; }
        input:checked + .slider:before { transform: translateX(20px); }

        .btn-delete { color: #ccc; transition: 0.3s; font-size: 1.1rem; border: none; background: none; cursor: pointer; text-decoration: none; }
        .btn-delete:hover { color: #ff4d4d; transform: scale(1.2); }
    </style>
</head>
<body>

 <?php 
    $sidebar_path = "sidebar.php";
    if (file_exists($sidebar_path)) {
        include($sidebar_path);
    } else {
        echo '<div class="sidebar">
                <h2>CHAMBITAS</h2>
                <div class="user-box"><span>Admin</span><p>'.htmlspecialchars($nombre_admin).'</p></div>
                <nav class="nav-menu">
                    <a href="../admin_home.php"><i class="fas fa-house"></i> Inicio</a>
                    <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                    <a href="usuarios_admin.php"><i class="fas fa-user-gear"></i> Usuarios</a>
                    <a href="publicaciones_admin.php"><i class="fas fa-file-pen"></i> Publicaciones</a>
                    <a href="servicios.php" class="active"><i class="fas fa-briefcase"></i> Servicios</a>
                    <a href="../logout.php" class="logout"><i class="fas fa-power-off"></i> Salir</a>
                </nav>
              </div>';
    }
?>

<div class="main-content">
    <div class="header-flex">
        <div class="titulo-seccion">
            <div class="barrita"></div>
            <h1>Gestionar <span>Servicios</span></h1>
        </div>
        <a href="agregar_servicio.php" style="background:#000; color:var(--primary); text-decoration:none; padding:12px 25px; border-radius:12px; font-weight:800;"><i class="fas fa-plus"></i> Nuevo Servicio</a>
    </div>

    <div class="barra-filtros">
        <div class="filter-group" style="flex: 2;">
            <label>Buscar servicio</label>
            <input type="text" id="busquedaTexto" placeholder="Nombre del servicio..." onkeyup="aplicarFiltros()">
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select id="busquedaEstado" onchange="aplicarFiltros()">
                <option value="">Todos</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Inactivos</option>
            </select>
        </div>
        <div class="filter-group" style="margin-left: auto;">
            <button onclick="limpiarFiltros()" style="background:none; border:none; color: #ff6666; cursor:pointer; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-top: 15px;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </div>

    <div class="table-container">
        <table id="tablaServicios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Servicio</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($s = $resultado->fetch_assoc()): ?>
                <tr class="fila-svc">
                    <td style="color: #bbb;">#<?php echo $s['id_servicio']; ?></td>
                    <td class="col-nombre" style="font-weight: 800;"><?php echo htmlspecialchars($s['nombre']); ?></td>
                    <td><span style="background:#f0f0f0; padding:4px 8px; border-radius:5px; font-size:0.8rem;"><?php echo htmlspecialchars($s['categoria_nombre']); ?></span></td>
                    <td class="col-estado-val" data-estado="<?php echo ($s['estado'] == 1) ? 'activo' : 'inactivo'; ?>">
                        <label class="switch">
                            <input type="checkbox" class="toggle-svc" data-id="<?php echo $s['id_servicio']; ?>" <?php echo ($s['estado'] == 1) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                        <span class="status-label" style="font-size:0.65rem; font-weight:800; margin-left:8px; color:<?php echo ($s['estado'] == 1) ? '#28a745' : '#aaa'; ?>;">
                            <?php echo ($s['estado'] == 1) ? 'ACTIVO' : 'INACTIVO'; ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="eliminar_servicio.php?id=<?php echo $s['id_servicio']; ?>" class="btn-delete" onclick="return confirm('¿Eliminar permanentemente? Se borrará de la base de datos.')">
                            <i class="fas fa-trash-can"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function aplicarFiltros() {
    const texto = document.getElementById("busquedaTexto").value.toLowerCase();
    const estado = document.getElementById("busquedaEstado").value;
    const filas = document.querySelectorAll(".fila-svc");

    filas.forEach(f => {
        const nombre = f.querySelector(".col-nombre").innerText.toLowerCase();
        const estFila = f.querySelector(".col-estado-val").getAttribute("data-estado");
        f.style.display = (nombre.includes(texto) && (!estado || estFila === estado)) ? "" : "none";
    });
}

function limpiarFiltros() {
    document.getElementById("busquedaTexto").value = "";
    document.getElementById("busquedaEstado").value = "";
    aplicarFiltros();
}

document.querySelectorAll('.toggle-svc').forEach(item => {
    item.addEventListener('change', function() {
        const id = this.getAttribute('data-id');
        const estadoNum = this.checked ? 1 : 0;
        const contenedor = this.closest('.col-estado-val');
        const label = contenedor.querySelector('.status-label');

        contenedor.setAttribute('data-estado', this.checked ? 'activo' : 'inactivo');
        label.innerText = this.checked ? "ACTIVO" : "INACTIVO";
        label.style.color = this.checked ? "#28a745" : "#aaa";

        fetch('actualizar_estado_servicio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&estado=${estadoNum}`
        });
    });
});
</script>
</body>
</html>