// Detectar si la propuesta se envió con éxito mediante la URL
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('enviado') === '1') {
    Swal.fire({
        title: '¡Propuesta Enviada!',
        text: 'Tu oferta ha sido registrada correctamente. ¡Mucho éxito!',
        icon: 'success',
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Entendido',
        timer: 3000, // Se cierra sola en 3 segundos si no le pican
        timerProgressBar: true
    });
    
    // Limpiar la URL para que no salga la alerta cada que recargue
    window.history.replaceState({}, document.title, window.location.pathname + "?id=<?php echo $id; ?>");
}

const modal = document.getElementById("modalPropuesta");
const btnAbrir = document.getElementById("btnAbrirModal");
const btnCerrar = document.getElementById("btnCerrarModal");

btnAbrir.onclick = () => modal.style.display = "block";
btnCerrar.onclick = () => modal.style.display = "none";

window.onclick = (event) => {
    if(event.target == modal){
        modal.style.display = "none";
    }
}