<?php
session_start();
include("../conexion.php");

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT 
publicaciones.id_publicacion,
publicaciones.titulo,
publicaciones.descripcion,
publicaciones.presupuesto,
publicaciones.fecha_publicacion,
COUNT(propuestas.id_propuesta) AS total_propuestas

FROM publicaciones

LEFT JOIN propuestas
ON publicaciones.id_publicacion = propuestas.id_publicacion

WHERE publicaciones.id_usuario = '$id_usuario'

GROUP BY publicaciones.id_publicacion

ORDER BY publicaciones.fecha_publicacion DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html>

<head>

<title>Mis trabajos publicados</title>

<link rel="stylesheet" href="../css/estilos.css">

</head>

<body>

<header>

<h2>📋 Mis trabajos publicados</h2>

<a class="boton" href="feed.php">
Volver
</a>

</header>

<div class="container">

<div class="feed">

<?php while($row = $resultado->fetch_assoc()){ ?>

<div class="chambita">

<h3><?php echo $row['titulo']; ?></h3>

<p><?php echo $row['descripcion']; ?></p>

<p class="precio">
$<?php echo $row['presupuesto']; ?>
</p>

<p>
📨 Propuestas recibidas: 
<strong><?php echo $row['total_propuestas']; ?></strong>
</p>

<br>

<a class="boton" href="ver_propuestas.php?id_publicacion=<?php echo $row['id_publicacion']; ?>">
    Ver propuestas
</a>

</div>

<?php } ?>

</div>

</div>

</body>
</html>