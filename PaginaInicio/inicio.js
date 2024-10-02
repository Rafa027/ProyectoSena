
function mostrarHora() {
    const fecha = new Date();
    const opcionesFecha = { day: '2-digit', month: '2-digit', year: 'numeric' };
    const fechaLocal = fecha.toLocaleDateString([], opcionesFecha);
    const opciones = { hour: '2-digit', minute: '2-digit' };
    const hora = fecha.toLocaleTimeString([], opciones); // Obtener la hora local
    document.querySelector(".hora").textContent = `${fechaLocal} - ${hora}`;
}

// Llamar a la función una vez para mostrar la hora al cargar la página
mostrarHora();

// Actualizar la hora cada segundo

