/**
 * Manejador de formularios para el sistema de afiliados
 * Este script gestiona la interacción de campos en el formulario y automatiza tareas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario relacionados al domicilio
    const domicilioInput = document.getElementById('domicilio');
    const calleInput = document.getElementById('calle');
    const numeroExteriorInput = document.getElementById('numero_exterior');
    const numeroInteriorInput = document.getElementById('numero_interior');
    const coloniaInput = document.getElementById('colonia');
    const codigoPostalInput = document.getElementById('codigo_postal');
    const estadoSelect = document.getElementById('estado');
    const municipioInput = document.getElementById('municipio');
    
    // Verificar que existen los elementos necesarios
    if (domicilioInput) {
        // Deshabilitar el campo de domicilio completo
        domicilioInput.readOnly = true;
        domicilioInput.classList.add('bg-gray-50');
        
        // Conjunto de elementos que componen el domicilio
        const domicilioElements = [
            calleInput, 
            numeroExteriorInput, 
            numeroInteriorInput, 
            coloniaInput, 
            codigoPostalInput,
            estadoSelect,
            municipioInput
        ];
        
        // Función para actualizar el domicilio completo
        function updateDomicilio() {
            let domicilioParts = [];
            
            // Añadir calle y número
            if (calleInput && calleInput.value.trim()) {
                domicilioParts.push(calleInput.value.trim());
            }
            
            // Añadir número exterior
            if (numeroExteriorInput && numeroExteriorInput.value.trim()) {
                domicilioParts.push('No. ' + numeroExteriorInput.value.trim());
            }
            
            // Añadir número interior (si existe)
            if (numeroInteriorInput && numeroInteriorInput.value.trim()) {
                domicilioParts.push('Int. ' + numeroInteriorInput.value.trim());
            }
            
            // Añadir colonia
            if (coloniaInput && coloniaInput.value.trim()) {
                domicilioParts.push('Col. ' + coloniaInput.value.trim());
            }
            
            // Añadir código postal
            if (codigoPostalInput && codigoPostalInput.value.trim()) {
                domicilioParts.push('C.P. ' + codigoPostalInput.value.trim());
            }
            
            // Añadir municipio
            if (municipioInput && municipioInput.value.trim()) {
                domicilioParts.push(municipioInput.value.trim());
            }
            
            // Añadir estado
            if (estadoSelect && estadoSelect.value) {
                // Verificar que no sea la opción por defecto
                if (estadoSelect.selectedIndex > 0) {
                    domicilioParts.push(estadoSelect.options[estadoSelect.selectedIndex].text);
                }
            }
            
            // Actualizar el campo de domicilio completo
            domicilioInput.value = domicilioParts.join(', ');
            
            // Asegurarse de que el campo se enviará aunque esté como readOnly
            if (domicilioInput.value.trim() === '') {
                domicilioInput.value = ' '; // Un espacio para asegurar que no está vacío
            }
        }
        
        // Agregar event listeners a todos los elementos del domicilio
        domicilioElements.forEach(element => {
            if (element) {
                element.addEventListener('change', updateDomicilio);
                element.addEventListener('input', updateDomicilio);
                element.addEventListener('blur', updateDomicilio);
            }
        });
        
        // Llamar para inicializar, por si hay datos cargados al inicio
        updateDomicilio();
        
        // Manejar el envío del formulario
        const form = document.getElementById('registro-form');
        if (form) {
            form.addEventListener('submit', function(event) {
                // Actualizar el domicilio una última vez antes de enviar
                updateDomicilio();
                
                // Si el domicilio está vacío, establecer un valor
                if (!domicilioInput.value.trim()) {
                    domicilioInput.value = "Dirección compilada de componentes individuales";
                }
            });
        }
    }
    
    // Otras funcionalidades del formulario pueden agregarse aquí
});