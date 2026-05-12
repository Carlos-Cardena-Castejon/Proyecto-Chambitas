function abrirModalReporte() {
    document.getElementById('modalReporte').style.display = 'block';
}

function cerrarModalReporte() {
    document.getElementById('modalReporte').style.display = 'none';
}

document.getElementById('formReporte').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('procesar_reporte.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if(data.trim() === 'success') {
            cerrarModalReporte();
            Swal.fire({
                icon: 'success',
                title: 'Reporte Enviado',
                text: 'El administrador revisará el caso pronto.',
                confirmButtonColor: '#f1c40f'
            });
        } else {
            Swal.fire('Error', 'No se pudo enviar el reporte: ' + data, 'error');
        }
    });
};

window.onclick = function(event) {
    let modal = document.getElementById('modalReporte');
    if (event.target == modal) { cerrarModalReporte(); }
}