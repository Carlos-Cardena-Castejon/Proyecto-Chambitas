<?php
session_start();
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit;
}

include("../conexion.php");

$id_usuario = $_SESSION['id_usuario'];
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$servicio_recibido = $_POST['servicio']; // Puede ser un ID o la palabra "otro"
$presupuesto = $_POST['presupuesto'];
$latitud = $_POST['latitud'] ?? null;
$longitud = $_POST['longitud'] ?? null;

/* ==========================
   LÓGICA PARA "OTRO" SERVICIO
   ========================== */
$id_servicio_final = null;
$servicio_personalizado = null;

// En tu DB el ID 31 es "Otro". Verificamos ambos casos.
if ($servicio_recibido === 'otro' || $servicio_recibido == '31') {
    $id_servicio_final = null; // NULL evita el error de Foreign Key
    $servicio_personalizado = $_POST['otro_servicio_nombre'] ?? 'Servicio no especificado';
} else {
    $id_servicio_final = intval($servicio_recibido);
    $servicio_personalizado = null;
}

/* ==========================
   GUARDAR PUBLICACIÓN
   ========================== */
// Usamos sentencias preparadas para mayor seguridad
$sql = "INSERT INTO publicaciones (id_usuario, id_servicio, titulo, descripcion, presupuesto, servicio_personalizado) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iisdds", $id_usuario, $id_servicio_final, $titulo, $descripcion, $presupuesto, $servicio_personalizado);

if($stmt->execute()){
    $id_publicacion = $stmt->insert_id;

    /* ==========================
       GUARDAR IMÁGENES
       ========================== */
    if(isset($_FILES['imagenes'])){
        $total = count($_FILES['imagenes']['name']);
        for($i=0; $i<$total; $i++){
            if($_FILES['imagenes']['name'][$i] != ""){
                $ext = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
                $nombre_limpio = preg_replace("/[^a-zA-Z0-9]/", "_", pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_FILENAME));
                $nombre = time()."_".$nombre_limpio.".".$ext;
                
                $rutaBD = "uploads/imagenes/".$nombre;
                $rutaServidor = "../uploads/imagenes/".$nombre;

                if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $rutaServidor)){
                    $sqlImg = "INSERT INTO imagenestrabajo (id_publicacion, ruta_imagen) VALUES (?, ?)";
                    $stmtImg = $conexion->prepare($sqlImg);
                    $stmtImg->bind_param("is", $id_publicacion, $rutaBD);
                    $stmtImg->execute();
                }
            }
        }
    }

    /* ==========================
       GUARDAR UBICACIÓN
       ========================== */
    if($latitud !== null && $longitud !== null){
        $sqlUbi = "INSERT INTO ubicaciones (id_usuario, id_publicacion, latitud, longitud) VALUES (?, ?, ?, ?)";
        $stmtUbi = $conexion->prepare($sqlUbi);
        $stmtUbi->bind_param("iidd", $id_usuario, $id_publicacion, $latitud, $longitud);
        if(!$stmtUbi->execute()){
            error_log("Error en ubicación: ".$conexion->error);
        }
    }

    header("Location: ../feed.php?success=1");
    exit;

} else {
    echo "Error crítico al publicar: " . $stmt->error;
}

/* ==========================
   GUARDAR PUBLICACIÓN (MODO DEBUG)
   ========================== */
$sql = "INSERT INTO publicaciones (id_usuario, id_servicio, titulo, descripcion, presupuesto, servicio_personalizado, estado) 
        VALUES (?, ?, ?, ?, ?, ?, 'Activa')";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la preparación: " . $conexion->error);
}

// Verifica que estas variables tengan contenido antes del bind_param
// var_dump($id_usuario, $id_servicio_final, $titulo, $descripcion, $presupuesto, $servicio_personalizado); 

$stmt->bind_param("iisdds", 
    $id_usuario, 
    $id_servicio_final, 
    $titulo, 
    $descripcion, 
    $presupuesto, 
    $servicio_personalizado
);

if($stmt->execute()){
    $id_publicacion = $stmt->insert_id;
    echo "ID Creado: " . $id_publicacion; // Si ves esto, sí se creó.
    
    // ... resto del código de imágenes ...
} else {
    // ESTO te dirá la verdad de por qué no se crea
    echo "Error al ejecutar: " . $stmt->error;
    exit;
}
?>