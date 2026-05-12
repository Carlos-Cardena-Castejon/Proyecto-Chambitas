<?php

include("../conexion.php");

$id = $_GET['id'];

$sql = "SELECT * FROM publicaciones WHERE id_publicacion='$id'";
$resultado = $conexion->query($sql);

$trabajo = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>

<title>Enviar propuesta</title>

<link rel="stylesheet" href="../css/estilos.css">

</head>

<body>

<div class="container">

<h2><?php echo $trabajo['titulo']; ?></h2>

<p><?php echo $trabajo['descripcion']; ?></p>

<p class="precio">
Presupuesto del cliente: $<?php echo $trabajo['presupuesto']; ?>
</p>

<form action="guardar_propuesta.php" method="POST">

<input type="hidden" name="id_publicacion" value="<?php echo $id; ?>">

<label>Tu precio</label>
<input type="number" name="precio" required>

<br><br>

<label>Tiempo estimado (días)</label>
<input type="number" name="tiempo_estimado" placeholder="Ej: 3" required>

<br><br>

<label>Presentacion</label>
<textarea name="mensaje" placeholder="Describe tu experiencia en el trabajo"></textarea>

<br><br>

<button onclick="abrirModal(<?php echo $row['id_publicacion']; ?>)">
Enviar propuesta
</button>

</form>

</div>

</body>
</html>