<?php
include("../conexion.php");

if(isset($_POST['id']) && isset($_POST['estado'])){
    $id = intval($_POST['id']);
    $estado = intval($_POST['estado']);

    // Actualiza el estado en la tabla para que el cambio sea persistente
    $stmt = $conexion->prepare("UPDATE servicios SET estado = ? WHERE id_servicio = ?");
    $stmt->bind_param("ii", $estado, $id);
    $stmt->execute();
    $stmt->close();
}
?>