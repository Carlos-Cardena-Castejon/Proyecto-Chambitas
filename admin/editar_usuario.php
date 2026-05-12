<?php
session_start();
include("../conexion.php");

// 1. Verificación de sesión
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

// 2. Obtener datos del administrador logueado (para el sidebar)
$id_admin = $_SESSION['id_usuario'];
$query_admin = "SELECT nombre FROM usuarios WHERE id_usuario = ?";
$stmt_a = $conexion->prepare($query_admin);
$stmt_a->bind_param("i", $id_admin);
$stmt_a->execute();
$res_admin = $stmt_a->get_result()->fetch_assoc();
$nombre_admin = $res_admin['nombre'] ?? 'Administrador';

// 3. Validar ID del usuario a editar
if(!isset($_GET['id'])) {
    header("Location: usuarios_admin.php");
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();

if(!$u) { 
    die("Usuario no encontrado"); 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary: #FFD700; 
            --dark: #121212; 
            --sidebar-width: 260px; 
        }
        
        body { font-family: 'Inter', sans-serif; background: #f4f4f4; margin: 0; display: flex; }

        /* --- SIDEBAR --- */
        .sidebar { width: var(--sidebar-width); height: 100vh; background: #121212; color: white; position: fixed; padding: 30px 20px; box-sizing: border-box; z-index: 1000; }
        .sidebar h2 { color: var(--primary); text-align: center; font-weight: 800; margin-bottom: 40px; letter-spacing: -1px; }
        .user-box { border: 1px solid #333; padding: 15px; border-radius: 12px; margin-bottom: 30px; }
        .user-box span { font-size: 0.7rem; color: #888; text-transform: uppercase; }
        .user-box p { margin: 5px 0 0; color: var(--primary); font-weight: 600; }
        
        .nav-menu a { display: flex; align-items: center; color: white; text-decoration: none; padding: 12px; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .nav-menu a i { margin-right: 15px; width: 20px; text-align: center; }
        .nav-menu a:hover, .nav-menu a.active { background: var(--primary); color: black; font-weight: bold; }

        /* --- CONTENIDO PRINCIPAL --- */
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 40px; display: flex; justify-content: center; }

        .form-container {
            width: 100%; max-width: 800px; background: #fff;
            padding: 40px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        h2.title { font-weight: 800; border-left: 5px solid var(--primary); padding-left: 15px; margin-bottom: 30px; color: var(--dark); }
        h2.title span { color: #999; font-weight: 400; font-size: 1.2rem; }
        
        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .full-width { grid-column: span 2; }

        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 0.75rem; font-weight: 800; color: #999; text-transform: uppercase; margin-bottom: 5px; }
        
        input, select, textarea {
            width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px;
            font-size: 14px; box-sizing: border-box; transition: 0.3s; font-family: inherit;
        }

        input:focus, textarea:focus { border-color: var(--primary); outline: none; background: #fffdf0; }

        .foto-edit { text-align: center; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 15px; }
        .foto-edit img { 
            width: 120px; height: 120px; border-radius: 50%; 
            object-fit: cover; border: 4px solid var(--primary); margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-save { 
            background: var(--dark); color: var(--primary); border: none;
            padding: 15px 40px; border-radius: 12px; font-weight: 800;
            cursor: pointer; width: 100%; margin-top: 20px; transition: 0.3s;
            font-size: 1rem;
        }
        .btn-save:hover { background: #000; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        
        .btn-cancel { 
            display: block; text-align: center; text-decoration: none;
            color: #999; font-size: 0.9rem; margin-top: 15px; transition: 0.2s;
        }
        .btn-cancel:hover { color: #666; }
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

<main class="main-content">
    <div class="form-container">
        <h2 class="title">Editar Usuario <span>#<?php echo $u['id_usuario']; ?></span></h2>
        
        <form action="actualizar_usuario_proceso.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">

            <div class="foto-edit">
                <img src="../<?php echo !empty($u['foto_perfil']) ? htmlspecialchars($u['foto_perfil']) : 'img/usuario.png'; ?>" id="preview">
                <div class="form-group">
                    <label>Cambiar Fotografía</label>
                    <input type="file" name="foto" accept="image/*" onchange="previewImage(event)">
                </div>
            </div>

            <div class="grid-form">
                <div class="form-group">
                    <label>Nombre(s)</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($u['nombre']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="<?php echo htmlspecialchars($u['apellido_paterno']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?php echo htmlspecialchars($u['apellido_materno']); ?>">
                </div>

                <div class="form-group">
                    <label>CURP</label>
                    <input type="text" name="curp" value="<?php echo htmlspecialchars($u['curp']); ?>" maxlength="18" style="text-transform: uppercase;">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" name="correo" value="<?php echo htmlspecialchars($u['correo']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($u['telefono']); ?>">
                </div>

                <div class="form-group">
                    <label>Rol de Usuario</label>
                    <select name="rol">
                        <option value="usuario" <?php echo ($u['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario Estándar</option>
                        <option value="admin" <?php echo ($u['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estado de Cuenta</label>
                    <select name="estado">
                        <option value="1" <?php echo ($u['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                        <option value="0" <?php echo ($u['estado'] == 0) ? 'selected' : ''; ?>>Inactivo / Suspendido</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Descripción / Biografía</label>
                    <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($u['descripcion']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" name="nueva_contrasena" placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn-save">GUARDAR CAMBIOS TOTALES</button>
            <a href="usuarios_admin.php" class="btn-cancel">Cancelar y salir</a>
        </form>
    </div>
</main>

<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('preview');
        output.src = reader.result;
    }
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

</body>
</html>