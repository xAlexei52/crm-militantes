<?php
/**
 * Clase para procesar imágenes de credenciales INE y extraer información mediante OCR
 */
class OCRProcessor {
    // API Key de OCR.space
    private $apiKey = 'K85968455988957'; // En el futuro moveremos esto a variables de entorno
    
    // Mapeo de códigos de estados INEGI a nombres de estados
    private $codigosEstados = [
        '01' => 'Aguascalientes',
        '1' => 'Aguascalientes',
        '02' => 'Baja California',
        '2' => 'Baja California',
        '03' => 'Baja California Sur',
        '3' => 'Baja California Sur',
        '04' => 'Campeche',
        '4' => 'Campeche',
        '05' => 'Coahuila',
        '5' => 'Coahuila',
        '06' => 'Colima',
        '6' => 'Colima',
        '07' => 'Chiapas',
        '7' => 'Chiapas',
        '08' => 'Chihuahua',
        '8' => 'Chihuahua',
        '09' => 'Ciudad de México',
        '9' => 'Ciudad de México',
        '10' => 'Durango',
        '11' => 'Guanajuato',
        '12' => 'Guerrero',
        '13' => 'Hidalgo',
        '14' => 'Jalisco',
        '15' => 'Estado de México',
        '16' => 'Michoacán',
        '17' => 'Morelos',
        '18' => 'Nayarit',
        '19' => 'Nuevo León',
        '20' => 'Oaxaca',
        '21' => 'Puebla',
        '22' => 'Querétaro',
        '23' => 'Quintana Roo',
        '24' => 'San Luis Potosí',
        '25' => 'Sinaloa',
        '26' => 'Sonora',
        '27' => 'Tabasco',
        '28' => 'Tamaulipas',
        '29' => 'Tlaxcala',
        '30' => 'Veracruz',
        '31' => 'Yucatán',
        '32' => 'Zacatecas'
    ];
    
    // Mapeo de códigos de municipios más comunes
    private $codigosMunicipios = [
        // Aguascalientes
        '01001' => 'Aguascalientes',
        '1001' => 'Aguascalientes',
        
        // Baja California
        '02001' => 'Mexicali',
        '2001' => 'Mexicali',
        '02002' => 'Tijuana',
        '2002' => 'Tijuana',
        
        // Baja California Sur
        '03001' => 'La Paz',
        '3001' => 'La Paz',
        
        // Campeche
        '04001' => 'Campeche',
        '4001' => 'Campeche',
        
        // Coahuila
        '05001' => 'Saltillo',
        '5001' => 'Saltillo',
        
        // Colima
        '06001' => 'Colima',
        '6001' => 'Colima',
        
        // Chiapas
        '07001' => 'Tuxtla Gutiérrez',
        '7001' => 'Tuxtla Gutiérrez',
        
        // Chihuahua
        '08001' => 'Chihuahua',
        '8001' => 'Chihuahua',
        
        // Ciudad de México
        '09001' => 'Ciudad de México',
        '9001' => 'Ciudad de México',
        
        // Durango
        '10001' => 'Durango',
        
        // Guanajuato
        '11001' => 'Guanajuato',
        
        // Guerrero
        '12001' => 'Chilpancingo',
        
        // Hidalgo
        '13001' => 'Pachuca',
        
        // Jalisco
        '14001' => 'Guadalajara',
        '14039' => 'Guadalajara',
        '14041' => 'Guadalajara',
        '14097' => 'Tlaquepaque',
        '14098' => 'Tlajomulco',
        '14101' => 'Tonalá',
        '14120' => 'Zapopan',
        
        // Estado de México
        '15001' => 'Toluca',
        
        // Michoacán
        '16001' => 'Morelia',
        
        // Morelos
        '17001' => 'Cuernavaca',
        
        // Nayarit
        '18001' => 'Tepic',
        
        // Nuevo León
        '19001' => 'Monterrey',
        
        // Oaxaca
        '20001' => 'Oaxaca',
        
        // Puebla
        '21001' => 'Puebla',
        
        // Querétaro
        '22001' => 'Querétaro',
        
        // Quintana Roo
        '23001' => 'Chetumal',
        
        // San Luis Potosí
        '24001' => 'San Luis Potosí',
        
        // Sinaloa
        '25001' => 'Culiacán',
        
        // Sonora
        '26001' => 'Hermosillo',
        
        // Tabasco
        '27001' => 'Villahermosa',
        
        // Tamaulipas
        '28001' => 'Ciudad Victoria',
        
        // Tlaxcala
        '29001' => 'Tlaxcala',
        
        // Veracruz
        '30001' => 'Xalapa',
        
        // Yucatán
        '31001' => 'Mérida',
        
        // Zacatecas
        '32001' => 'Zacatecas'
    ];
    
    /**
     * Procesa una imagen de INE y extrae información
     * 
     * @param string $imagePath Ruta del archivo de imagen temporal
     * @return array Datos extraídos de la credencial
     * @throws Exception Si ocurre un error durante el procesamiento
     */
    public function processINEImage($imagePath) {
        try {
            // Optimizar imagen para mejorar resultados del OCR
            $optimizedImagePath = $this->optimizeImage($imagePath);
            
            // Extraer texto mediante OCR
            $text = $this->performOCR($optimizedImagePath);
            
            // Guardar el texto extraído para depuración (opcional)
            $this->saveDebugText($text);
            
            // Analizar texto y extraer datos
            $datos = $this->extractDataFromText($text);
            
            // Eliminar archivo temporal optimizado si es diferente del original
            if ($optimizedImagePath != $imagePath && file_exists($optimizedImagePath)) {
                unlink($optimizedImagePath);
            }
            
            return $datos;
        } catch (Exception $e) {
            // Registrar el error para depuración
            error_log("Error en OCRProcessor::processINEImage: " . $e->getMessage());
            throw new Exception("Error procesando imagen: " . $e->getMessage());
        }
    }
    
    /**
     * Guarda el texto extraído para depuración
     */
    private function saveDebugText($text) {
        try {
            $debugDir = __DIR__ . '/../logs/ocr/';
            if (!file_exists($debugDir)) {
                mkdir($debugDir, 0755, true);
            }
            file_put_contents($debugDir . 'last_ocr_result_' . date('Ymd_His') . '.txt', $text);
        } catch (Exception $e) {
            // Si hay error al guardar, simplemente lo ignoramos - es solo para depuración
            error_log("No se pudo guardar el texto de depuración: " . $e->getMessage());
        }
    }
    
    /**
     * Optimiza una imagen para mejorar resultados del OCR
     * 
     * @param string $imagePath Ruta de la imagen original
     * @return string Ruta de la imagen optimizada
     */
    private function optimizeImage($imagePath) {
        // Verificar si tenemos GD disponible
        if (!extension_loaded('gd')) {
            return $imagePath; // Si no hay GD, devolvemos la imagen original
        }
        
        try {
            // Crear directorio temporal si no existe
            $tempDir = __DIR__ . '/../temp/';
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            // Obtener información de la imagen
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                return $imagePath; // Si no podemos obtener info, devolvemos original
            }
            
            // Nombre de archivo optimizado
            $optimizedPath = $tempDir . uniqid() . '.jpg';
            
            // Cargar imagen según su tipo
            $image = null;
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($imagePath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($imagePath);
                    break;
                default:
                    return $imagePath; // Tipo no soportado
            }
            
            if (!$image) {
                return $imagePath;
            }
            
            // Redimensionar si es demasiado grande (mejora velocidad de OCR)
            $maxWidth = 1800; // Resolución suficiente para OCR pero no demasiado grande
            $width = imagesx($image);
            $height = imagesy($image);
            
            if ($width > $maxWidth) {
                $newHeight = floor($height * ($maxWidth / $width));
                $tmpImage = imagecreatetruecolor($maxWidth, $newHeight);
                
                // Conservar transparencia si es PNG
                if ($imageInfo[2] === IMAGETYPE_PNG) {
                    imagealphablending($tmpImage, false);
                    imagesavealpha($tmpImage, true);
                }
                
                imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $tmpImage;
                $width = $maxWidth;
                $height = $newHeight;
            }
            
            // Aplicar filtros para mejorar el OCR
            // Convertir a escala de grises
            imagefilter($image, IMG_FILTER_GRAYSCALE);
            
            // Aumentar contraste (mejora reconocimiento de texto)
            imagefilter($image, IMG_FILTER_CONTRAST, -5);
            
            // Ajustar brillo ligeramente para mejorar texto oscuro
            imagefilter($image, IMG_FILTER_BRIGHTNESS, 10);
            
            // Guardar imagen optimizada
            imagejpeg($image, $optimizedPath, 90);
            imagedestroy($image);
            
            return $optimizedPath;
        } catch (Exception $e) {
            error_log("Error al optimizar imagen: " . $e->getMessage());
            return $imagePath; // En caso de error, devolver imagen original
        }
    }
    
    /**
     * Realiza OCR en una imagen usando un servicio externo o biblioteca
     * 
     * @param string $imagePath Ruta de la imagen a procesar
     * @return string Texto extraído de la imagen
     */
    private function performOCR($imagePath) {
        return $this->performOCRWithOCRSpace($imagePath);
    }
    
    /**
     * Realiza OCR utilizando la API de OCR.space con configuración optimizada
     * 
     * @param string $imagePath Ruta de la imagen
     * @return string Texto extraído
     */
    private function performOCRWithOCRSpace($imagePath) {
        // URL de la API
        $url = 'https://api.ocr.space/parse/image';
        
        // Preparar la solicitud con parámetros válidos para OCR.space
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Solo para desarrollo
        curl_setopt($ch, CURLOPT_POST, true);
        
        // Configurar parámetros según la documentación de OCR.space
        // (referencia: https://ocr.space/OCRAPI#parameters)
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'apikey' => $this->apiKey,         // API key
            'language' => 'spa',               // Español
            'isOverlayRequired' => 'true',     // Para obtener posiciones de palabras
            'file' => new CURLFile($imagePath), // Archivo a procesar
            'detectOrientation' => 'true',     // Detectar orientación automáticamente
            'scale' => 'true',                 // Escalar imagen si es necesario
            'OCREngine' => '2',                // Motor 2 para mejor reconocimiento
            'filetype' => 'AUTO',              // Detectar tipo de archivo automáticamente
            'isTable' => 'false',              // No es una tabla
            'detectCheckbox' => 'false'        // No detectar casillas de verificación
        ]);
        
        // Ejecutar curl
        $response = curl_exec($ch);
        
        // Verificar errores
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('Error cURL: ' . $error);
        }
        
        curl_close($ch);
        
        // Procesar respuesta
        $result = json_decode($response, true);
        
        // Guardar respuesta completa para depuración
        $this->saveDebugText(json_encode($result, JSON_PRETTY_PRINT));
        
        // Verificar si hubo error en la API
        if (isset($result['IsErroredOnProcessing']) && $result['IsErroredOnProcessing']) {
            // CORRECCIÓN: Manejar ErrorMessage cuando es un array
            $errorMessage = 'Error desconocido en OCR.space';
            
            if (isset($result['ErrorMessage'])) {
                if (is_array($result['ErrorMessage'])) {
                    // Convertir array a string usando json_encode
                    $errorMessage = json_encode($result['ErrorMessage']);
                } else {
                    $errorMessage = $result['ErrorMessage'];
                }
            }
            
            throw new Exception($errorMessage);
        }
        
        // Verificar si hay resultados
        if (!isset($result['ParsedResults'][0]['ParsedText'])) {
            throw new Exception('No se detectó texto en la imagen');
        }
        
        return $result['ParsedResults'][0]['ParsedText'];
    }
    
    /**
     * Extrae datos específicos del texto OCR con patrones mejorados
     * 
     * @param string $text Texto extraído de la imagen
     * @return array Datos estructurados del INE
     */
    private function extractDataFromText($text) {
        $datos = [
            'nombre' => '',
            'apellido_paterno' => '',
            'apellido_materno' => '',
            'fecha_nacimiento' => '',
            'genero' => '',
            'clave_elector' => '',
            'curp' => '',
            'folio_nacional' => '',
            'domicilio' => '',
            'calle' => '',
            'codigo_postal' => '',
            'numero_exterior' => '',
            'numero_interior' => '',
            'colonia' => '',
            'estado' => '',
            'municipio' => '',
            'seccion' => '',
            'emision' => '',
            'vigencia' => ''
        ];
        
        // Normalizar texto: eliminar saltos de línea extra, espacios, etc.
        $text = preg_replace('/\s+/', ' ', $text);
        $text = str_replace(["\r", "\n"], ' ', $text);
        $text = trim($text);
        
        // Convertir a mayúsculas para normalizar comparaciones
        $textUpper = mb_strtoupper($text, 'UTF-8');
        
        // Log del texto normalizado para depuración
        error_log("Texto normalizado para OCR: " . substr($textUpper, 0, 300) . "...");
        
        // ----- EXTRACCIÓN DE NOMBRE -----
        
        // Patrones comunes para el nombre en credenciales INE/IFE
        $nombrePatterns = [
            '/NOMBRE(?:\s+|:)([A-ZÁÉÍÓÚÜÑ\s]+)(?:DOMICILIO|DIRECCION|CALLE|SEXO|FECHA)/i',
            '/APELLIDO(?:\s+|:)([A-ZÁÉÍÓÚÜÑ\s]+)NOMBRE(?:\s+|:)([A-ZÁÉÍÓÚÜÑ\s]+)(?:DOMICILIO|DIRECCION|CALLE)/i',
            '/([A-ZÁÉÍÓÚÜÑ\s]{10,60})(?:DOMICILIO|DIRECCION|CALLE|SEXO|FECHA)/i' // Patrón genérico para texto largo antes de DOMICILIO
        ];
        
        $nombreCompleto = '';
        
        foreach ($nombrePatterns as $pattern) {
            if (preg_match($pattern, $textUpper, $matches)) {
                if (count($matches) >= 2) {
                    $nombreCompleto = trim($matches[1]);
                    // Si hay capturas adicionales (en el caso del segundo patrón)
                    if (count($matches) >= 3) {
                        $apellidos = trim($matches[1]);
                        $nombre = trim($matches[2]);
                        $nombreCompleto = $apellidos . ' ' . $nombre;
                    }
                    break;
                }
            }
        }
        
        // Si encontramos un nombre completo, intentamos separarlo en partes
        if (!empty($nombreCompleto)) {
            $partes = preg_split('/\s+/', $nombreCompleto);
            
            // Típicamente el formato es: APELLIDO_PATERNO APELLIDO_MATERNO NOMBRE(S)
            if (count($partes) >= 3) {
                $datos['apellido_paterno'] = $partes[0];
                $datos['apellido_materno'] = $partes[1];
                $datos['nombre'] = implode(' ', array_slice($partes, 2));
            } elseif (count($partes) == 2) {
                // Solo dos palabras: asumimos apellido paterno y nombre
                $datos['apellido_paterno'] = $partes[0];
                $datos['nombre'] = $partes[1];
            }
        }
        
        // ----- EXTRACCIÓN DE DOMICILIO -----
        
        // CORRECIÓN IMPORTANTE: Patrones para domicilio completo
        // Debemos evitar que "CLAVE DE ELECTOR" y lo que sigue se incluya en el campo domicilio
        
        // Primero intentamos con un patrón que se detiene explícitamente en marcadores de fin de domicilio
        $domicilioStopWords = 'CLAVE DE ELECTOR|CLAVE ELECTOR|CURP|FECHA DE REGISTRO|AÑO DE REGISTRO|ESTADO \d+|MUNICIPIO \d+';
        
        if (preg_match('/DOMICILIO(?:\s+|:)([^.]*(?:\.|JAL\.)[^.]*?)(?:' . $domicilioStopWords . ')/i', $textUpper, $matches)) {
            $datos['domicilio'] = trim($matches[1]);
        } 
        // Si no encontramos un patrón con stop words, probamos con el patrón original
        elseif (preg_match('/DOMICILIO(?:\s+|:)([A-ZÁÉÍÓÚÜÑ0-9\s\.,#-]+)(?:COLONIA|MUNICIPIO|ESTADO|LOCALIDAD|ENTIDAD|SECCION|CLAVE)/i', $textUpper, $matches)) {
            $datos['domicilio'] = trim($matches[1]);
        }
        // También buscar dirección sin el marcador "DOMICILIO"
        elseif (preg_match('/C[.]?\s+([A-ZÁÉÍÓÚÜÑ0-9\s\.,#-]+)(?:' . $domicilioStopWords . ')/i', $textUpper, $matches)) {
            $datos['domicilio'] = 'C. ' . trim($matches[1]);
        }
        
        // Limpiar el domicilio para asegurarnos de que no contiene información de CLAVE DE ELECTOR o CURP
        if (!empty($datos['domicilio'])) {
            // Si encontramos alguna de estas palabras en el domicilio, cortamos el texto en ese punto
            $cleanPatterns = [
                '/\b(?:CLAVE DE ELECTOR|CLAVE ELECTOR|CURP|FECHA DE REGISTRO|AÑO DE REGISTRO).*/i',
                '/\bESTADO\s+\d+.*/i',
                '/\bMUNICIPIO\s+\d+.*/i'
            ];
            
            foreach ($cleanPatterns as $pattern) {
                $datos['domicilio'] = preg_replace($pattern, '', $datos['domicilio']);
            }
            
            $datos['domicilio'] = trim($datos['domicilio']);
        }
        
        // ----- EXTRACCIÓN DE COMPONENTES DE DOMICILIO -----
        
        // Calle
        if (preg_match('/(?:CALLE|C[.]?)\s+([A-ZÁÉÍÓÚÜÑ0-9\s\.,#-]+?)(?:\s+(?:NUM|NÚMERO|NO|COLONIA|CP|C\.P\.|CÓDIGO|\d+))/i', $textUpper, $matches)) {
            $datos['calle'] = trim($matches[1]);
        }
        
        // Intentar extraer código postal (5 dígitos seguidos típicamente)
        preg_match_all('/\b(\d{5})\b/', $textUpper, $cpMatches);
        if (!empty($cpMatches[1])) {
            $datos['codigo_postal'] = $cpMatches[1][0]; // Tomamos el primer código postal que encontremos
        }
        
        // Número exterior - Buscar después de la calle o en el domicilio completo
        if (preg_match('/(?:NUM|NÚMERO|NO)(?:\.|\s+)(?:EXT|EXTERIOR)\s+([0-9A-Z\s\.-]+)/i', $textUpper, $matches)) {
            $datos['numero_exterior'] = trim($matches[1]);
        } elseif (preg_match('/\b(\d+)\b/', $textUpper, $matches)) {
            // Si no encontramos un patrón específico, buscamos números en el texto
            $datos['numero_exterior'] = $matches[1];
        }
        
        // Número interior - Buscar después del exterior o patrones específicos
        if (preg_match('/(?:INT|INTERIOR|DEPTO|DEPARTAMENTO)\s+([0-9A-Z\s\.-]+)/i', $textUpper, $matches)) {
            $datos['numero_interior'] = trim($matches[1]);
        } elseif (preg_match('/\b([A-Z]-\d+|\b[A-Z]\s+\d+)\b/i', $textUpper, $matches)) {
            // Buscar patrones como "B-34" o "B 34"
            $datos['numero_interior'] = $matches[1];
        }
        
        // Colonia
        if (preg_match('/(?:COL|COLONIA)(?:\.|\s+|:)\s+([A-ZÁÉÍÓÚÜÑ0-9\s\.,#-]+?)(?:\s+(?:CP|C\.P\.|MUNICIPIO|LOCALIDAD|ENTIDAD|CIUDAD|\d{5}))/i', $textUpper, $matches)) {
            $datos['colonia'] = trim($matches[1]);
        }
        
        // ----- EXTRACCIÓN DE CLAVE DE ELECTOR -----
        
        // Patrones para clave de elector (típicamente 18 caracteres alfanuméricos)
        if (preg_match('/CLAVE(?:\s+DE)?\s+ELECTOR(?:\s+|:)\s*([A-Z0-9]{18})/i', $textUpper, $matches) || 
            preg_match('/([A-Z]{6}[0-9]{8}[HM][A-Z]{3})/i', $textUpper, $matches)) {
            $datos['clave_elector'] = trim($matches[1]);
        } else {
            // Búsqueda secundaria: buscar cualquier secuencia que parezca clave de elector
            preg_match_all('/[A-Z]{6}[0-9]{8}[HM][A-Z]{3}/i', $textUpper, $matches);
            if (!empty($matches[0])) {
                $datos['clave_elector'] = $matches[0][0];
            }
        }
        
        // ----- EXTRACCIÓN DE CURP -----
        
        // CURP: 18 caracteres específicos
        if (preg_match('/CURP(?:\s+|:)\s*([A-Z0-9]{18})/i', $textUpper, $matches)) {
            $datos['curp'] = trim($matches[1]);
        } else {
            // Búsqueda de patrón CURP en cualquier parte del texto
            preg_match_all('/[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9]{2}/i', $textUpper, $matches);
            if (!empty($matches[0])) {
                $datos['curp'] = $matches[0][0];
            }
        }
        
        // ----- EXTRACCIÓN DE FECHA DE NACIMIENTO -----
        
        // Patrones para fechas en formato DD/MM/AAAA
        if (preg_match('/FECHA\s+DE\s+NACIMIENTO(?:\s+|:)(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})/i', $textUpper, $matches) || 
            preg_match('/NACIMIENTO(?:\s+|:)(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})/i', $textUpper, $matches)) {
            // Formatear fecha correctamente
            $dia = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $mes = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $año = $matches[3];
            $datos['fecha_nacimiento'] = "$dia/$mes/$año";
        } else {
            // Búsqueda genérica de fechas
            preg_match_all('/(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})/i', $textUpper, $matches);
            if (!empty($matches[0])) {
                // Intentar determinar si es fecha de nacimiento (no de emisión/expiración)
                foreach ($matches[0] as $index => $fechaStr) {
                    $diaMatch = $matches[1][$index];
                    $mesMatch = $matches[2][$index];
                    $añoMatch = $matches[3][$index];
                    
                    // Verificar si es una fecha válida
                    if (checkdate(intval($mesMatch), intval($diaMatch), intval($añoMatch))) {
                        // Si el año es < 2010, probablemente es fecha de nacimiento
                        if (intval($añoMatch) < 2010) {
                            $dia = str_pad($diaMatch, 2, '0', STR_PAD_LEFT);
                            $mes = str_pad($mesMatch, 2, '0', STR_PAD_LEFT);
                            $datos['fecha_nacimiento'] = "$dia/$mes/$añoMatch";
                            break;
                        }
                    }
                }
            }
        }
        
        // ----- EXTRACCIÓN DE SEXO/GÉNERO -----
        
        if (preg_match('/SEXO(?:\s+|:)\s*([HM])/i', $textUpper, $matches)) {
            $sexo = strtoupper($matches[1]);
            $datos['genero'] = ($sexo == 'H') ? 'M' : 'F';
        } else if (preg_match('/SEXO(?:\s+|:)\s*(HOMBRE|MUJER)/i', $textUpper, $matches)) {
            $sexoTexto = strtoupper($matches[1]);
            $datos['genero'] = ($sexoTexto == 'HOMBRE') ? 'M' : 'F';
        }
        
        // ----- EXTRACCIÓN DE FOLIO NACIONAL -----
        
        if (preg_match('/(?:FOLIO|NO\.\s*FOLIO|NO\.FOLIO)(?:\s+|:)([0-9]{10,20})/i', $textUpper, $matches)) {
            $datos['folio_nacional'] = trim($matches[1]);
        } else if (preg_match('/FOLIO(?:\s+NACIONAL)?(?:\s+|:)([0-9]{10,20})/i', $textUpper, $matches)) {
            $datos['folio_nacional'] = trim($matches[1]);
        }
        
        // ----- EXTRACCIÓN DE MUNICIPIO Y ESTADO -----
        
        // Buscar códigos INEGI para estado y municipio
        $estadoCode = null;
        $municipioCode = null;
        
        // Buscar patrón "ESTADO 14" o similar
        if (preg_match('/ESTADO\s+(\d{1,2})/i', $textUpper, $matches)) {
            $estadoCode = $matches[1];
            // Verificar si tenemos un mapeo para este código
            if (isset($this->codigosEstados[$estadoCode])) {
                $datos['estado'] = $this->codigosEstados[$estadoCode];
            }
        }
        
        // Buscar patrón "MUNICIPIO 041" o similar
        if (preg_match('/MUNICIPIO\s+(\d{1,3})/i', $textUpper, $matches)) {
            $municipioCode = $matches[1];
            
            // Si tenemos estado, intentar construir un código completo
            if ($estadoCode) {
                $codigoCompleto = str_pad($estadoCode, 2, '0', STR_PAD_LEFT) . str_pad($municipioCode, 3, '0', STR_PAD_LEFT);
                if (isset($this->codigosMunicipios[$codigoCompleto])) {
                    $datos['municipio'] = $this->codigosMunicipios[$codigoCompleto];
                }
            }
            
            // Casos específicos comunes
            if ($municipioCode == '041' || $municipioCode == '039') {
                if (empty($datos['estado']) || $datos['estado'] == 'Jalisco') {
                    $datos['municipio'] = 'GUADALAJARA';
                }
            }
        }
        
        // Si no encontramos mediante códigos, intentamos con patrones de texto
        if (empty($datos['municipio'])) {
            if (preg_match('/MUNICIPIO(?:\s+|:)([A-ZÁÉÍÓÚÜÑ\s\.,-]+)(?:LOCALIDAD|ESTADO|ENTIDAD|SECCION|CLAVE)/i', $textUpper, $matches)) {
                $datos['municipio'] = trim($matches[1]);
            } elseif (strpos($textUpper, 'GUADALAJARA') !== false) {
                $datos['municipio'] = 'GUADALAJARA';
            }
        }
        
        // Estado - si no se encontró mediante código, intentar con texto
        if (empty($datos['estado'])) {
            if (preg_match('/(?:ESTADO|ENTIDAD)(?:\s+|:)([A-ZÁÉÍÓÚÜÑ\s\.,-]+)(?:MUNICIPIO|LOCALIDAD|SECCION|CLAVE)/i', $textUpper, $matches)) {
                $estadoTexto = trim($matches[1]);
                $datos['estado'] = $this->normalizarEstado($estadoTexto);
            } else {
                // Buscar coincidencias con la lista de estados
                $estadosPatron = implode('|', $this->getListaEstados());
                if (preg_match('/\b(' . $estadosPatron . ')\b/i', $textUpper, $matches)) {
                    $datos['estado'] = $this->normalizarEstado($matches[1]);
                }
            }
        }
        
        // Si encontramos "JAL" asumimos Jalisco
        if (empty($datos['estado']) && (strpos($textUpper, 'JAL.') !== false || strpos($textUpper, 'JAL ') !== false)) {
            $datos['estado'] = 'Jalisco';
        }
        
        // ----- EXTRACCIÓN DE SECCIÓN ELECTORAL -----
        
        // Patrones para la sección electoral
        if (preg_match('/SECCION(?:\s+|:)(\d{1,4})/i', $textUpper, $matches) || 
            preg_match('/SECC(?:\.|\s+|:)(\d{1,4})/i', $textUpper, $matches)) {
            $datos['seccion'] = trim($matches[1]);
        }
        
        // ----- EXTRACCIÓN DE EMISIÓN Y VIGENCIA -----
        
        // Emisión
        if (preg_match('/EMISION(?:\s+|:)(\d{4})/i', $textUpper, $matches)) {
            $datos['emision'] = trim($matches[1]);
        }
        
        // Vigencia
        if (preg_match('/VIGENCIA(?:\s+|:)(\d{4})/i', $textUpper, $matches)) {
            $datos['vigencia'] = trim($matches[1]);
        }
        
        // ----- VALIDACIONES Y LIMPIEZA FINAL -----
        
        // Limpieza y validación final de todos los campos
        foreach ($datos as $campo => $valor) {
            // Eliminar caracteres no deseados
            $datos[$campo] = preg_replace('/[^\p{L}\p{N}\s\/.,#-]/u', '', trim($valor));
        }
        
        return $datos;
    }
    
    /**
     * Normaliza el nombre del estado a formato estándar
     * 
     * @param string $estadoTexto Texto del estado extraído
     * @return string Nombre normalizado del estado
     */
    private function normalizarEstado($estadoTexto) {
        $estadoTexto = trim($estadoTexto);
        
        // Mapa de normalización
        $mapaEstados = [
            'AGUASCALIENTES' => 'Aguascalientes',
            'AGS' => 'Aguascalientes',
            'BAJA CALIFORNIA' => 'Baja California',
            'BC' => 'Baja California',
            'BAJA CALIFORNIA SUR' => 'Baja California Sur',
            'BCS' => 'Baja California Sur',
            'CAMPECHE' => 'Campeche',
            'CAMP' => 'Campeche',
            'CHIAPAS' => 'Chiapas',
            'CHIS' => 'Chiapas',
            'CHIHUAHUA' => 'Chihuahua',
            'CHIH' => 'Chihuahua',
            'CIUDAD DE MEXICO' => 'Ciudad de México',
            'CDMX' => 'Ciudad de México',
            'DF' => 'Ciudad de México',
            'DISTRITO FEDERAL' => 'Ciudad de México',
            'COAHUILA' => 'Coahuila',
            'COAH' => 'Coahuila',
            'COLIMA' => 'Colima',
            'COL' => 'Colima',
            'DURANGO' => 'Durango',
            'DGO' => 'Durango',
            'ESTADO DE MEXICO' => 'Estado de México',
            'EDO MEX' => 'Estado de México',
            'EDOMEX' => 'Estado de México',
            'MEXICO' => 'Estado de México',
            'MEX' => 'Estado de México',
            'GUANAJUATO' => 'Guanajuato',
            'GTO' => 'Guanajuato',
            'GUERRERO' => 'Guerrero',
            'GRO' => 'Guerrero',
            'HIDALGO' => 'Hidalgo',
            'HGO' => 'Hidalgo',
            'JALISCO' => 'Jalisco',
            'JAL' => 'Jalisco',
            'MICHOACAN' => 'Michoacán',
            'MICH' => 'Michoacán',
            'MORELOS' => 'Morelos',
            'MOR' => 'Morelos',
            'NAYARIT' => 'Nayarit',
            'NAY' => 'Nayarit',
            'NUEVO LEON' => 'Nuevo León',
            'NL' => 'Nuevo León',
            'OAXACA' => 'Oaxaca',
            'OAX' => 'Oaxaca',
            'PUEBLA' => 'Puebla',
            'PUE' => 'Puebla',
            'QUERETARO' => 'Querétaro',
            'QRO' => 'Querétaro',
            'QUINTANA ROO' => 'Quintana Roo',
            'QROO' => 'Quintana Roo',
            'SAN LUIS POTOSI' => 'San Luis Potosí',
            'SLP' => 'San Luis Potosí',
            'SINALOA' => 'Sinaloa',
            'SIN' => 'Sinaloa',
            'SONORA' => 'Sonora',
            'SON' => 'Sonora',
            'TABASCO' => 'Tabasco',
            'TAB' => 'Tabasco',
            'TAMAULIPAS' => 'Tamaulipas',
            'TAMPS' => 'Tamaulipas',
            'TLAXCALA' => 'Tlaxcala',
            'TLAX' => 'Tlaxcala',
            'VERACRUZ' => 'Veracruz',
            'VER' => 'Veracruz',
            'YUCATAN' => 'Yucatán',
            'YUC' => 'Yucatán',
            'ZACATECAS' => 'Zacatecas',
            'ZAC' => 'Zacatecas'
        ];
        
        // Normalizar texto para comparación
        $estadoNormalizado = mb_strtoupper($estadoTexto, 'UTF-8');
        $estadoNormalizado = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'], ['A', 'E', 'I', 'O', 'U', 'U', 'N'], $estadoNormalizado);
        
        // Buscar coincidencia en el mapa
        foreach ($mapaEstados as $clave => $valor) {
            $claveNormalizada = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'], ['A', 'E', 'I', 'O', 'U', 'U', 'N'], $clave);
            if (strpos($estadoNormalizado, $claveNormalizada) !== false) {
                return $valor;
            }
        }
        
        // Si no hay coincidencia, devolver el texto original
        return $estadoTexto;
    }
    
    /**
     * Obtiene la lista de estados de México para reconocimiento
     * 
     * @return array Lista de estados
     */
    private function getListaEstados() {
        return [
            'AGUASCALIENTES', 'AGS',
            'BAJA CALIFORNIA', 'BC',
            'BAJA CALIFORNIA SUR', 'BCS',
            'CAMPECHE', 'CAMP',
            'CHIAPAS', 'CHIS',
            'CHIHUAHUA', 'CHIH',
            'CIUDAD DE MEXICO', 'CDMX', 'DF', 'DISTRITO FEDERAL',
            'COAHUILA', 'COAH',
            'COLIMA', 'COL',
            'DURANGO', 'DGO',
            'ESTADO DE MEXICO', 'EDO MEX', 'EDOMEX', 'MEXICO', 'MEX',
            'GUANAJUATO', 'GTO',
            'GUERRERO', 'GRO',
            'HIDALGO', 'HGO',
            'JALISCO', 'JAL',
            'MICHOACAN', 'MICH',
            'MORELOS', 'MOR',
            'NAYARIT', 'NAY',
            'NUEVO LEON', 'NL',
            'OAXACA', 'OAX',
            'PUEBLA', 'PUE',
            'QUERETARO', 'QRO',
            'QUINTANA ROO', 'QROO',
            'SAN LUIS POTOSI', 'SLP',
            'SINALOA', 'SIN',
            'SONORA', 'SON',
            'TABASCO', 'TAB',
            'TAMAULIPAS', 'TAMPS',
            'TLAXCALA', 'TLAX',
            'VERACRUZ', 'VER',
            'YUCATAN', 'YUC',
            'ZACATECAS', 'ZAC'
        ];
    }
}
?>