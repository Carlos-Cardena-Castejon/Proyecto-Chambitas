<?php
session_start();
include("conexion.php");

if(!isset($_SESSION['id_usuario'])){
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

/* ======================
OBTENER TRABAJOS
====================== */

$sql = "
SELECT 
p.titulo,
p.presupuesto,
p.id_publicacion,
c.estado,
c.fecha_contratacion
FROM contrataciones c
INNER JOIN publicaciones p 
ON c.id_publicacion = p.id_publicacion
WHERE c.id_usuario = ?
ORDER BY c.fecha_contratacion DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i",$id_usuario);
$stmt->execute();
$trabajos = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Historial de trabajos</title>
    <link rel="icon" type="image/png" href="img/favicon.png">

<link rel="stylesheet" href="css/perfil.css">

<style>

.container{
max-width:900px;
margin:40px auto;
}

.trabajo{

background:white;
padding:20px;
border-radius:10px;
margin-bottom:15px;
box-shadow:0 5px 10px rgba(0,0,0,0.1);

}

.estado{
font-weight:bold;
}

.finalizado{color:#27ae60;}
.progreso{color:#f39c12;}

</style>

</head>

<body>

<div class="container">

<h2>Historial de trabajos</h2>

<?php if($trabajos->num_rows > 0): ?>

<?php while($trab = $trabajos->fetch_assoc()): ?>

<div class="trabajo">

<h3>
<a href="publicaciones/trabajo.php?id=<?php echo $trab['id_publicacion']; ?>">
<?php echo htmlspecialchars($trab['titulo']); ?>
</a>
</h3>

<p>Presupuesto: $<?php echo $trab['presupuesto']; ?></p>

<p>

Estado:

<?php if($trab['estado'] == "Finalizado"): ?>

<span class="estado finalizado">Finalizado</span>

<?php else: ?>

<span class="estado progreso">En progreso</span>

<?php endif; ?>

</p>

<p>
Fecha: <?php echo date("d M Y", strtotime($trab['fecha_contratacion'])); ?>
</p>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No has realizado ningún trabajo aún.</p>

<?php endif; ?>

</div>

</body>
</html>