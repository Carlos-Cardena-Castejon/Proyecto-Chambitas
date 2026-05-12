<?php
session_start();
if(!isset($_SESSION['id_usuario'])){
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_usuario = $_SESSION['id_usuario'];

/* 1. INICIALIZACIÓN DE VARIABLES (Evita los Warnings) */
$info = null;
$resultado = null;
$id_destino = isset($_GET['id_usuario']) ? (int)$_GET['id_usuario'] : 0;
$id_publicacion = isset($_GET['id_publicacion']) ? (int)$_GET['id_publicacion'] : 0;

/* 2. LÓGICA DE DATOS (Solo si hay IDs válidos) */
if ($id_destino > 0 && $id_publicacion > 0) {
    
    // Marcar mensajes como leídos
    $marcar = "UPDATE mensajes SET leido = 1 
               WHERE id_usuario_recibe = '$id_usuario' 
               AND id_usuario_envia = '$id_destino' 
               AND id_publicacion = '$id_publicacion'";
    $conexion->query($marcar);

    // Obtener información del destinatario y la publicación
    $info_sql = "SELECT u.nombre, u.foto_perfil, p.titulo 
                 FROM Usuarios u 
                 JOIN Publicaciones p ON p.id_publicacion = '$id_publicacion'
                 WHERE u.id_usuario = '$id_destino'";
    $res_info = $conexion->query($info_sql);
    if ($res_info && $res_info->num_rows > 0) {
        $info = $res_info->fetch_assoc();
    }

    // Obtener el historial de mensajes
    $sql = "SELECT * FROM mensajes 
            WHERE id_publicacion = '$id_publicacion' 
            AND ((id_usuario_envia = '$id_usuario' AND id_usuario_recibe = '$id_destino') 
            OR (id_usuario_envia = '$id_destino' AND id_usuario_recibe = '$id_usuario')) 
            ORDER BY fecha_envio ASC";
    $resultado = $conexion->query($sql);
}

// Determinar la foto del otro usuario
$foto_otro = ($info && !empty($info['foto_perfil'])) ? '../'.$info['foto_perfil'] : '../img/usuario.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Chat | Chambitas</title>
        <link rel="icon" type="image/png" href="../img/favicon.png">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/chat.css">
</head>
<body>

<header class="chat-header">
    <div class="header-left">
        <a href="conversaciones.php" class="back-btn"><i class="fas fa-chevron-left"></i></a>
        
        <a href="../publicaciones/perfil_publico.php?id=<?php echo $id_destino; ?>" style="display: flex; align-items: center; text-decoration: none; gap: 12px;">
            <img src="<?php echo $foto_otro; ?>" class="header-avatar" alt="Perfil">
            <div class="user-info">
                <span class="user-name">
                    <?php echo ($info) ? htmlspecialchars($info['nombre']) : "Usuario"; ?>
                </span>
                <span class="job-title">
                    <?php echo ($info) ? htmlspecialchars($info['titulo']) : "Sin detalles"; ?>
                </span>
            </div>
        </a>
    </div>

</header>

    <main class="chat-container" id="chat-box">
        <?php 
        if($resultado && $resultado->num_rows > 0){
            while($row = $resultado->fetch_assoc()){ 
                $es_mio = ($row['id_usuario_envia'] == $id_usuario);
                $clase_lado = $es_mio ? "tuyo" : "usuario";
        ?>
            <div class="mensaje-wrapper <?php echo $clase_lado; ?>" data-id="<?php echo $row['id_mensaje']; ?>">
                
                <?php if(!$es_mio): ?>
                    <img src="<?php echo $foto_otro; ?>" class="chat-mini-avatar" alt="User">
                <?php endif; ?>
                
                <div class="mensaje">
                    <p><?php echo htmlspecialchars($row['mensaje']); ?></p>
                    <div class="hora">
                        <?php echo date("H:i", strtotime($row['fecha_envio'])); ?>
                        <?php if($es_mio): ?>
                            <i class="fas fa-check-double <?php echo ($row['leido'] == 1) ? 'read' : ''; ?>"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php 
            } 
        } else {
            echo '<div class="empty-chat" style="text-align:center; padding:40px; color:rgba(255,255,255,0.2);">
                    <i class="fas fa-comments" style="font-size:3rem; margin-bottom:10px;"></i>
                    <p>No hay mensajes aún.</p>
                  </div>';
        }
        ?>
    </main>

    <footer class="chat-input-area">
        <form id="form-enviar" autocomplete="off">
            <input type="hidden" name="destino" value="<?php echo $id_destino; ?>">
            <input type="hidden" name="id_publicacion" value="<?php echo $id_publicacion; ?>">
            
            <div class="input-wrapper">
                <textarea name="mensaje" id="msj-input" placeholder="Escribe un mensaje..." required rows="1"></textarea>
                <button type="submit" id="btn-enviar">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </footer>

    <script>
        const chatBox = document.getElementById("chat-box");
        const formEnviar = document.getElementById("form-enviar");
        const msjInput = document.getElementById("msj-input");

        // Función para bajar el scroll al último mensaje
        function scrollAbajo() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        window.onload = scrollAbajo;

        /* --- 1. ENVIAR CON ENTER --- */
        msjInput.addEventListener("keydown", function(e) {
            // Si pulsa Enter sin Shift, envía el formulario
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                formEnviar.dispatchEvent(new Event("submit"));
            }
        });

        /* --- 2. AUTO-AJUSTE DE ALTURA DEL TEXTAREA --- */
        msjInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight > 100 ? 100 : this.scrollHeight) + 'px';
        });

        /* --- 3. PROCESAR EL ENVÍO (AJAX) --- */
        formEnviar.onsubmit = function(e) {
            e.preventDefault();
            const texto = msjInput.value.trim();
            if(texto === "") return;

            const formData = new FormData(formEnviar);
            fetch("enviar_mensaje.php", { 
                method: "POST", 
                body: formData 
            })
            .then(() => {
                msjInput.value = ""; // Limpiar campo
                msjInput.style.height = 'auto'; // Resetear altura
                cargarMensajes(); // Refrescar
                setTimeout(scrollAbajo, 100);
            })
            .catch(err => console.error("Error al enviar:", err));
        };

        /* --- 4. CARGAR MENSAJES DINÁMICAMENTE --- */
        function cargarMensajes(){
            fetch(`cargar_mensajes.php?id_usuario=<?php echo $id_destino; ?>&id_publicacion=<?php echo $id_publicacion; ?>`)
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                
                const nuevos = tempDiv.querySelectorAll('.mensaje-wrapper');
                const actuales = chatBox.querySelectorAll('.mensaje-wrapper');
                
                // Solo refrescar si hay mensajes nuevos
                if (nuevos.length !== actuales.length) {
                    chatBox.innerHTML = html;
                    scrollAbajo();
                }
            })
            .catch(err => console.error("Error al cargar:", err));
        }

        // Revisar mensajes nuevos cada 3 segundos
        setInterval(cargarMensajes, 3000);


        /* --- AUTO-SCROLL AL ENFOCAR --- */
msjInput.addEventListener("focus", function() {
    setTimeout(scrollAbajo, 200); // Esperamos a que el teclado termine de subir
});
    </script>
</body>
</html>