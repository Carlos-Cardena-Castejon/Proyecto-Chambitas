<?php
session_start();
// Correcto: Sube dos niveles para llegar a la raíz (chambitas/conexion.php)
include("../../conexion.php");

if(!isset($_SESSION['id_usuario'])){
// Al final de tu lógica de guardado exitoso:
header("Location: ../trabajo.php?id=" . $id_publicacion . "&enviado=1");
exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Es buena práctica usar (int) para asegurar que sean números
$id_publicacion = (int)$_POST['id_publicacion'];
$precio = (float)$_POST['precio'];
$tiempo = (int)$_POST['tiempo_estimado'];
// real_escape_string evita errores si el mensaje lleva comillas (')
$mensaje = $conexion->real_escape_string($_POST['mensaje']);


/* =========================
   VERIFICAR PROPUESTA EXISTENTE
========================= */
$verificar = "SELECT * FROM propuestas 
              WHERE id_publicacion = '$id_publicacion'
              AND id_usuario = '$id_usuario'";

$resultado = $conexion->query($verificar);

if($resultado->num_rows > 0){
    echo "<script>
    alert('Ya enviaste una propuesta para este trabajo');
    window.history.back();
    </script>";
    exit();
}


/* =========================
   GUARDAR PROPUESTA
========================= */
$sql = "INSERT INTO propuestas (id_publicacion, id_usuario, precio_oferta, tiempo_estimado, mensaje)
        VALUES ('$id_publicacion', '$id_usuario', '$precio', '$tiempo', '$mensaje')";

if($conexion->query($sql)){
    // ÉXITO: Redireccionamos a la página del trabajo con el parámetro enviado=1
    // Esto disparará el SweetAlert que pusiste en trabajo.php
    header("Location: ../trabajo.php?id=" . $id_publicacion . "&enviado=1");
    exit();
} else {
    echo "Error al guardar: " . $conexion->error;
}
?>
?>