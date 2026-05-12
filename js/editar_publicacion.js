document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editForm');
    const tituloInput = document.getElementById('titulo');
    const descInput = document.getElementById('descripcion');
    const presupuestoInput = document.getElementById('presupuesto');

    // --- 1. CONTADORES DE CARACTERES ---
    function updateCount(input, targetId, max) {
        const countSpan = document.getElementById(targetId);
        if (countSpan) {
            countSpan.innerText = (max - input.value.length) + " restantes";
        }
    }

    tituloInput.addEventListener('input', () => updateCount(tituloInput, 'count-titulo', 50));
    descInput.addEventListener('input', () => updateCount(descInput, 'count-desc', 500));
    
    // Inicializar contadores al cargar
    updateCount(tituloInput, 'count-titulo', 50);
    updateCount(descInput, 'count-desc', 500);

    // --- 2. LÓGICA DEL MAPA (Leaflet) ---
    let latInit = parseFloat(document.getElementById('latitud').value) || 25.6866;
    let lonInit = parseFloat(document.getElementById('longitud').value) || -100.3161;

    const map = L.map('mapa').setView([latInit, lonInit], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    let marker = L.marker([latInit, lonInit], {draggable: true}).addTo(map);

    marker.on('dragend', function() {
        let p = marker.getLatLng();
        document.getElementById('latitud').value = p.lat;
        document.getElementById('longitud').value = p.lng;
    });

    // --- 3. BÚSQUEDA LIMITADA A NUEVO LEÓN (MENSAJES EN FORM) ---
    window.buscarDireccion = function() {
        let d = document.getElementById('direccion').value;
        const mapError = document.getElementById('mapa-error');
        if(mapError) mapError.innerText = ""; // Limpiar error previo

        if(d.length < 3) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(d + ", Nuevo Leon, Mexico")}&limit=1`)
        .then(r => r.json()).then(data => {
            if(data.length > 0) {
                // Validación estricta de que sea en Nuevo León
                const isNL = data[0].display_name.toLowerCase().includes("nuevo león");
                
                if(isNL) {
                    map.flyTo([data[0].lat, data[0].lon], 16);
                    marker.setLatLng([data[0].lat, data[0].lon]);
                    document.getElementById('latitud').value = data[0].lat;
                    document.getElementById('longitud').value = data[0].lon;
                } else {
                    if(mapError) mapError.innerText = "⚠️ Por favor, selecciona una ubicación dentro de Nuevo León.";
                }
            } else {
                if(mapError) mapError.innerText = "❌ No se encontró la dirección en Nuevo León.";
            }
        }).catch(() => {
            if(mapError) mapError.innerText = "Error al conectar con el servidor de mapas.";
        });
    };

    // --- 4. VALIDACIÓN DE PRESUPUESTO Y ENVÍO (MENSAJES EN FORM) ---
    editForm.onsubmit = function(e) {
        const pValue = parseFloat(presupuestoInput.value);
        const presError = document.getElementById('presupuesto-error');
        
        if(presError) presError.innerText = ""; // Limpiar error previo

        if (pValue < 50 || pValue > 200000) {
            e.preventDefault();
            if(presError) presError.innerText =  "El presupuesto debe estar entre $50 y $200,000.";
            presupuestoInput.focus();
            return false;
        }

        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<span>GUARDANDO...</span> <i class="fas fa-spinner fa-spin"></i>';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    };
});