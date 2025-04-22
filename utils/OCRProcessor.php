<?php
/**
 * Clase para procesar imágenes de credenciales INE y extraer información mediante OCR
 */
class OCRProcessor {
    
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
            
            // Analizar texto y extraer datos
            $datos = $this->extractDataFromText($text);
            
            // Eliminar archivo temporal optimizado si es diferente del original
            if ($optimizedImagePath != $imagePath && file_exists($optimizedImagePath)) {
                unlink($optimizedImagePath);
            }
            
            return $datos;
        } catch (Exception $e) {
            throw new Exception("Error procesando imagen: " . $e->getMessage());
        }
    }
    
    /**
     * Optimiza una imagen para mejorar resultados del OCR
     * 
     * @param string $imagePath Ruta de la imagen original
     * @return string Ruta de la imagen optimizada
     */
    private function optimizeImage($imagePath) {
        // Si tienes GD o ImageMagick, puedes usarlos para optimizar la imagen
        // Por ahora, simplemente devolvemos la misma ruta
        return $imagePath;
        
        /* Si prefieres implementar la optimización, descomenta este código:
        
        // Crear directorio temporal si no existe
        $tempDir = __DIR__ . '/../temp/';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Nombre de archivo optimizado
        $optimizedPath = $tempDir . uniqid() . '.jpg';
        
        // Cargar imagen
        $image = imagecreatefromjpeg($imagePath);
        
        // Convertir a escala de grises para mejorar OCR
        imagefilter($image, IMG_FILTER_GRAYSCALE);
        
        // Ajustar contraste
        imagefilter($image, IMG_FILTER_CONTRAST, -10);
        
        // Guardar imagen optimizada
        imagejpeg($image, $optimizedPath, 90);
        imagedestroy($image);
        
        return $optimizedPath;
        */
    }
    
    /**
     * Realiza OCR en una imagen usando un servicio externo o biblioteca
     * 
     * @param string $imagePath Ruta de la imagen a procesar
     * @return string Texto extraído de la imagen
     */
    private function performOCR($imagePath) {
        // OPCIÓN 1: Usar OCR.space API (requiere registro gratuito)
        return $this->performOCRWithOCRSpace($imagePath);
        
        // OPCIÓN 2: Si tienes Tesseract instalado en el servidor, puedes usar:
        // return $this->performOCRWithTesseract($imagePath);
    }
    
    /**
     * Realiza OCR utilizando la API de OCR.space
     * 
     * @param string $imagePath Ruta de la imagen
     * @return string Texto extraído
     */
    private function performOCRWithOCRSpace($imagePath) {
        // Obten tu API Key en https://ocr.space/ocrapi (tienen un plan gratuito)
        $apiKey = 'K85968455988957';
        
        // Preparar la solicitud
        $url = 'https://api.ocr.space/parse/image';
        
        // Crear curl post request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'apikey' => $apiKey,
            'language' => 'spa',
            'isOverlayRequired' => 'false',
            'file' => new CURLFile($imagePath),
            'detectOrientation' => 'true',
            'scale' => 'true',
            'OCREngine' => '2'  // Motor 2 para mejor reconocimiento
        ]);
        
        // Ejecutar curl
        $response = curl_exec($ch);
        
        // Verificar errores
        if (curl_errno($ch)) {
            throw new Exception('Error cURL: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        // Procesar respuesta
        $result = json_decode($response, true);
        
        if (!isset($result['ParsedResults'][0]['ParsedText'])) {
            throw new Exception('Error en la respuesta del OCR: ' . json_encode($result));
        }
        
        return $result['ParsedResults'][0]['ParsedText'];
    }
    
    /**
     * Realiza OCR utilizando Tesseract (si está instalado en el servidor)
     * 
     * @param string $imagePath Ruta de la imagen
     * @return string Texto extraído
     */
    private function performOCRWithTesseract($imagePath) {
        // Verificar si Tesseract está disponible
        exec('which tesseract', $output, $returnVar);
        if ($returnVar !== 0) {
            throw new Exception('Tesseract OCR no está instalado en el servidor');
        }
        
        // Nombre de archivo temporal para la salida
        $outputFile = tempnam(sys_get_temp_dir(), 'ocr');
        
        // Ejecutar Tesseract (configurado para español)
        $command = "tesseract $imagePath $outputFile -l spa";
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new Exception('Error ejecutando Tesseract OCR');
        }
        
        // Leer el texto resultante
        $text = file_get_contents($outputFile . '.txt');
        
        // Limpiar archivos temporales
        if (file_exists($outputFile)) unlink($outputFile);
        if (file_exists($outputFile . '.txt')) unlink($outputFile . '.txt');
        
        return $text;
    }
    
    /**
     * Extrae datos específicos del texto OCR
     * 
     * @param string $text Texto extraído de la imagen
     * @return array Datos estructurados (nombre, CURP, clave elector, etc.)
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
            'domicilio' => '',
            'estado' => '',
            'municipio' => '',
            'seccion' => ''
        ];
        
        // Normalizar texto: eliminar saltos de línea extra, espacios, etc.
        $text = preg_replace('/\s+/', ' ', $text);
        $text = str_replace(["\r", "\n"], ' ', $text);
        $text = trim($text);
        
        // Extraer nombre
        // El formato típico es "NOMBRE APELLIDO1 APELLIDO2"
        if (preg_match('/NOMBRE\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+DOMICILIO/i', $text, $matches) || 
            preg_match('/NOMBRE\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+CLAVE/i', $text, $matches) ||
            preg_match('/NOMBRE\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+FECHA/i', $text, $matches)) {
            
            $nombreCompleto = trim($matches[1]);
            $partes = explode(' ', $nombreCompleto);
            
            // Si tiene al menos 2 palabras, asumimos que la última es apellido materno
            // la penúltima es apellido paterno, y el resto es el nombre
            if (count($partes) >= 3) {
                $datos['apellido_materno'] = array_pop($partes);
                $datos['apellido_paterno'] = array_pop($partes);
                $datos['nombre'] = implode(' ', $partes);
            } elseif (count($partes) == 2) {
                // Si solo hay 2 palabras, asumimos nombre y apellido paterno
                $datos['apellido_paterno'] = array_pop($partes);
                $datos['nombre'] = implode(' ', $partes);
            } else {
                // Si solo hay una palabra, asumimos que es el nombre
                $datos['nombre'] = $nombreCompleto;
            }
        }
        
        // Extraer clave de elector (formato típico: ABCD123456HDFGHI12)
        if (preg_match('/CLAVE\s+DE\s+ELECTOR\s+([A-Z0-9]{18})/i', $text, $matches) ||
            preg_match('/ELECTOR\s+([A-Z0-9]{18})/i', $text, $matches)) {
            $datos['clave_elector'] = $matches[1];
        }
        
        // Extraer CURP (formato: ABCD123456HDFGHI12)
        if (preg_match('/CURP\s+([A-Z0-9]{18})/i', $text, $matches)) {
            $datos['curp'] = $matches[1];
        }
        
        // Extraer fecha de nacimiento (formato: DD/MM/AAAA)
        if (preg_match('/FECHA\s+DE\s+NACIMIENTO\s+(\d{2}\/\d{2}\/\d{4})/i', $text, $matches) ||
            preg_match('/NACIMIENTO\s+(\d{2}\/\d{2}\/\d{4})/i', $text, $matches)) {
            $datos['fecha_nacimiento'] = $matches[1];
        }
        
        // Extraer género
        if (preg_match('/SEXO\s+([HM])/i', $text, $matches) ||
            preg_match('/GÉNERO\s+([HM])/i', $text, $matches)) {
            $genero = strtoupper($matches[1]);
            $datos['genero'] = ($genero == 'H') ? 'M' : 'F'; // Convertir H/M a M/F
        }
        
        // Extraer estado
        if (preg_match('/ESTADO\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+MUNICIPIO/i', $text, $matches) ||
            preg_match('/ESTADO\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+LOCALIDAD/i', $text, $matches)) {
            $datos['estado'] = trim($matches[1]);
        }
        
        // Extraer municipio
        if (preg_match('/MUNICIPIO\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+SECCIÓN/i', $text, $matches) ||
            preg_match('/MUNICIPIO\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+SECCION/i', $text, $matches) ||
            preg_match('/MUNICIPIO\s+([A-ZÁÉÍÓÚÜÑ\s]+)\s+LOCALIDAD/i', $text, $matches)) {
            $datos['municipio'] = trim($matches[1]);
        }
        
        // Extraer sección
        if (preg_match('/SECCIÓN\s+(\d+)/i', $text, $matches) ||
            preg_match('/SECCION\s+(\d+)/i', $text, $matches)) {
            $datos['seccion'] = $matches[1];
        }
        
        // Extraer domicilio
        if (preg_match('/DOMICILIO\s+([A-ZÁÉÍÓÚÜÑ0-9\s\.,#]+)\s+COLONIA/i', $text, $matches) ||
            preg_match('/DOMICILIO\s+([A-ZÁÉÍÓÚÜÑ0-9\s\.,#]+)\s+ESTADO/i', $text, $matches)) {
            $datos['domicilio'] = trim($matches[1]);
        }
        
        return $datos;
    }
}
?>