<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

$nombre_admin = $_SESSION['nombre'] ?? 'Administrador';

// CONSULTA: Traemos todos los usuarios activos
// Traemos a todos los que existan en la BD
// Cambia esto al principio de usuarios_admin.php
$resultado = $conexion->query("SELECT * FROM usuarios ORDER BY id_usuario DESC");?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios | Chambitas Admin</title>
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

        /* --- CONTENIDO --- */
        .main-content { margin-left: var(--sidebar-width); padding: 50px; width: calc(100% - var(--sidebar-width)); box-sizing: border-box; min-height: 100vh; }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .titulo-seccion { display: flex; align-items: center; gap: 15px; }
        .titulo-seccion .barrita { width: 6px; height: 40px; background: var(--primary); border-radius: 2px; }
        .titulo-seccion h1 { margin: 0; font-size: 2.2rem; font-weight: 900; }

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
            border: 1px solid #eee;
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

        /* TABLA */
        .table-container { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; }
        th { background: var(--dark); color: var(--primary); padding: 20px; text-align: left; font-size: 0.75rem; text-transform: uppercase; font-weight: 800; }
        td { padding: 18px 20px; border-bottom: 1px solid #f8f8f8; color: #444; }
        tr:hover td { background-color: #fffef0; }

        .btn-action { font-size: 1.1rem; margin: 0 8px; transition: 0.3s; cursor: pointer; text-decoration: none; }
        .btn-view { color: #3498db; }
        .btn-edit { color: #888; }
        .btn-delete { color: #ccc; }
        .btn-delete:hover { color: #ff4d4d; }

        /* --- MODAL FLOTANTE --- */
        .modal-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.8); backdrop-filter: blur(8px);
            display: none; align-items: center; justify-content: center; z-index: 2000; 
            padding: 40px; box-sizing: border-box;
        }
        .modal-card { 
            background: white; width: 100%; max-width: 500px; max-height: 85vh; border-radius: 30px; 
            position: relative; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            display: flex; flex-direction: column;
            animation: modalPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes modalPop { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        
        .modal-header { background: var(--dark); padding: 40px 20px; text-align: center; color: white; flex-shrink: 0; }
        .modal-header img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); background: #fff; margin-bottom: 15px; }
        
        .modal-body { padding: 30px 40px; overflow-y: auto; background: #fff; }
        .modal-body::-webkit-scrollbar { width: 6px; }
        .modal-body::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; margin-top: 20px; }
        .info-item label { display: block; font-size: 0.65rem; color: #aaa; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
        .info-item span { font-weight: 700; color: #333; font-size: 0.95rem; }
        
        .bio-box { background: #f9f9f9; padding: 20px; border-radius: 15px; margin-bottom: 20px; font-size: 0.9rem; color: #555; line-height: 1.5; border: 1px solid #eee; }
        .modal-close { position: absolute; top: 20px; right: 25px; font-size: 2rem; cursor: pointer; color: white; z-index: 10; }
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
                <h1>Panel de <span>Usuarios</span></h1>
            </div>
            <a href="agregar_usuario.php" style="background:#000; color:var(--primary); text-decoration:none; padding:12px 25px; border-radius:12px; font-weight:800;"><i class="fas fa-plus"></i> Nuevo Usuario</a>
        </div>

        <div class="barra-filtros">
            <div class="filter-group" style="flex: 2;">
                <label>Buscar usuario</label>
                <input type="text" id="busquedaTexto" placeholder="Nombre, correo o teléfono..." onkeyup="aplicarFiltros()">
            </div>
            <div class="filter-group">
                <label>Rol</label>
                <select id="busquedaRol" onchange="aplicarFiltros()">
                    <option value="">Todos</option>
                    <option value="usuario">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Fecha Registro</label>
                <input type="date" id="busquedaFecha" onchange="aplicarFiltros()">
            </div>
            <div class="filter-group" style="margin-left: auto;">
                <button onclick="limpiarFiltros()" style="background:none; border:none; color: #ff6666; cursor:pointer; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-top: 15px;">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>
        </div>

        <div class="table-container">
            <table id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>Perfil</th>
                        <th>Nombre / Rol</th>
                        <th>Contacto</th>
                        <th>CURP</th>
                        <th style="text-align: center;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = $resultado->fetch_assoc()): ?>
                    <tr class="fila-usuario">
                        <td>
                            <img src="<?php echo !empty($u['foto_perfil']) ? '../'.$u['foto_perfil'] : '../img/usuario.png'; ?>" style="width:45px; height:45px; border-radius:12px; object-fit:cover; border: 1px solid #eee;">
                        </td>
                        <td>
                            <strong class="col-nombre"><?php echo htmlspecialchars($u['nombre'] . " " . $u['apellido_paterno']); ?></strong><br>
                            <small class="col-rol" style="color:var(--primary); font-weight:700; background:#121212; padding:2px 6px; border-radius:4px; font-size:0.65rem;"><?php echo strtoupper($u['rol']); ?></small>
                        </td>
                        <td>
                            <span class="col-correo"><i class="far fa-envelope" style="width:15px"></i> <?php echo $u['correo']; ?></span><br>
                            <span class="col-tel"><i class="fas fa-phone-alt" style="width:15px; font-size:0.7rem"></i> <?php echo $u['telefono']; ?></span>
                        </td>
                        <td class="col-curp"><code><?php echo $u['curp'] ?: 'N/A'; ?></code></td>
                        <td style="display:none;" class="col-fecha"><?php echo date('Y-m-d', strtotime($u['fecha_registro'])); ?></td>
                        
                        <td style="text-align: center;">
                            <a class="btn-action btn-view" onclick='abrirModal(<?php echo json_encode($u); ?>)'>
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn-action btn-edit">
                                <i class="fas fa-pen-to-square"></i>
                            </a>
                            <a href="eliminar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn-action btn-delete" onclick="return confirm('¿Eliminar usuario?')">
                                <i class="fas fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="modalPerfil">
        <div class="modal-card">
            <span class="modal-close" onclick="cerrarModal()">&times;</span>
            <div class="modal-header">
                <img id="m-foto" src="" alt="Perfil">
                <h2 id="m-nombre" style="margin:0;">---</h2>
                <div id="m-rol" style="background:var(--primary); color:#000; padding:4px 12px; border-radius:20px; font-size:0.7rem; font-weight:800; display:inline-block; margin-top:10px;">ROL</div>
            </div>
            <div class="modal-body">
                <div class="bio-box" id="m-desc">Sin biografía registrada.</div>
                <div class="info-grid">
                    <div class="info-item"><label>ID</label><span id="m-id">#0</span></div>
                    <div class="info-item"><label>Teléfono</label><span id="m-tel">--</span></div>
                    <div class="info-item"><label>Correo</label><span id="m-correo">--</span></div>
                    <div class="info-item"><label>CURP</label><span id="m-curp">--</span></div>
                    <div class="info-item"><label>Registro</label><span id="m-fecha">--</span></div>
                    <div class="info-item"><label>Estado</label><span style="color:#2ecc71;">● Activo</span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function aplicarFiltros() {
        const texto = document.getElementById("busquedaTexto").value.toLowerCase();
        const rol = document.getElementById("busquedaRol").value.toLowerCase();
        const fecha = document.getElementById("busquedaFecha").value;
        const filas = document.querySelectorAll("#tablaUsuarios tbody tr");

        filas.forEach(f => {
            const nombre = f.querySelector(".col-nombre").innerText.toLowerCase();
            const correo = f.querySelector(".col-correo").innerText.toLowerCase();
            const tel = f.querySelector(".col-tel").innerText.toLowerCase();
            const rolFila = f.querySelector(".col-rol").innerText.toLowerCase();
            const fechaFila = f.querySelector(".col-fecha").innerText;

            const matchTexto = nombre.includes(texto) || correo.includes(texto) || tel.includes(texto);
            const matchRol = !rol || rolFila === rol; // Búsqueda exacta para evitar que "admin" incluya "usuario"
            const matchFecha = !fecha || fechaFila === fecha;

            f.style.display = (matchTexto && matchRol && matchFecha) ? "" : "none";
        });
    }

    function limpiarFiltros() {
        document.getElementById("busquedaTexto").value = "";
        document.getElementById("busquedaRol").value = "";
        document.getElementById("busquedaFecha").value = "";
        aplicarFiltros();
    }

    function abrirModal(u) {
        document.getElementById("m-foto").src = u.foto_perfil ? "../" + u.foto_perfil : "../img/usuario.png";
        document.getElementById("m-nombre").innerText = u.nombre + " " + u.apellido_paterno;
        document.getElementById("m-rol").innerText = u.rol.toUpperCase();
        document.getElementById("m-desc").innerText = u.descripcion || "Sin biografía.";
        document.getElementById("m-id").innerText = "#" + u.id_usuario;
        document.getElementById("m-tel").innerText = u.telefono || "N/A";
        document.getElementById("m-correo").innerText = u.correo;
        document.getElementById("m-curp").innerText = u.curp || "N/A";
        document.getElementById("m-fecha").innerText = u.fecha_registro;
        document.getElementById("modalPerfil").style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    function cerrarModal() {
        document.getElementById("modalPerfil").style.display = "none";
        document.body.style.overflow = "auto";
    }

    window.onclick = function(e) { if (e.target == document.getElementById("modalPerfil")) cerrarModal(); }
    </script>
</body>
</html>