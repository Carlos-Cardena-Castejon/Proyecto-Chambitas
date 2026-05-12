<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="img/logo.png" alt="Logo Nextio" class="login-logo">
        <p>Ingresa tus credenciales para continuar</p>
    </div>

    <form action="auth/validar_login.php" method="POST" class="login-form">
        <div class="input-group">
            <label for="correo">Correo Electrónico</label>
            <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
        </div>

        <div class="input-group">
            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">
            Entrar
        </button>
    </form>

    <div class="login-footer">
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</div>

</body>
</html>