document.addEventListener('DOMContentLoaded', function() {
    // Obtener la URL base del sistema desde una variable global
    // que debemos definir en la vista
    const APP_URL = window.appUrl || '';
    
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('ine-upload');
    const loadingIndicator = document.getElementById('loading-indicator');
    const ocrResult = document.getElementById('ocr-result');
    const ocrError = document.getElementById('ocr-error');
    
    // Manejar click en el dropzone
    if (dropzone) {
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });
    }
    
    // Manejar cambio en el input de archivo
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                processINEImage(fileInput.files[0]);
            }
        });
    }
    
    // Manejar eventos de drag and drop
    if (dropzone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, unhighlight, false);
        });
        
        // Manejar evento de soltar archivo
        dropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                processINEImage(files[0]);
            }
        });
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight() {
        dropzone.classList.add('active');
    }
    
    function unhighlight() {
        dropzone.classList.remove('active');
    }
    
    // Procesar imagen del INE usando el servidor
    async function processINEImage(file) {
        // Validar tipo de archivo
        if (!file.type.match('image.*')) {
            alert('Por favor selecciona una imagen válida.');
            return;
        }
        
        // Validar tamaño de archivo (máx. 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('La imagen es demasiado grande. El tamaño máximo es 10MB.');
            return;
        }
        
        // Mostrar indicador de carga
        if (loadingIndicator) {
            loadingIndicator.classList.remove('hidden');
            const loadingText = loadingIndicator.querySelector('p');
            if (loadingText) {
                loadingText.textContent = 'Procesando tu credencial...';
            }
        }
        
        if (ocrResult) ocrResult.classList.add('hidden');
        if (ocrError) ocrError.classList.add('hidden');
        
        try {
            // Primero subimos la imagen para tenerla guardada
            const imagePath = await uploadImageToServer(file);
            
            // Luego ejecutamos el OCR en el servidor
            const datos = await processOCROnServer(file);
            
            if (datos) {
                console.log('Datos extraídos:', datos);
                
                // Rellenar el formulario con los datos extraídos
                fillFormWithData(datos);
                
                // Guardar la ruta de la imagen
                if (imagePath) {
                    let hiddenInput = document.getElementById('imagen_ine_path');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.id = 'imagen_ine_path';
                        hiddenInput.name = 'imagen_ine_path';
                        const form = document.getElementById('registro-form');
                        if (form) {
                            form.appendChild(hiddenInput);
                        }
                    }
                    hiddenInput.value = imagePath;
                }
                
                // Mostrar mensaje de éxito
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
                
                if (hasExtractedData(datos)) {
                    if (ocrResult) {
                        ocrResult.classList.remove('hidden');
                        const resultText = ocrResult.querySelector('span');
                        if (resultText) {
                            resultText.textContent = 'Información extraída correctamente. Algunos campos han sido prellenados. Por favor verifica y completa los datos faltantes.';
                        }
                    }
                } else {
                    if (ocrError) {
                        ocrError.classList.remove('hidden');
                        const errorText = ocrError.querySelector('span');
                        if (errorText) {
                            errorText.textContent = 'No se pudo extraer información automáticamente. Por favor, llena el formulario manualmente.';
                        }
                    }
                }
            } else {
                throw new Error('No se recibieron datos del servidor');
            }
        } catch (error) {
            console.error('Error en OCR:', error);
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            
            if (ocrError) {
                ocrError.classList.remove('hidden');
                const errorText = ocrError.querySelector('span');
                if (errorText) {
                    errorText.textContent = `Error: ${error.message}. Por favor, intenta con otra imagen o llena el formulario manualmente.`;
                }
            }
        }
    }
    
    // Subir imagen al servidor y devolver la ruta como promesa
    async function uploadImageToServer(file) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('ine_image', file);
            
            fetch(APP_URL + '/register/upload-ine', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error HTTP: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.image_path) {
                    resolve(data.image_path);
                } else {
                    reject(new Error(data.error || 'Error desconocido al subir la imagen'));
                }
            })
            .catch(error => {
                console.error('Error al subir la imagen:', error);
                reject(error);
            });
        });
    }
    
    // Procesar OCR en el servidor y devolver los datos como promesa
    async function processOCROnServer(file) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('ine_image', file);
            
            fetch(APP_URL + '/register/process-ine', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error HTTP: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.datos) {
                    resolve(data.datos);
                } else {
                    reject(new Error(data.error || 'Error desconocido al procesar la imagen'));
                }
            })
            .catch(error => {
                console.error('Error al procesar OCR:', error);
                reject(error);
            });
        });
    }
    
    // Verificar si se extrajo al menos un dato relevante
    function hasExtractedData(datos) {
        if (!datos) return false;
        
        // Verificamos especialmente los campos que mencionaste
        const camposRelevantes = [
            'nombre', 'apellido_paterno', 'apellido_materno',
            'domicilio', 'calle', 'colonia', 
            'clave_elector', 'curp', 
            'estado', 'municipio', 'seccion',
            'genero', 'fecha_nacimiento'
        ];
        
        return camposRelevantes.some(campo => datos[campo] && datos[campo].trim !== '' && datos[campo].trim);
    }
    
    // Rellenar el formulario con los datos extraídos
    function fillFormWithData(datos) {
        if (!datos) return;
        
        console.log('Rellenando formulario con:', datos);
        
        // Mapeo de campos del OCR a campos del formulario - prioridad a los que mencionaste
        const camposFormulario = {
            // Datos personales prioritarios
            'nombre': 'nombre',
            'apellido_paterno': 'apellido_paterno',
            'apellido_materno': 'apellido_materno',
            'fecha_nacimiento': 'fecha_nacimiento',
            'genero': 'genero',
            'clave_elector': 'clave_elector',
            'curp': 'curp',
            

            'codigo_postal': 'codigo_postal',
            
            // Ubicación
            'estado': 'estado',
            'municipio': 'municipio', // Equivalente a localidad
            'seccion': 'seccion',
            
            // Otros campos
            'folio_nacional': 'folio_nacional'
        };
        
        // Rellenar campos de texto e inputs
        Object.entries(camposFormulario).forEach(([campoDatos, campoFormulario]) => {
            const elemento = document.getElementById(campoFormulario);
            if (elemento && datos[campoDatos]) {
                const valor = datos[campoDatos].trim();
                
                // Manejo especial para fecha de nacimiento
                if (campoFormulario === 'fecha_nacimiento' && valor) {
                    // Si la fecha está en formato DD/MM/AAAA, convertirla a AAAA-MM-DD para el input date
                    if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(valor)) {
                        const partes = valor.split('/');
                        const fechaFormateada = `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
                        elemento.value = fechaFormateada;
                    } else {
                        // Si ya está en formato AAAA-MM-DD (desde el servidor)
                        elemento.value = valor;
                    }
                } else {
                    elemento.value = valor;
                }
            }
        });
        
        // Manejar selects (género y estado)
        if (datos.genero) {
            const generoSelect = document.getElementById('genero');
            if (generoSelect) {
                for (let i = 0; i < generoSelect.options.length; i++) {
                    if (generoSelect.options[i].value === datos.genero) {
                        generoSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }
        
        if (datos.estado) {
            const estadoSelect = document.getElementById('estado');
            if (estadoSelect) {
                const estadoNormalizado = normalizarTexto(datos.estado);
                
                for (let i = 0; i < estadoSelect.options.length; i++) {
                    const optionText = estadoSelect.options[i].text;
                    const optionValue = estadoSelect.options[i].value;
                    
                    // Normalizar para comparación
                    const optionNormalizado = normalizarTexto(optionText);
                    
                    if (optionNormalizado === estadoNormalizado || 
                        optionNormalizado.includes(estadoNormalizado) || 
                        estadoNormalizado.includes(optionNormalizado)) {
                        estadoSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }
        
        console.log('Formulario rellenado con los siguientes campos:');
        Object.entries(camposFormulario).forEach(([campoDatos, campoFormulario]) => {
            if (datos[campoDatos] && datos[campoDatos].trim && datos[campoDatos].trim() !== '') {
                console.log(`- ${campoFormulario}: ${datos[campoDatos]}`);
            }
        });
    }
    
    // Función para normalizar texto (quitar acentos, espacios extra, etc.)
    function normalizarTexto(texto) {
        if (!texto) return '';
        
        // Convertir a minúsculas
        let normalizado = texto.toLowerCase();
        
        // Eliminar acentos
        normalizado = normalizado.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        
        // Eliminar espacios extra
        normalizado = normalizado.trim().replace(/\s+/g, ' ');
        
        return normalizado;
    }
});