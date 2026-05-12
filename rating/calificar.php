<?php
$id_contratacion = $_GET['id_contratacion'];
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title>Calificar trabajador</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
font-family: Arial, sans-serif;
background:#f4f6f9;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
margin:0;
}

.card{

background:white;
padding:40px;
border-radius:12px;
box-shadow:0 10px 25px rgba(0,0,0,0.1);
width:400px;
text-align:center;

}

.card h2{
margin-bottom:20px;
color:#333;
}

.estrellas{
font-size:35px;
color:#ccc;
cursor:pointer;
margin-bottom:20px;
}

.estrella.activa{
color:#FFD700;
}

textarea{

width:100%;
height:100px;
border-radius:8px;
border:1px solid #ccc;
padding:10px;
resize:none;
margin-top:10px;
margin-bottom:20px;
font-size:14px;

}

button{

background:#28a745;
color:white;
border:none;
padding:12px 20px;
border-radius:8px;
cursor:pointer;
font-size:16px;
width:100%;
transition:0.3s;

}

button:hover{
background:#218838;
}

</style>

</head>

<body>

<div class="card">

<h2>⭐ Califica al trabajador</h2>

<form action="guardar_calificacion.php" method="POST">

<input type="hidden" name="id_contratacion" value="<?php echo $id_contratacion; ?>">
<input type="hidden" name="puntuacion" id="puntuacion">

<div class="estrellas">

<i class="fa-solid fa-star estrella" data-valor="1"></i>
<i class="fa-solid fa-star estrella" data-valor="2"></i>
<i class="fa-solid fa-star estrella" data-valor="3"></i>
<i class="fa-solid fa-star estrella" data-valor="4"></i>
<i class="fa-solid fa-star estrella" data-valor="5"></i>

</div>

<textarea name="comentario" placeholder="Escribe un comentario sobre el trabajo..."></textarea>

<button type="submit">Enviar calificación</button>

</form>

</div>

<script>

const estrellas = document.querySelectorAll(".estrella");
const input = document.getElementById("puntuacion");

estrellas.forEach(estrella => {

estrella.addEventListener("click", function(){

let valor = this.dataset.valor;

input.value = valor;

estrellas.forEach(e => e.classList.remove("activa"));

for(let i=0;i<valor;i++){
estrellas[i].classList.add("activa");
}

});

});

</script>

</body>
</html>
