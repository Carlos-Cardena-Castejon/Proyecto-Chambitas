<style>
    .modal-legal {
        display: none; 
        position: fixed; 
        z-index: 3000; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        background-color: rgba(0,0,0,0.6); 
        backdrop-filter: blur(3px);
    }
    .modal-legal-content {
        background-color: #fff; 
        margin: 5vh auto; 
        padding: 30px; 
        width: 90%; 
        max-width: 600px; 
        border-radius: 15px; 
        position: relative; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .close-legal {
        position: absolute; 
        top: 15px; 
        right: 20px; 
        font-size: 28px; 
        font-weight: bold; 
        color: #888; 
        cursor: pointer;
    }
    .close-legal:hover { color: #ff4b4b; }
    
    .legal-text {
        max-height: 65vh;
        overflow-y: auto;
        text-align: justify; 
        margin-bottom: 20px; 
        padding-right: 15px; 
        line-height: 1.6; 
        color: #444; 
        font-size: 0.95rem;
    }
    .legal-text::-webkit-scrollbar { width: 6px; }
    .legal-text::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    
    .btn-entendido {
        width: 100%; 
        padding: 12px; 
        background: #f1c40f; 
        border: none; 
        border-radius: 8px; 
        font-weight: bold; 
        cursor: pointer; 
        font-size: 1rem;
        transition: 0.3s;
    }
    .btn-entendido:hover { background: #d4ac0d; }
</style>

<div id="modalTerminos" class="modal-legal">
    <div class="modal-legal-content">
        <span class="close-legal" onclick="cerrarModalTerminos()">&times;</span>
        <h2 style="margin-top:0; border-bottom: 2px solid #f1c40f; padding-bottom:10px;">Términos y Condiciones</h2>
        <div class="legal-text">
            <h3>1. Aceptación y Requisito de Edad</h3>
            <p>Al registrarse y utilizar Chambitas, el usuario acepta estos términos. El uso de esta plataforma está estrictamente restringido a <strong>personas mayores de 18 años</strong>.</p>
            
            <h3>2. Naturaleza de la Plataforma</h3>
            <p>Chambitas es una plataforma digital sin fines de lucro para conectar personas. Chambitas no es empleador, contratista, ni agencia de colocación.</p>
            
            <h3>3. Independencia Económica</h3>
            <p>Chambitas no procesa transacciones económicas. Todos los acuerdos financieros y de pagos se realizan estrictamente en persona y por fuera de la plataforma. No asumimos responsabilidad legal o moral por fraudes o pagos no realizados.</p>
            
            <h3>4. Límite de Responsabilidad Civil</h3>
            <p>Cualquier interacción física o trato comercial se realiza bajo el propio riesgo de los usuarios. Chambitas no se hace responsable por daños materiales, lesiones o mala calidad del servicio.</p>
            
            <h3>5. Código de Conducta</h3>
            <p>Queda estrictamente prohibido: promover actividades ilegales/violentas, el Doxing (difamación o chismes), la venta de animales o productos físicos, el fraude académico (hacer tareas a nombre de otros), el spam y las estafas. El incumplimiento resultará en el baneo de la cuenta.</p>
        </div>
        <button type="button" class="btn-entendido" onclick="cerrarModalTerminos()">Entendido</button>
    </div>
</div>

<div id="modalPrivacidad" class="modal-legal">
    <div class="modal-legal-content">
        <span class="close-legal" onclick="cerrarModalPrivacidad()">&times;</span>
        <h2 style="margin-top:0; border-bottom: 2px solid #f1c40f; padding-bottom:10px;">Aviso de Privacidad</h2>
        <div class="legal-text">
            <h3>1. Identidad y Propósito</h3>
            <p>El equipo NEXTIC, desarrollador de Chambitas, se compromete a proteger su privacidad y detallar el uso de su información personal.</p>
            
            <h3>2. Información que Recopilamos</h3>
            <p>Solicitamos y almacenamos de forma segura: Nombre, correo, teléfono, Clave Única de Registro de Población (CURP), coordenadas geográficas de los servicios solicitados y evidencias fotográficas.</p>
            
            <h3>3. Uso de la Información</h3>
            <p>La CURP y el teléfono se utilizan exclusivamente para <strong>verificar su identidad</strong> y mantener la seguridad. Los datos geográficos permiten la funcionalidad del mapa. Utilizamos su información para investigar reportes de mal comportamiento si fuera necesario.</p>
            
            <h3>4. Protección de Datos</h3>
            <p>Sus datos personales <strong>no serán vendidos ni compartidos</strong> con terceros ni empresas de publicidad. Su información, incluyendo su contraseña, está respaldada por estrictos estándares de encriptación.</p>
            
            <h3>5. Derechos del Usuario</h3>
            <p>Usted puede solicitar la eliminación permanente de sus datos y su cuenta en cualquier momento a través del soporte de la plataforma.</p>
        </div>
        <button type="button" class="btn-entendido" onclick="cerrarModalPrivacidad()">Entendido</button>
    </div>
</div>

<script>
    function abrirModalTerminos() { document.getElementById("modalTerminos").style.display = "block"; }
    function abrirModalPrivacidad() { document.getElementById("modalPrivacidad").style.display = "block"; }

    function cerrarModalTerminos() { document.getElementById("modalTerminos").style.display = "none"; }
    function cerrarModalPrivacidad() { document.getElementById("modalPrivacidad").style.display = "none"; }

    window.addEventListener("click", function(event) {
        let modTerm = document.getElementById("modalTerminos");
        let modPriv = document.getElementById("modalPrivacidad");
        if (event.target == modTerm) { cerrarModalTerminos(); }
        if (event.target == modPriv) { cerrarModalPrivacidad(); }
    });
</script>