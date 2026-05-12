<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

$nombre_admin = $_SESSION['nombre'] ?? 'Administrador';

// Consulta SQL
$sql = "SELECT p.*, 
               u.nombre, u.correo, u.telefono, u.foto_perfil, u.apellido_paterno
        FROM publicaciones p 
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario 
        WHERE p.estado != 'Eliminada' 
        ORDER BY p.fecha_publicacion DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicaciones | Chambitas Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
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

        /* --- CONTENIDO PRINCIPAL --- */
        .main { margin-left: var(--sidebar-width); padding: 50px; width: calc(100% - var(--sidebar-width)); box-sizing: border-box; min-height: 100vh; }
        
        .titulo-contenedor { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
        .titulo-contenedor .linea-amarilla { width: 6px; height: 40px; background: var(--primary); border-radius: 2px; }
        .titulo-contenedor h1 { margin: 0; font-size: 2.2rem; font-weight: 800; }

        /* --- BARRA DE FILTROS --- */
        .barra-filtros { 
            background: #fff; 
            padding: 20px; 
            border-radius: 15px; 
            margin-bottom: 25px; 
            display: flex; 
            gap: 15px; 
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 0.65rem; font-weight: 800; color: #999; text-transform: uppercase; margin-left: 5px; }
        .barra-filtros input, .barra-filtros select { 
            padding: 10px 15px; 
            border: 1px solid #ddd; 
            border-radius: 10px; 
            font-family: 'Inter'; 
            outline: none;
            font-size: 0.9rem;
        }
        .barra-filtros input:focus { border-color: var(--primary); }

        /* TABLA */
        .table-container { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--dark); }
        th { padding: 20px; text-align: left; font-size: 0.75rem; color: var(--primary); text-transform: uppercase; }
        td { padding: 20px; border-bottom: 1px solid #f0f0f0; }

        .user-tag { background: #f0f0f0; padding: 6px 12px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; transition: 0.2s; }
        .user-tag:hover { background: #000; color: #fff; }
        .user-tag img { width: 25px; height: 25px; border-radius: 50%; object-fit: cover; }

        /* --- MODAL FLOTANTE --- */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 2000; 
            left: 0; top: 0; 
            width: 100%; height: 100%; 
            background: rgba(0,0,0,0.75); 
            backdrop-filter: blur(8px); 
            align-items: center; 
            justify-content: center; 
            padding: 40px; 
            box-sizing: border-box;
        }

        .modal-content { 
            background: white; 
            width: 100%;
            max-width: 750px; 
            max-height: 85vh; 
            border-radius: 30px; 
            position: relative; 
            overflow: hidden; 
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalPop {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .modal-header { background: var(--dark); padding: 40px; text-align: center; color: white; flex-shrink: 0; }
        .modal-header img { width: 100px; height: 100px; border-radius: 50%; border: 4px solid var(--primary); object-fit: cover; background: #fff; margin-bottom: 15px; }

        .modal-body { padding: 40px 50px; overflow-y: auto; background: #fff; }
        .modal-body::-webkit-scrollbar { width: 8px; }
        .modal-body::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 30px; }
        .info-item label { display: block; font-size: 0.7rem; color: #aaa; font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
        .info-item p { margin: 0; font-weight: 700; color: #222; font-size: 1rem; }

        .desc-box { background: #f8f9fa; padding: 25px; border-radius: 20px; border: 1px solid #eee; line-height: 1.6; color: #444; }
        .pub-img { width: 100%; height: auto; border-radius: 20px; margin-top: 20px; border: 1px solid #ddd; }

        .close { position: absolute; right: 25px; top: 20px; font-size: 30px; color: white; cursor: pointer; z-index: 10; transition: 0.2s; }
        .close:hover { color: var(--primary); transform: rotate(90deg); }

        .btn-delete { color: #ccc; transition: 0.3s; font-size: 1.2rem; cursor: pointer; border: none; background: none; }
        .btn-delete:hover { color: #ff4d4d; }
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


<div class="main">
    <div class="titulo-contenedor">
        <div class="linea-amarilla"></div>
        <h1>Gestionar <span>Publicaciones</span></h1>
    </div>

    <div class="barra-filtros">
        <div class="filter-group">
            <label>Buscar</label>
            <input type="text" id="busquedaTexto" placeholder="Título o usuario..." onkeyup="aplicarFiltros()">
        </div>
        <div class="filter-group">
            <label>Fecha</label>
            <input type="date" id="busquedaFecha" onchange="aplicarFiltros()">
        </div>
        <div class="filter-group">
            <label>Estado</label>
            <select id="busquedaEstado" onchange="aplicarFiltros()">
                <option value="">Todos</option>
                <option value="Activa">Activa</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Finalizada">Finalizada</option>
            </select>
        </div>
        <div class="filter-group" style="margin-left: auto;">
            <button onclick="limpiarFiltros()" style="background:none; border:none; color: #ff6666; cursor:pointer; font-size: 0.8rem; font-weight: 800; text-transform: uppercase;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </div>

    <div class="table-container">
        <table id="tablaPublicaciones">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título del Servicio</th>
                    <th>Publicado por</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Presupuesto</th>
                    <th style="text-align: center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $resultado->fetch_assoc()): ?>
                <tr class="fila">
                    <td style="color:#bbb;">#<?php echo $p['id_publicacion']; ?></td>
                    <td class="col-titulo" style="font-weight: 800;"><?php echo htmlspecialchars($p['titulo']); ?></td>
                    <td>
                        <div class="user-tag" onclick='abrirModal(<?php echo json_encode($p); ?>)'>
                            <img src="<?php echo !empty($p['foto_perfil']) ? '../'.$p['foto_perfil'] : '../img/usuario.png'; ?>">
                            <span class="col-nombre"><?php echo htmlspecialchars($p['nombre']); ?></span>
                        </div>
                    </td>
                    <td class="col-fecha"><?php echo date('Y-m-d', strtotime($p['fecha_publicacion'])); ?></td>
                    <td class="col-estado">
                        <span style="font-weight: 700; font-size: 0.85rem;"><?php echo $p['estado']; ?></span>
                    </td>
                    <td style="color:#2ecc71; font-weight:800;">$<?php echo number_format($p['presupuesto'], 2); ?></td>
                    <td style="text-align: center;">
                        <a href="eliminar_publicacion.php?id=<?php echo $p['id_publicacion']; ?>" class="btn-delete" onclick="return confirm('¿Eliminar?')">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalU" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <div class="modal-header">
            <img id="m-foto-user" src="" alt="User">
            <h2 id="m-nombre" style="margin:0;"></h2>
            <p id="m-correo" style="margin:5px 0 0; color:var(--primary); font-weight:600;"></p>
        </div>
        <div class="modal-body">
            <div class="info-grid">
                <div class="info-item"><label>ID Pub.</label><p id="m-id-pub"></p></div>
                <div class="info-item"><label>Estado</label><p id="m-estado" style="color: #2ecc71;"></p></div>
                <div class="info-item"><label>ID Usuario</label><p id="m-id-user"></p></div>
                <div class="info-item"><label>Teléfono</label><p id="m-tel"></p></div>
                <div class="info-item" style="grid-column: span 2;"><label>Ubicación (Colonia)</label><p id="m-colonia"></p></div>
            </div>
            <div style="font-size: 0.7rem; font-weight: 800; color: #bbb; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px;">Descripción</div>
            <div class="desc-box"><p id="m-desc" style="margin: 0;"></p></div>
            <div id="cont-img" style="margin-top: 30px; display: none;">
                <div style="font-size: 0.7rem; font-weight: 800; color: #bbb; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px;">Imagen</div>
                <img id="m-img-pub" class="pub-img" src="">
            </div>
            <div style="height: 30px;"></div>
        </div>
    </div>
</div>

<script>
function aplicarFiltros() {
    const texto = document.getElementById("busquedaTexto").value.toLowerCase();
    const fecha = document.getElementById("busquedaFecha").value;
    const estado = document.getElementById("busquedaEstado").value.toLowerCase();
    const filas = document.querySelectorAll("#tablaPublicaciones tbody .fila");

    filas.forEach(fila => {
        const titulo = fila.querySelector(".col-titulo").innerText.toLowerCase();
        const nombre = fila.querySelector(".col-nombre").innerText.toLowerCase();
        const fFila = fila.querySelector(".col-fecha").innerText;
        const eFila = fila.querySelector(".col-estado").innerText.toLowerCase();

        const matchTexto = titulo.includes(texto) || nombre.includes(texto);
        const matchFecha = !fecha || fFila === fecha;
        const matchEstado = !estado || eFila.includes(estado);

        fila.style.display = (matchTexto && matchFecha && matchEstado) ? "" : "none";
    });
}

function limpiarFiltros() {
    document.getElementById("busquedaTexto").value = "";
    document.getElementById("busquedaFecha").value = "";
    document.getElementById("busquedaEstado").value = "";
    aplicarFiltros();
}

function abrirModal(data) {
    document.getElementById("m-nombre").innerText = data.nombre + " " + (data.apellido_paterno || "");
    document.getElementById("m-correo").innerText = data.correo;
    document.getElementById("m-tel").innerText = data.telefono || 'Sin teléfono';
    document.getElementById("m-foto-user").src = data.foto_perfil ? '../' + data.foto_perfil : '../img/usuario.png';
    document.getElementById("m-id-pub").innerText = "#" + data.id_publicacion;
    document.getElementById("m-id-user").innerText = data.id_usuario;
    document.getElementById("m-estado").innerText = data.estado;
    document.getElementById("m-colonia").innerText = data.colonia || 'No especificada';
    document.getElementById("m-desc").innerText = data.descripcion;

    let imgPub = document.getElementById("m-img-pub");
    let contImg = document.getElementById("cont-img");
    if(data.imagen) {
        imgPub.src = '../' + data.imagen;
        contImg.style.display = "block";
    } else {
        contImg.style.display = "none";
    }

    document.getElementById("modalU").style.display = "flex";
    document.body.style.overflow = "hidden"; 
}

function cerrarModal() {
    document.getElementById("modalU").style.display = "none";
    document.body.style.overflow = "auto";
}

window.onclick = function(e) {
    if (e.target == document.getElementById("modalU")) cerrarModal();
}
</script>

</body>
</html>