<?php
session_start();
include("conexion.php");

if(!isset($_GET['id_usuario'])){
    die("Usuario no encontrado");
}

$id_usuario = (int) $_GET['id_usuario'];

// Consulta mejorada para traer también la foto y el ID del calificador
$sql = "
SELECT 
    c.puntuacion, 
    c.comentario, 
    u.id_usuario AS id_autor,
    u.nombre, 
    u.apellido_paterno, 
    u.foto_perfil
FROM calificaciones c
JOIN usuarios u ON c.id_calificador = u.id_usuario
WHERE c.id_calificado = ?
ORDER BY c.id_calificacion DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas de Clientes | Chambitas</title>
        <link rel="icon" type="image/png" href="img/favicon.png">

    <link rel="stylesheet" href="css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container-reviews {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .review-card {
            display: flex;
            gap: 20px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .review-card:hover {
            transform: translateY(-3px);
        }

        .reviewer-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #f1c40f;
        }

        .review-content {
            flex: 1;
        }

        .reviewer-name {
            font-weight: 700;
            color: #111;
            text-decoration: none;
            font-size: 16px;
        }

        .reviewer-name:hover {
            color: #f1c40f;
        }

        .stars-box {
            color: #f1c40f;
            font-size: 12px;
            margin: 5px 0;
        }

        .comment-text {
            color: #555;
            line-height: 1.5;
            font-size: 14px;
            margin-top: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #888;
        }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container-reviews">
    <div class="header-section">
        <a href="perfil.php" style="color: #000; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin:0;">Reseñas de Clientes</h2>
    </div>

    <?php if($reviews->num_rows > 0): ?>
        <?php while($rev = $reviews->fetch_assoc()): 
            // Lógica de foto default
            $fotoAutor = "img/usuario.png";
            if (!empty($rev['foto_perfil']) && file_exists($rev['foto_perfil'])) {
                $fotoAutor = $rev['foto_perfil'];
            }
        ?>
            <div class="review-card">
                <a href="publicaciones/perfil_publico.php?id=<?php echo $rev['id_autor']; ?>">
                    <img src="<?php echo $fotoAutor; ?>" class="reviewer-photo" alt="Autor">
                </a>
                
                <div class="review-content">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <a href="publicaciones/perfil_publico.php?id=<?php echo $rev['id_autor']; ?>" class="reviewer-name">
                            <?php echo htmlspecialchars($rev['nombre']." ".$rev['apellido_paterno']); ?>
                        </a>
                    </div>

                    <div class="stars-box">
                        <?php
                        for($i=1; $i<=5; $i++){
                            echo ($i <= $rev['puntuacion']) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>

                    <p class="comment-text">
                        <i class="fas fa-quote-left" style="font-size: 10px; color: #ccc; margin-right: 5px;"></i>
                        <?php echo htmlspecialchars($rev['comentario']); ?>
                    </p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-comment-slash" style="font-size: 40px; margin-bottom: 15px;"></i>
            <p>Este profesional aún no ha recibido valoraciones.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>