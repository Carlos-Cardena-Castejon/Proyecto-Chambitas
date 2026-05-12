<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$mensaje = "";

// 1. OBTENER DATOS ACTUALES
$sql_user = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($sql_user);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

// 2. LÓGICA DE ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_cambios'])) {
    
    $nombre = $_POST['nombre'];
    $apellido_p = $_POST['apellido_paterno'];
    $apellido_m = $_POST['apellido_materno'];
    $telefono = $_POST['telefono'];
    $descripcion = $_POST['descripcion'];
    $rutaBD = $usuario['foto_perfil']; 

    // --- MANEJO DE FOTO (Límite 6MB) ---
    if(isset($_FILES['foto']) && !empty($_FILES['foto']['tmp_name'])){
        $archivo = $_FILES['foto'];
        $carpetaFisica = __DIR__ . "/uploads/perfiles/";
        
        if (!is_dir($carpetaFisica)) { mkdir($carpetaFisica, 0777, true); }

        // Validación de 6MB (6 * 1024 * 1024)
        if($archivo['size'] <= 6 * 1024 * 1024){
            $tipoReal = mime_content_type($archivo['tmp_name']);
            $permitidos = ['image/jpeg','image/png','image/webp'];

            if(in_array($tipoReal, $permitidos)){
                $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                $nombreArchivo = uniqid("chambitas_") . "." . $extension;
                
                if(move_uploaded_file($archivo['tmp_name'], $carpetaFisica . $nombreArchivo)){
                    // Borrar anterior si existe y no es la default
                    if(!empty($usuario['foto_perfil']) && strpos($usuario['foto_perfil'], 'usuario.png') === false){
                        $rutaAnterior = __DIR__ . "/" . $usuario['foto_perfil'];
                        if(file_exists($rutaAnterior)) { unlink($rutaAnterior); }
                    }
                    $rutaBD = "uploads/perfiles/" . $nombreArchivo;
                }
            } else {
                $mensaje = "<div style='color:red; margin-bottom:15px;'>Formato de imagen no válido.</div>";
            }
        } else {
            $mensaje = "<div style='color:red; margin-bottom:15px;'>La imagen excede los 6MB permitidos.</div>";
        }
    }

    // 3. UPDATE (CURP y Correo no se tocan)
    if(empty($mensaje)){
        $sql_up = "UPDATE usuarios SET nombre=?, apellido_paterno=?, apellido_materno=?, telefono=?, descripcion=?, foto_perfil=? WHERE id_usuario=?";
        $stmt_up = $conexion->prepare($sql_up);
        $stmt_up->bind_param("ssssssi", $nombre, $apellido_p, $apellido_m, $telefono, $descripcion, $rutaBD, $id_usuario);
        
        if ($stmt_up->execute()) {
            echo "<script>window.location.href='perfil.php?status=updated';</script>";
            exit();
        }
    }
}

// 4. PREPARAR VISTA DE IMAGEN
$fotoMostrar = "img/usuario.png";
if (!empty($usuario['foto_perfil']) && file_exists(__DIR__ . "/" . $usuario['foto_perfil'])) {
    $fotoMostrar = $usuario['foto_perfil'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración | NEXTIC</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .readonly-input { background-color: #f5f5f5 !important; color: #999 !important; cursor: not-allowed; border: 1px solid #ddd !important; }
        .avatar-section { display: flex; flex-direction: column; align-items: center; margin-bottom: 30px; background: #fafafa; padding: 20px; border-radius: 15px; }
        .char-counter { font-size: 11px; color: #888; text-align: right; display: block; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<main class="main-container">
    <div class="content-box" style="grid-column: 1 / -1;">
        <h2 style="margin-bottom: 20px;"><i class="fas fa-user-cog"></i> Editar Perfil Profesional</h2>
        
        <?php echo $mensaje; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="avatar-section">
                <div class="avatar-wrapper" style="margin-bottom: 15px;">
                    <img src="<?php echo $fotoMostrar; ?>" id="imgPreview" style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 3px solid #f1c40f;">
                </div>
                <label for="foto" class="btn-action" style="cursor:pointer; background:#000; color:#fff; font-size: 13px; padding: 8px 15px;">
                    <i class="fas fa-camera"></i> Seleccionar Imagen
                </label>
                <input type="file" name="foto" id="foto" accept="image/*" style="display:none;" onchange="preview(event)">
            </div>

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="field">
                    <label>Nombre(s)</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                </div>
                <div class="field">
                    <label>Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" value="<?php echo htmlspecialchars($usuario['apellido_paterno']); ?>" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                </div>
                <div class="field">
                    <label>Apellido Materno</label>
                    <input type="text" name="apellido_materno" value="<?php echo htmlspecialchars($usuario['apellido_materno']); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                </div>
                <div class="field">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['telefono']); ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
                </div>
                
                <div class="field">
                    <label>CURP (No modificable)</label>
                    <input type="text" class="readonly-input" value="<?php echo htmlspecialchars($usuario['curp']); ?>" readonly style="width:100%; padding:10px; border-radius:8px;">
                </div>
                <div class="field">
                    <label>Correo Electrónico (No modificable)</label>
                    <input type="text" class="readonly-input" value="<?php echo htmlspecialchars($usuario['correo']); ?>" readonly style="width:100%; padding:10px; border-radius:8px;">
                </div>

                <div class="field" style="grid-column: 1 / -1;">
                    <label>Acerca de mí</label>
                    <textarea name="descripcion" id="desc" rows="5" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; font-family:inherit;"><?php echo htmlspecialchars($usuario['descripcion']); ?></textarea>
                    <span class="char-counter"><span id="count">0</span>/500 caracteres</span>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" name="guardar_cambios" class="btn-action" style="background:#f1c40f; color:#000; border:none; font-weight:700; padding:12px 25px; cursor:pointer; border-radius:8px;">
                    GUARDAR CAMBIOS
                </button>
                <a href="perfil.php" style="text-decoration:none; color:#666; display:flex; align-items:center; font-weight:600;">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<script>
// Previsualización de imagen
function preview(e) {
    const reader = new FileReader();
    reader.onload = () => document.getElementById('imgPreview').src = reader.result;
    reader.readAsDataURL(e.target.files[0]);
}

// Contador de descripción
const area = document.getElementById('desc');
const count = document.getElementById('count');
area.addEventListener('input', () => {
    count.textContent = area.value.length;
    if(area.value.length > 500) count.style.color = 'red';
    else count.style.color = '#888';
});
// Inicializar contador
count.textContent = area.value.length;
</script>
</body>
</html>