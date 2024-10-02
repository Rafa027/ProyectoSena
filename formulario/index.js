container = document.querySelector(".container")
        // Establecemos atributos de validación
        container.setAttribute('min', '1000000'); // Mínimo valor de 7 dígitos
        container.setAttribute('max', '9999999999'); // Máximo valor de 10 dígitos
        // Validación personalizada al cambiar el valor
        container.addEventListener('input', function() {
            const value = container.value;
            if (value.length <= 7) {
                container.setCustomValidity('El número debe tener al menos 7 dígitos.');
            }
            else if(value.length >= 11){
                container.setCustomValidity('El numero debe ser menor a 10 digitos.');
            } 
            else {
                container.setCustomValidity(''); // No hay errores
            }
        });

const password = document.querySelector(".password");
password.addEventListener('input', function(){
    const value = password.value;
    if(value.length < 6){
        password.setCustomValidity('Debe tener por lo menos 6 caracteres')
    }

    else{
        password.setCustomValidity('');
    }
})


const contacto = document.querySelector(".numero");
contacto.addEventListener('input', function(){
    const value = contacto.value;
    if(value.length < 10){
        contacto.setCustomValidity('Escriba un numero valido')
    }

    else{
        contacto.setCustomValidity('');
    }
})
