<?php
include("../conexion.php"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Limpieza y Formateo Profesional
    $nombre = ucwords(strtolower(trim(mysqli_real_escape_string($conexion, $_POST['nombre']))));
    $ap = ucwords(strtolower(trim(mysqli_real_escape_string($conexion, $_POST['apellido_paterno']))));
    $am = ucwords(strtolower(trim(mysqli_real_escape_string($conexion, $_POST['apellido_materno']))));
    $curp = strtoupper(trim(mysqli_real_escape_string($conexion, $_POST['curp'])));
    $correo = strtolower(trim(mysqli_real_escape_string($conexion, $_POST['correo'])));
    $tel = trim(mysqli_real_escape_string($conexion, $_POST['telefono']));
    $pass = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);

    // --- REFUERZO: Validar extensión de correo (Evita el .ocm) ---
    $extensiones_permitidas = ['com', 'net', 'org', 'mx', 'es'];
    $partes_correo = explode('.', $correo);
    $ultima_parte = end($partes_correo);

    if (!in_array($ultima_parte, $extensiones_permitidas)) {
        header("Location: ../registro.php?error=correo_invalido");
        exit();
    }

    // 2. EL FILTRO DE INSULTOS
    $archivo_insultos = __DIR__ . '/insultos.txt';
    if (file_exists($archivo_insultos)) {
        $insultos = file($archivo_insultos, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $texto_revisar = strtolower("$nombre $ap $am $correo");

        foreach ($insultos as $insulto) {
            $insulto_limpio = preg_quote(trim($insulto), '/');
            if (preg_match("/\b$insulto_limpio\b/i", $texto_revisar)) {
                header("Location: ../registro.php?error=vocabulario");
                exit();
            }
        }
    }

    // 3. VERIFICAR DUPLICADOS
    // --- Correo ---
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { 
        $stmt->close();
        header("Location: ../registro.php?error=correo_existe"); 
        exit(); 
    }
    $stmt->close();

    // --- CURP ---
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE curp = ?");
    $stmt->bind_param("s", $curp);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { 
        $stmt->close();
        header("Location: ../registro.php?error=curp_existe"); 
        exit(); 
    }
    $stmt->close();

    // --- Teléfono ---
    $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE telefono = ?");
    $stmt->bind_param("s", $tel);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) { 
        $stmt->close();
        header("Location: ../registro.php?error=tel_existe"); 
        exit(); 
    }
    $stmt->close();

    // 4. INSERCIÓN FINAL
    $sql = $conexion->prepare("INSERT INTO usuarios (nombre, apellido_paterno, apellido_materno, curp, correo, telefono, contrasena) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("sssssss", $nombre, $ap, $am, $curp, $correo, $tel, $pass);

    if ($sql->execute()) {
        $sql->close();
        header("Location: ../login.php?registro=exitoso");
    } else {
        header("Location: ../registro.php?error=db");
    }
    
    $conexion->close();
}
?>