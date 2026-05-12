<?php
session_start();
include("../conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

// Obtener categorías para el select
$query_cat = "SELECT * FROM categorias";
$res_cat = $conexion->query($query_cat);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $id_cat = $_POST['id_categoria'];
    $desc = $_POST['descripcion'];

    $stmt = $conexion->prepare("INSERT INTO servicios (id_categoria, nombre, descripcion, estado) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("iss", $id_cat, $nombre, $desc);

    if ($stmt->execute()) {
        header("Location: servicios.php?msg=creado");
    } else {
        $error = "Error al guardar el servicio.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Servicio | Chambitas Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #FFD700; --dark: #121212; }
        body { font-family: 'Inter', sans-serif; background: #fff; margin: 0; display: flex; }
        
        /* Sidebar fijo (mismo estilo) */
        .sidebar { width: 260px; height: 100vh; background: #121212; color: white; position: fixed; padding: 30px 20px; box-sizing: border-box; }
        .main-content { margin-left: 260px; padding: 50px; width: calc(100% - 260px); box-sizing: border-box; }

        .titulo-seccion { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .titulo-seccion .barrita { width: 6px; height: 40px; background: var(--primary); border-radius: 2px; }
        .titulo-seccion h1 { margin: 0; font-size: 2.2rem; font-weight: 900; }

        /* Formulario Estilizado */
        .form-card { background: #f9f9f9; padding: 30px; border-radius: 15px; border: 1px solid #eee; max-width: 600px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 800; margin-bottom: 8px; font-size: 0.9rem; text-transform: uppercase; }
        input, select, textarea { 
            width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 10px; 
            font-family: 'Inter'; outline: none; transition: 0.3s; box-sizing: border-box;
        }
        input:focus, select:focus { border-color: var(--primary); }

        .btn-save { 
            background: #000; color: var(--primary); border: none; padding: 15px 30px; 
            border-radius: 10px; font-weight: 800; cursor: pointer; transition: 0.3s; width: 100%;
        }
        .btn-save:hover { background: var(--primary); color: #000; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #888; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>


<div class="main-content">
    <a href="servicios.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver al listado</a>
    
    <div class="titulo-seccion">
        <div class="barrita"></div>
        <h1>Nuevo <span>Servicio</span></h1>
    </div>

    <div class="form-card">
        <form action="" method="POST">
            <div class="form-group">
                <label>Nombre del Servicio</label>
                <input type="text" name="nombre" placeholder="Ej. Pintor de Casas" required>
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <select name="id_categoria" required>
                    <option value="">Selecciona una categoría...</option>
                    <?php while($c = $res_cat->fetch_assoc()): ?>
                        <option value="<?php echo $c['id_categoria']; ?>">
                            <?php echo htmlspecialchars($c['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Descripción (Opcional)</label>
                <textarea name="descripcion" rows="3" placeholder="Breve descripción del servicio..."></textarea>
            </div>

            <button type="submit" class="btn-save">
                <i class="fas fa-check"></i> Crear Servicio
            </button>
        </form>
    </div>
</div>

</body>
</html>