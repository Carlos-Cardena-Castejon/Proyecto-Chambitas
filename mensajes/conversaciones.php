<?php
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_usuario = $_SESSION['id_usuario'];

/* =============================================
   CONSULTA DE CONVERSACIONES
   ============================================= */
$sql = "
SELECT 
    p.id_publicacion,
    p.titulo,
    u.id_usuario,
    u.nombre,
    m.mensaje,
    m.fecha_envio,
    m.id_usuario_envia,
    (
        SELECT COUNT(*) 
        FROM mensajes 
        WHERE id_usuario_recibe = $id_usuario 
        AND id_usuario_envia = u.id_usuario
        AND id_publicacion = p.id_publicacion
        AND leido = 0
    ) AS no_leidos
FROM mensajes m
INNER JOIN publicaciones p ON m.id_publicacion = p.id_publicacion
INNER JOIN usuarios u ON (
    (m.id_usuario_envia = $id_usuario AND u.id_usuario = m.id_usuario_recibe)
    OR
    (m.id_usuario_recibe = $id_usuario AND u.id_usuario = m.id_usuario_envia)
)
WHERE m.id_mensaje IN (
    SELECT MAX(id_mensaje)
    FROM mensajes
    WHERE id_usuario_envia = $id_usuario
    OR id_usuario_recibe = $id_usuario
    GROUP BY id_publicacion,
    LEAST(id_usuario_envia,id_usuario_recibe),
    GREATEST(id_usuario_envia,id_usuario_recibe)
)
ORDER BY m.fecha_envio DESC
";

$resultado = mysqli_query($conexion,$sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversaciones | Chambitas</title>
    
    <link rel="icon" type="image/png" href="../img/favicon.png">
    
    <link rel="stylesheet" href="../css/conversaciones.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<?php include '../includes/navbar.php'; ?>

<div class="container">
    <div class="encabezado_feed">
        <h1>Mis conversaciones</h1>
        <p class="intro">Aquí puedes ver todos los chats relacionados con tus trabajos.</p>
    </div>

    <div class="lista-conversaciones">
        <?php
        if(mysqli_num_rows($resultado) > 0){
            while($row = mysqli_fetch_assoc($resultado)){
        ?>
            <div class="chat-card <?php echo ($row['no_leidos'] > 0) ? 'nuevo' : ''; ?>" 
                 onclick="window.location='chat.php?id_usuario=<?php echo $row['id_usuario']; ?>&id_publicacion=<?php echo $row['id_publicacion']; ?>'">
                
                <div class="chat-info">
                    <div class="chat-header">
                        <span class="trabajo-tag"><?php echo htmlspecialchars($row['titulo']); ?></span>
                        <span class="fecha-chat"><?php echo date("d/m H:i",strtotime($row['fecha_envio'])); ?></span>
                    </div>
                    <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                    <p class="ultimo-msj">
                        <?php
                        if($row['id_usuario_envia'] == $id_usuario){ echo "<strong>Tú:</strong> "; }
                        echo substr(htmlspecialchars($row['mensaje']),0,60)."...";
                        ?>
                    </p>
                </div>

                <?php if($row['no_leidos'] > 0){ ?>
                    <div class="notificacion-msj"><?php echo $row['no_leidos']; ?></div>
                <?php } ?>
            </div>
        <?php
            }
        } else {
        ?>
            <div class="sin-msj">
                <i class="fas fa-comments fa-3x"></i>
                <p>Aún no tienes conversaciones.</p>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>