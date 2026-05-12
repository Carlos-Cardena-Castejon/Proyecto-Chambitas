<?php
session_start();
include("../conexion.php");

// 1. Verificación de seguridad
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

// Definir nombre del admin para el menú lateral
$nombre_admin = $_SESSION['nombre'] ?? 'Administrador';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y limpiar datos
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $ap_paterno = $conexion->real_escape_string($_POST['apellido_paterno']);
    $ap_materno = $conexion->real_escape_string($_POST['apellido_materno']);
    $curp = $conexion->real_escape_string($_POST['curp']);
    $correo = $conexion->real_escape_string($_POST['correo']);
    $telefono = $conexion->real_escape_string($_POST['telefono']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $rol = $conexion->real_escape_string($_POST['rol']);
    $pass = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    
    // 2. Manejo de la Foto de Perfil
    $foto = "default.png"; 
    if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0){
        $ruta = "../uploads/"; 
        // Crear carpeta si no existe
        if (!file_exists($ruta)) { mkdir($ruta, 0777, true); }
        
        $extension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
        $nombre_foto = time() . "_" . bin2hex(random_bytes(4)) . "." . $extension;
        
        if(move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $ruta . $nombre_foto)){
            $foto = $nombre_foto;
        }
    }

    // 3. Verificar correo duplicado
    $check = $conexion->query("SELECT id_usuario FROM usuarios WHERE correo = '$correo'");
    if($check->num_rows > 0){
        $mensaje = "<div class='alert error'><i class='fas fa-info-circle'></i> El correo ya está registrado.</div>";
    } else {
        // 4. Insertar con todos los campos solicitados
        $sql = "INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, curp, correo, telefono, contrasena, descripcion, rol, foto_perfil, estado, fecha_registro) 
                VALUES ('$nombre', '$ap_paterno', '$ap_materno', '$curp', '$correo', '$telefono', '$pass', '$descripcion', '$rol', '$foto', 1, NOW())";
        
        if($conexion->query($sql)){
            header("Location: usuarios_admin.php?msg=creado");
            exit();
        } else {
            $mensaje = "<div class='alert error'>Error: " . $conexion->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario | Admin Chambitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary: #FFD700; 
            --dark: #121212; 
            --sidebar-width: 260px; 
        }
        
        body { font-family: 'Inter', sans-serif; background: #fdfdfd; margin: 0; display: flex; }

        /* --- SIDEBAR --- */
        .sidebar { width: var(--sidebar-width); height: 100vh; background: #121212; color: white; position: fixed; padding: 30px 20px; box-sizing: border-box; z-index: 1000; }
        .sidebar h2 { color: var(--primary); text-align: center; font-weight: 800; margin-bottom: 40px; letter-spacing: -1px; }
        .user-box { border: 1px solid #333; padding: 15px; border-radius: 12px; margin-bottom: 30px; }
        .user-box span { font-size: 0.7rem; color: #888; text-transform: uppercase; }
        .user-box p { margin: 5px 0 0; color: var(--primary); font-weight: 600; }
        .nav-menu a { display: flex; align-items: center; color: white; text-decoration: none; padding: 12px; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .nav-menu a i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-menu a:hover, .nav-menu a.active { background: var(--primary); color: black; font-weight: bold; }

        /* --- CONTENIDO --- */
        .main-content { margin-left: var(--sidebar-width); padding: 40px; width: 100%; box-sizing: border-box; min-height: 100vh; display: flex; flex-direction: column; align-items: center; }
        
        .form-container { width: 100%; max-width: 850px; background: white; padding: 40px; border-radius: 25px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .header-form { border-left: 6px solid var(--primary); padding-left: 20px; margin-bottom: 35px; }
        .header-form h1 { margin: 0; font-weight: 800; font-size: 1.8rem; color: var(--dark); }
        .header-form span { color: var(--primary); }
        
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .full { grid-column: span 3; }

        .group { margin-bottom: 15px; }
        label { display: block; font-size: 0.75rem; font-weight: 800; color: #555; margin-bottom: 8px; text-transform: uppercase; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #f4f4f4; border-radius: 12px; box-sizing: border-box; font-family: inherit; font-size: 0.9rem; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { border-color: var(--primary); outline: none; background: #fffdf5; border-color: #ffe033; }
        
        textarea { height: 100px; resize: none; }
        
        .btn-save { background: var(--dark); color: var(--primary); border: none; padding: 18px; width: 100%; border-radius: 15px; font-weight: 800; cursor: pointer; margin-top: 25px; font-size: 1rem; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; }
        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); background: #000; }
        
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; text-align: center; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>CHAMBITAS</h2>
    <div class="user-box"><span>Admin</span><p><?php echo htmlspecialchars($nombre_admin); ?></p></div>
    <nav class="nav-menu">
        <a href="usuarios_admin.php" class="active"><i class="fas fa-user-gear"></i> Volver a Usuarios</a>
    </nav>
</div>

<div class="main-content">
    <div class="form-container">
        <div class="header-form">
            <h1>Registrar Nuevo <span>Usuario</span></h1>
        </div>

        <?php echo $mensaje; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="group">
                    <label>Nombre(s)</label>
                    <input type="text" name="nombre" placeholder="Ej. Juan" required>
                </div>
                <div class="group">
                    <label>Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" placeholder="Ej. Pérez" required>
                </div>
                <div class="group">
                    <label>Apellido Materno</label>
                    <input type="text" name="apellido_materno" placeholder="Ej. García">
                </div>

                <div class="group">
                    <label>CURP</label>
                    <input type="text" name="curp" maxlength="18" style="text-transform:uppercase;" placeholder="18 caracteres">
                </div>
                <div class="group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" placeholder="10 dígitos">
                </div>

                <div class="group">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" placeholder="••••••••" required>
                </div>
                <div class="group">
                    <label>Rol en el Sistema</label>
                    <select name="rol">
                        <option value="usuario">Usuario Estándar</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="group">
                    <label>Foto de Perfil</label>
                    <input type="file" name="foto_perfil" style="border:none; padding:5px; font-size:0.8rem;">
                </div>

                
            </div>

            <button type="submit" class="btn-save">Finalizar Registro</button>
        </form>
    </div>
</div>

</body>
</html>