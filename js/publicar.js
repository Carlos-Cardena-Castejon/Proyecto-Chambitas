// 1. VARIABLES GLOBALES
let archivosChambita = [];

// 2. LÓGICA DE FOTOS (FUERA PARA SER ACCESIBLE)
window.actualizarContadorFotos = function() {
    const input = document.getElementById('imagenes');
    // Guardamos los archivos en nuestro array global
    archivosChambita = Array.from(input.files);
    renderizarMiniaturas();
};

function renderizarMiniaturas() {
    const preview = document.getElementById('previsualizacion-fotos');
    const text = document.getElementById('foto-text');
    const input = document.getElementById('imagenes');
    
    preview.innerHTML = ''; 

    if (archivosChambita.length > 0) {
        text.innerHTML = `<span style="color: #27ae60;"><i class="fas fa-check-circle"></i> ${archivosChambita.length} fotos listas</span>`;
        
        archivosChambita.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-container'; // Asegúrate de tener el CSS que te pasé antes
                div.style = "position: relative; width: 100%; height: 80px;";
                div.innerHTML = `
                    <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px; border: 2px solid #f1c40f;">
                    <button type="button" onclick="quitarFoto(${index})" style="position: absolute; top: -5px; right: -5px; background: #ff4b4b; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10;">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        text.innerText = "Toca para subir fotos o arrastra aquí";
        input.value = ""; 
    }
}

window.quitarFoto = function(index) {
    archivosChambita.splice(index, 1);
    
    // Actualizamos el input real para que PHP reciba solo lo que quedó
    const dt = new DataTransfer();
    archivosChambita.forEach(file => dt.items.add(file));
    document.getElementById('imagenes').files = dt.files;

    renderizarMiniaturas();
};

// 3. INICIALIZACIÓN CUANDO EL DOM ESTÉ LISTO
document.addEventListener('DOMContentLoaded', function() {
    
    // OTRO SERVICIO
    window.verificarOtroServicio = function() {
        const select = document.getElementById('selectServicio');
        const grupoOtro = document.getElementById('grupoOtroServicio');
        const inputOtro = document.getElementById('otro_servicio');

        if (select.value === 'otro') {
            grupoOtro.style.setProperty('display', 'block', 'important');
            inputOtro.setAttribute('required', 'true');
            inputOtro.focus();
        } else {
            grupoOtro.style.display = 'none';
            inputOtro.removeAttribute('required');
        }
    };

    // MAPA
    const boundsNL = [[23.1, -101.2], [27.8, -98.3]];
    window.map = L.map('mapa', { maxBounds: boundsNL, maxBoundsViscosity: 1.0 }).setView([25.6866, -100.3161], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    window.marcador = null;

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lon = e.latlng.lng;
        if(window.marcador) map.removeLayer(window.marcador);
        window.marcador = L.marker([lat, lon]).addTo(map).bindPopup("<b>¡Aquí es!</b>").openPopup();
        document.getElementById("latitud").value = lat;
        document.getElementById("longitud").value = lon;
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(res => res.json())
        .then(data => {
            const muni = data.address.city || data.address.town || "Nuevo León";
            document.getElementById("municipio").value = muni;
            document.getElementById("direccion").value = (data.display_name.split(',')[0] || "") + ", " + muni;
        });
    });

    // CONTADORES
    function setupCounter(inputId, countId, max) {
        const input = document.getElementById(inputId);
        const count = document.getElementById(countId);
        if(input && count) {
            input.addEventListener('input', () => {
                const restantes = max - input.value.length;
                count.textContent = `${restantes} caracteres restantes`;
                count.style.color = restantes < 10 ? 'red' : '#888';
            });
        }
    }
    setupCounter('titulo', 'count-titulo', 50);
    setupCounter('descripcion', 'count-desc', 500);
    setupCounter('otro_servicio', 'charCount', 15);
});

// 4. FUNCIONES DE APOYO (BUSCADOR Y CONFIRMACIÓN)
function buscarDireccion() {
    let direccion = document.getElementById("direccion").value;
    if(!direccion) return;
    const btn = document.getElementById('btnBuscar');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion + ", Nuevo Leon")}&limit=1`)
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = '<i class="fas fa-search"></i> Buscar';
        if(data.length > 0) {
            let res = data[0];
            let lat = parseFloat(res.lat);
            let lon = parseFloat(res.lon);
            if(window.marcador) map.removeLayer(window.marcador);
            window.marcador = L.marker([lat, lon]).addTo(map).openPopup();
            document.getElementById("latitud").value = lat;
            document.getElementById("longitud").value = lon;
            map.setView([lat, lon], 14);
        }
    });
}

function confirmarPublicacion(e) {
    if(!document.getElementById('latitud').value) {
        e.preventDefault();
        Swal.fire('¿Dónde es?', 'Fija la ubicación en el mapa.', 'info');
        return false;
    }
    Swal.fire({title: 'Publicando...', didOpen: () => { Swal.showLoading(); }});
    return true;
}
