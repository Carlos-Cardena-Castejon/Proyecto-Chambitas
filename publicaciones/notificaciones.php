<?php
session_start();
include("../conexion.php"); 

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php"); 
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// 1. Marcar todas como leídas al entrar
$update = $conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE id_usuario = ?");
$update->bind_param("i", $id_usuario);
$update->execute();

// 2. Obtener las notificaciones
$sql = "SELECT * FROM notificaciones WHERE id_usuario = ? ORDER BY fecha_registro DESC LIMIT 20";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Notificaciones | Chambitas</title>
        <link rel="icon" type="image/png" href="../img/favicon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background: #f0f2f5; font-family: 'Inter', sans-serif; margin: 0; }
        .notif-container { max-width: 600px; margin: 40px auto; padding: 20px; }
        
        .notif-card { 
            background: white; border-radius: 16px; padding: 18px; 
            margin-bottom: 12px; display: flex; gap: 15px; align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); transition: 0.3s;
            text-decoration: none; color: inherit;
            border-left: 6px solid #ccc;
            position: relative; /* Para posicionar la X */
        }
        
        /* COLORES POR TIPO */
        .notif-card.edicion { border-left-color: #ffa500; }
        .notif-card.justicia { border-left-color: #2ecc71; }
        .notif-card.sancion { border-left-color: #ff4b4b; }

        .notif-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }

        .notif-icon { 
            width: 50px; height: 50px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.3rem; flex-shrink: 0; 
        }

        .icon-edicion { background: #fff8e1; color: #ffa500; }
        .icon-justicia { background: #e8f5e9; color: #2ecc71; }
        .icon-sancion { background: #ffebee; color: #ff4b4b; }

        .notif-content { flex-grow: 1; padding-right: 25px; } /* Espacio para la X */
        .notif-content p { margin: 0; font-size: 0.95rem; color: #333; line-height: 1.5; font-weight: 500; }
        .notif-time { font-size: 0.75rem; color: #999; margin-top: 6px; display: block; }
        
        /* BOTÓN ELIMINAR (X) */
        .btn-delete-noti {
            position: absolute; top: 15px; right: 15px;
            color: #ccc; background: transparent; border: none;
            cursor: pointer; font-size: 1rem; transition: 0.2s;
            padding: 5px;
        }
        .btn-delete-noti:hover { color: #ff4b4b; transform: scale(1.2); }

        .empty-state { text-align: center; padding: 60px 20px; color: #999; }
        
        /* Animación de salida */
        .fade-out { opacity: 0; transform: translateX(20px); transition: 0.4s; }
    </style>
</head>
<body>

<div class="notif-container">
    <h1 style="color: #1a1a1a; margin-bottom: 30px;"><i class="fas fa-bell"></i> Notificaciones</h1>

    <?php if($resultado->num_rows > 0): ?>
        <?php while($n = $resultado->fetch_assoc()): 
            $link = $n['link'] ?: '#';
            if(strpos($link, 'publicaciones/') === 0) {
                $link = str_replace('publicaciones/', '', $link);
            }

            $tipo = $n['tipo'];
            $icono = 'fa-info-circle';
            $clase_icon = 'icon-default';

            if($tipo == 'edicion'){ $icono = 'fa-tools'; $clase_icon = 'icon-edicion'; }
            elseif($tipo == 'justicia'){ $icono = 'fa-shield-alt'; $clase_icon = 'icon-justicia'; }
            elseif($tipo == 'sancion'){ $icono = 'fa-gavel'; $clase_icon = 'icon-sancion'; }
        ?>
            <div class="notif-card <?php echo $tipo; ?>" id="noti-<?php echo $n['id_notificacion']; ?>">
                <a href="<?php echo htmlspecialchars($link); ?>" style="display: flex; align-items: center; text-decoration: none; color: inherit; width: 100%;">
                    <div class="notif-icon <?php echo $clase_icon; ?>">
                        <i class="fas <?php echo $icono; ?>"></i>
                    </div>
                    <div class="notif-content">
                        <p><?php echo htmlspecialchars($n['mensaje']); ?></p>
                        <span class="notif-time"><i class="far fa-clock"></i> <?php echo date('d M, h:i A', strtotime($n['fecha_registro'])); ?></span>
                    </div>
                </a>
                <button class="btn-delete-noti" onclick="eliminarNoti(<?php echo $n['id_notificacion']; ?>)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
            <p>No tienes notificaciones por el momento.</p>
        </div>
    <?php endif; ?>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="../feed.php" style="color: #888; text-decoration: none; font-size: 0.9rem; font-weight: 600;">
            <i class="fas fa-chevron-left"></i> Volver al tablero
        </a>
    </div>
</div>

<script>
function eliminarNoti(id) {
    // Usamos SwetAlert por si acaso se arrepienten, pero rápido
    Swal.fire({
        title: '¿Borrar aviso?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ff4b4b',
        cancelButtonColor: '#ccc',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mandamos la orden al servidor
            fetch('eliminar_noti.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'ok') {
                    const elemento = document.getElementById('noti-' + id);
                    elemento.classList.add('fade-out'); // Efecto visual
                    setTimeout(() => elemento.remove(), 400);
                } else {
                    Swal.fire('Error', 'No se pudo eliminar', 'error');
                }
            });
        }
    })
}
</script>

</body>
</html>