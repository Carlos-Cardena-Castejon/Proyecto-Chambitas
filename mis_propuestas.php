<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

/* ======================
OBTENER PROPUESTAS
====================== */

$sql = "
SELECT 
p.titulo,
p.presupuesto,
p.id_publicacion,
pr.estado,
pr.mensaje
FROM propuestas pr
INNER JOIN publicaciones p
ON pr.id_publicacion = p.id_publicacion
WHERE pr.id_usuario = ?
ORDER BY pr.id_propuesta DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();
$propuestas = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Mis propuestas</title>
    <link rel="icon" type="image/png" href="img/favicon.png">

<link rel="stylesheet" href="css/perfil.css">

<style>

.container{
max-width:900px;
margin:40px auto;
}

.propuesta{

background:white;
padding:20px;
border-radius:10px;
margin-bottom:15px;
box-shadow:0 5px 10px rgba(0,0,0,0.1);

}

.estado{
font-weight:bold;
}

.aceptada{color:#27ae60;}
.rechazada{color:#e74c3c;}
.pendiente{color:#f39c12;}

</style>

</head>

<body>

<div class="container">

<h2>Mis propuestas</h2>

<?php if($propuestas->num_rows > 0): ?>

<?php while($prop = $propuestas->fetch_assoc()): ?>

<div class="propuesta">

<h3>
<a href="publicaciones/trabajo.php?id=<?php echo $prop['id_publicacion']; ?>">
<?php echo htmlspecialchars($prop['titulo']); ?>
</a>
</h3>

<p>Presupuesto: $<?php echo $prop['presupuesto']; ?></p>

<p><?php echo htmlspecialchars($prop['mensaje']); ?></p>

<p>

Estado:

<?php

if($prop['estado'] == "Aceptada"){
echo "<span class='estado aceptada'>Aceptada</span>";
}
elseif($prop['estado'] == "Rechazada"){
echo "<span class='estado rechazada'>Rechazada</span>";
}
else{
echo "<span class='estado pendiente'>Pendiente</span>";
}

?>

</p>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No has enviado propuestas aún.</p>

<?php endif; ?>

</div>

</body>
</html>