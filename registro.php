<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta | Chambitas</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #fffdf2; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        
        .login-card { background: white; padding: 2.5rem; border-radius: 20px; box-shadow: 0 15px 35px rgba(219, 163, 0, 0.15); width: 100%; max-width: 600px; border: 1px solid #f9eac2; }
        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-logo { max-width: 140px; height: auto; margin-bottom: 10px; }
        .login-header p { color: #887030; font-size: 0.95rem; }

        .register-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .input-group { display: flex; flex-direction: column; margin-bottom: 10px; position: relative; }
        .input-group label { font-size: 0.8rem; font-weight: 600; margin-bottom: 5px; color: #665421; }
        
        .input-group input { 
            padding: 12px 15px; 
            border: 2px solid #f1e4c0; 
            border-radius: 10px; 
            outline: none; 
            transition: all 0.3s ease; 
            background-color: #fffefb; 
            font-size: 0.9rem; 
            width: 100%;
        }
        .input-group input:focus { border-color: #ffc107; box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1); }
        
        /* Contenedor especial para el ojito */
        .pass-wrapper { position: relative; display: flex; align-items: center; }
        .toggle-icon { position: absolute; right: 15px; cursor: pointer; color: #887030; user-select: none; font-size: 20px; }

        .full-width { grid-column: span 2; }
        .btn-login { width: 100%; padding: 14px; background: #ffc107; color: #fff; border: none; border-radius: 10px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.3s ease; margin-top: 10px; }
        .btn-login:hover { background: #e0a800; }
        
        .login-footer { margin-top: 1.5rem; text-align: center; font-size: 0.9rem; }
        .login-footer a { color: #e0a800; text-decoration: none; font-weight: 600; }

        /* Modales */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); display: flex; justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: white; padding: 2rem; border-radius: 15px; text-align: center; max-width: 350px; border-top: 6px solid #ff4b4b; }
        .btn-close { margin-top: 15px; padding: 10px 20px; background: #333; color: white; border: none; border-radius: 8px; cursor: pointer; }

        @media (max-width: 500px) { .register-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="img/logo.png" alt="Chambitas Logo" class="login-logo">
        <p>Trabajo Local, Servicio Ideal</p>
    </div>

    <form action="auth/guardar_usuario.php" method="POST" class="register-grid">
        <div class="input-group">
            <label>Nombre(s)</label>
            <input type="text" name="nombre" placeholder="Ej. Juan" maxlength="40" required>
        </div>

        <div class="input-group">
            <label>Apellido Paterno</label>
            <input type="text" name="apellido_paterno" placeholder="Pérez" maxlength="40" required>
        </div>

        <div class="input-group">
            <label>Apellido Materno</label>
            <input type="text" name="apellido_materno" placeholder="García" maxlength="40" required>
        </div>

        <div class="input-group">
            <label>CURP</label>
            <input type="text" name="curp" placeholder="18 caracteres" minlength="18" maxlength="18" 
                   style="text-transform: uppercase;"
                   pattern="^[A-Z][AEIOUX][A-Z]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CH|CL|CM|CS|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$" 
                   title="Ingresa una CURP válida de 18 caracteres" required>
        </div>

        <div class="input-group">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" placeholder="usuario@dominio.com" 
                   pattern="^[a-zA-Z0-9._%+-]+@(gmail|outlook|hotmail|yahoo|icloud|live|prodigy|msn|infinitummail|protonmail|zoho)\.(com|net|org|mx|es)$" 
                   title="Usa un correo real (Gmail, Outlook, Hotmail, etc.)" required>
        </div>

        <div class="input-group">
            <label>Teléfono</label>
            <input type="tel" name="telefono" placeholder="10 dígitos" minlength="10" maxlength="10" 
                   pattern="[0-9]{10}" title="El teléfono debe tener 10 dígitos numéricos" required>
        </div>

        <div class="input-group full-width">
            <label>Contraseña</label>
            <div class="pass-wrapper">
                <input type="password" id="passInput" name="contrasena" placeholder="Mínimo 8 caracteres" 
                       minlength="8" style="padding-right: 45px;" required>
                <span class="material-icons-outlined toggle-icon" id="togglePass">visibility</span>
            </div>
        </div>

        <div class="full-width" style="margin: 10px 0 5px 0; font-size: 0.85rem; color: #665421; display: flex; align-items: center;">
            <input type="checkbox" id="acepto_terminos" name="acepto_terminos" required style="margin-right: 10px; transform: scale(1.2); cursor: pointer; width: auto;">
            <label for="acepto_terminos" style="cursor: pointer; margin-bottom: 0;">
                He leído y acepto los 
                <a href="#" onclick="abrirModalTerminos(); return false;" style="color: #e0a800; font-weight: bold; text-decoration: none;">Términos y Condiciones</a> 
                y el 
                <a href="#" onclick="abrirModalPrivacidad(); return false;" style="color: #e0a800; font-weight: bold; text-decoration: none;">Aviso de Privacidad</a>.
            </label>
        </div>

        <div class="full-width">
            <button type="submit" class="btn-login">Registrarse ahora</button>
        </div>
    </form>

    <div class="login-footer">
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</div>

<?php if(isset($_GET['error'])): ?>
<div id="errorModal" class="modal-overlay">
    <div class="modal-content">
        <div style="font-size: 40px;">⚠️</div>
        <h3 style="margin: 10px 0;">
            <?php 
                $err = $_GET['error'];
                if($err == 'correo_existe') echo "Correo repetido";
                elseif($err == 'curp_existe') echo "CURP repetida";
                elseif($err == 'tel_existe') echo "Teléfono repetido";
                elseif($err == 'vocabulario') echo "Texto no permitido";
                else echo "¡Atención!";
            ?>
        </h3>
        <p style="color: #666; font-size: 0.9rem;">
            <?php 
                if($err == 'correo_existe') echo "Este correo ya está en uso. Intenta con otro.";
                elseif($err == 'curp_existe') echo "Esta CURP ya está registrada en el sistema.";
                elseif($err == 'tel_existe') echo "Este número de teléfono ya pertenece a otra cuenta.";
                elseif($err == 'vocabulario') echo "Por favor, evita usar lenguaje inapropiado o nombres de broma.";
                else echo "Hubo un error al procesar tus datos. Revisa el formulario.";
            ?>
        </p>
        <button onclick="window.location.href='registro.php'" class="btn-close">Entendido</button>
    </div>
</div>
<?php endif; ?>

<script>
    // Lógica para mostrar/ocultar contraseña
    const togglePass = document.querySelector('#togglePass');
    const passInput = document.querySelector('#passInput');

    togglePass.addEventListener('click', function () {
        const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passInput.setAttribute('type', type);
        this.textContent = type === 'password' ? 'visibility' : 'visibility_off';
    });
</script>

<?php include 'includes/terminos_privacidad.php'; ?>

</body>
</html>