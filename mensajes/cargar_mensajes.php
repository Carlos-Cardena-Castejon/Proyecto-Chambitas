<?php
session_start();
include("../conexion.php");

$id_usuario = $_SESSION['id_usuario'];
$id_destino = (int)$_GET['id_usuario'];
$id_publicacion = (int)$_GET['id_publicacion'];

$sql = "SELECT * FROM mensajes 
        WHERE id_publicacion = '$id_publicacion' 
        AND ((id_usuario_envia = '$id_usuario' AND id_usuario_recibe = '$id_destino') 
        OR (id_usuario_envia = '$id_destino' AND id_usuario_recibe = '$id_usuario')) 
        ORDER BY fecha_envio ASC";

$resultado = $conexion->query($sql);

while($row = $resultado->fetch_assoc()){
    $es_mio = ($row['id_usuario_envia'] == $id_usuario);
    $clase = $es_mio ? "tuyo" : "usuario";
    
    // AGREGAMOS el atributo data-id con el ID de la base de datos
    echo '<div class="mensaje-wrapper '.$clase.'" data-id="'.$row['id_mensaje'].'">
            <div class="mensaje">
                <p>'.htmlspecialchars($row['mensaje']).'</p>
                <span class="hora">'.date("H:i", strtotime($row['fecha_envio'])).'</span>
            </div>
          </div>';
}
?>
