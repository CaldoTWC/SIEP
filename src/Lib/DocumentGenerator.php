<?php
// Archivo: src/Lib/DocumentGenerator.php
// Esta librería contiene servicios para la creación de documentos PDF.

// Incluimos la biblioteca FPDF desde la carpeta vendor.
require_once(__DIR__ . '/../../vendor/fpdf/fpdf.php');
require_once(__DIR__ . '/../../vendor/fpdi/src/autoload.php');
use setasign\Fpdi\Fpdi; // Usamos el namespace de FPDI

/**
 * Clase de servicio para generar documentos específicos del proyecto.
 */

class DocumentService {
    
    /**
     * Genera la Carta de Presentación en PDF basada en la plantilla oficial.
     * Este método es versátil: puede enviar el PDF al navegador o devolverlo como una cadena de texto.
     *
     * @param array $student_data - Un array con los datos del estudiante (full_name, boleta, career, percentage_progress).
     * @param bool $returnAsString - Si es true, el método devuelve el contenido del PDF como una cadena.
     *                             Si es false (por defecto), envía el PDF al navegador para su visualización.
     * @return string|void - Devuelve el contenido del PDF si $returnAsString es true, de lo contrario no devuelve nada.
     */
    
/**
 * Genera la Carta de Presentación en PDF basada en la plantilla oficial.
 * VERSIÓN 2.1: Ajustes de formato y posicionamiento mejorados
 *
 * @param array $student_data - Datos del estudiante y configuración
 * @param string $letter_number - Número de oficio (ej: "No. 01-2025/2")
 * @param bool $returnAsString - Si es true, devuelve el PDF como string
 * @return string|void
 */
public function generatePresentationLetter(array $student_data, $letter_number = 'No. 00-2025/2', bool $returnAsString = false) {
    
    $pdf = new Fpdi('P', 'mm', 'Letter');
    $pdf->AddPage();
    
    // --- Determinar qué plantilla usar ---
    $has_recipient = isset($student_data['has_specific_recipient']) ? (bool)$student_data['has_specific_recipient'] : false;
    $requires_hours = isset($student_data['requires_hours']) ? (bool)$student_data['requires_hours'] : false;
    
    // Determinar tipo de plantilla
    if ($has_recipient && $requires_hours) {
        $template_type = 'destinatario_horas';
    } elseif ($has_recipient && !$requires_hours) {
        $template_type = 'destinatario';
    } elseif (!$has_recipient && $requires_hours) {
        $template_type = 'normal_horas';
    } else {
        $template_type = 'normal';
    }
    
    // Si viene especificado en student_data, usar ese
    if (isset($student_data['letter_template_type'])) {
        $template_type = $student_data['letter_template_type'];
    }
    
    // --- Obtener ruta de la plantilla desde la BD ---
    require_once(__DIR__ . '/../Models/LetterTemplate.php');
    $templateModel = new LetterTemplate();
    $template_info = $templateModel->getTemplateByType($template_type);
    
    if (!$template_info) {
        die('Error: No se encontró la plantilla del tipo: ' . $template_type);
    }
    
    $templatePath = __DIR__ . '/../../' . $template_info['template_file_path'];
    
    if (!file_exists($templatePath)) {
        die('Error: No se encontró el archivo de plantilla: ' . $templatePath);
    }
    
    $pdf->setSourceFile($templatePath);
    $templateId = $pdf->importPage(1);
    $pdf->useTemplate($templateId, 0, 0, 215.9, 279.4);

    // --- INICIO DEL POSICIONAMIENTO DEL TEXTO ---
    
    // --- Bloque de Asunto y Número de Oficio ---
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetXY(20, 50);
    $pdf->Cell(0, 7, utf8_decode('Asunto: Carta de presentación'), 0, 1, 'L');
    $pdf->SetX(20);
    $pdf->Cell(0, 7, utf8_decode($letter_number), 0, 1, 'L');
    
    // --- Destinatario ---
    $pdf->SetXY(20, 70); // Ajustado para dar más espacio
    $pdf->SetFont('Arial', 'B', 11);
    
    if ($has_recipient && !empty($student_data['recipient_name'])) {
        // Destinatario específico
        $pdf->Cell(0, 6, utf8_decode(strtoupper($student_data['recipient_name'])), 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, utf8_decode($student_data['recipient_position']), 0, 1, 'L');
        $pdf->SetX(20);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, utf8_decode('P R E S E N T E'), 0, 1, 'L');
        
        // ✅ FECHA DESPUÉS DE "PRESENTE"
        $pdf->Ln(4);
        $y_fecha = $pdf->GetY();
    } else {
        // A quien corresponda
        $pdf->Cell(0, 7, utf8_decode('A QUIEN CORRESPONDA'), 0, 1, 'L');
        
        // ✅ FECHA DESPUÉS DE "A QUIEN CORRESPONDA"
        $pdf->Ln(4);
        $y_fecha = $pdf->GetY();
    }
    
    // --- Fecha (DESPUÉS del destinatario) ---
    $pdf->SetXY(20, $y_fecha);
    $pdf->SetFont('Arial', '', 11);
    setlocale(LC_TIME, 'es_MX.UTF-8', 'Spanish_Mexico', 'Spanish');
    $fecha = 'CDMX, a ' . date('d') . ' de ' . strftime('%B') . ' de ' . date('Y');
    $pdf->Cell(0, 6, utf8_decode($fecha), 0, 1, 'R');
    
    // --- Cuerpo de la Carta ---
    $pdf->Ln(6); // Espacio después de la fecha
    $pdf->SetFont('Arial', '', 11);
    $lineHeight = 5.5;
    $margen_izq = 20;
    $margen_der = 20;
    $ancho_texto = 215.9 - $margen_izq - $margen_der;

    // ✅ PRIMER PÁRRAFO - JUSTIFICADO SIN SANGRÍA
    $pdf->SetX($margen_izq);
    
    $parrafo1 = "Por este medio me permito presentar a " . 
                strtoupper($student_data['full_name']) . 
                " con número de boleta " . 
                $student_data['boleta'] . 
                ", quien es estudiante de la carrera de " . 
                $student_data['career'] . 
                " que se imparte en la Escuela Superior de Cómputo del Instituto Politécnico Nacional, y quien cursa actualmente con un porcentaje de avance de " . 
                number_format($student_data['percentage_progress'], 2) . "%.";
    
    $pdf->MultiCell($ancho_texto, $lineHeight, utf8_decode($parrafo1), 0, 'J'); // 'J' = Justificado
    
    // ✅ SEGUNDO PÁRRAFO - JUSTIFICADO SIN SANGRÍA
    $pdf->Ln(4); // Espacio entre párrafos
    $pdf->SetX($margen_izq);
    
    $parrafo2 = "De acuerdo con lo anterior, " . 
                strtoupper($student_data['full_name']) . 
                " se encuentra en posibilidades de desarrollar la estancia profesional, la cual corresponde a una de las unidades de aprendizaje de su programa de estudios";
    
    // Si requiere horas, agregar mención
    if ($requires_hours) {
        $parrafo2 .= ", misma que deberá cubrir un total de 200 horas";
    }
    
    $parrafo2 .= ", misma que pretende realizar en la empresa o dependencia a su digno cargo.";
    
    $pdf->MultiCell($ancho_texto, $lineHeight, utf8_decode($parrafo2), 0, 'J'); // 'J' = Justificado

    // ✅ TERCER PÁRRAFO (Despedida) - JUSTIFICADO
    $pdf->Ln(4);
    $pdf->SetX($margen_izq);
    $pdf->MultiCell($ancho_texto, $lineHeight, utf8_decode("Sin otro particular, queda de usted."), 0, 'J');
    
    // --- Bloque de Firma ---
    $pdf->Ln(8);
    $pdf->SetX($margen_izq);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, 'ATENTAMENTE', 0, 1, 'L');
    $pdf->SetX($margen_izq);
    $pdf->Cell(0, 6, utf8_decode('"La Técnica al Servicio de la Patria"'), 0, 1, 'L');
    
    // Espacio para firma
    $pdf->Ln(25);
    
    // Nombre del firmante
    $pdf->SetX($margen_izq);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, utf8_decode('Dr. José Asunción Enríquez Zárate'), 0, 1, 'L');
    $pdf->SetFont('Arial', '', 11);
    $pdf->SetX($margen_izq);
    $pdf->Cell(0, 5, utf8_decode('Subdirector de Servicios Educativos'), 0, 1, 'L');
    $pdf->SetX($margen_izq);
    $pdf->Cell(0, 5, utf8_decode('e Integración Social'), 0, 1, 'L');

    // --- Salida del PDF ---
    $filename = 'Carta_Presentacion_' . $student_data['boleta'] . '.pdf';
    
    if ($returnAsString) {
        return $pdf->Output('S', $filename, true);
    } else {
        $pdf->Output('I', $filename, true);
        exit;
    }
}

    
    public function generateAcceptanceLetter(array $student_data, array $company_data) {
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetMargins(25, 20, 25);
        $pdf->SetFont('Arial', 'B', 12);

        // Título Subrayado (simulado)
        $title = 'CARTA DE ACEPTACIÓN DE ESTANCIA PROFESIONAL';
        $pdf->Cell(0, 5, utf8_decode($title), 0, 1, 'C');
        $width = $pdf->GetStringWidth($title);
        $pdf->Line($pdf->GetX() + ($pdf->GetPageWidth() - $width)/2 - 25, $pdf->GetY(), $pdf->GetX() + $width + 10, $pdf->GetY());
        $pdf->Ln(15);

        // Fecha
        setlocale(LC_TIME, 'es_MX.UTF-8', 'Spanish_Mexico', 'Spanish');
        $fecha_actual = 'CDMX, a ' . date('d') . ' de ' . strftime('%B') . ' de ' . date('Y');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 10, utf8_decode($fecha_actual), 0, 1, 'R');
        $pdf->Ln(10);

        // Destinatario Fijo
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->MultiCell(0, 5, utf8_decode(
            "DR. JOSÉ ASUNCIÓN ENRÍQUEZ ZÁRATE\n".
            "SUBDIRECTOR DE SERVICIOS EDUCATIVOS\n".
            "E INTEGRACIÓN SOCIAL\n".
            "ESCUELA SUPERIOR DE CÓMPUTO\n".
            "INSTITUTO POLITÉCNICO NACIONAL\n".
            "P R E S E N T E"
        ));
        $pdf->Ln(10);

        // Cuerpo de la carta
        $pdf->SetFont('Arial', '', 11);
        $texto_p1 = "Por este conducto hago de su conocimiento, que al (o " . $student_data['gender'] . ") C. " .
                    strtoupper($student_data['student_name']) . ", estudiante de la carrera de " .
                    $student_data['student_career'] . ", con número de boleta " . $student_data['student_boleta'] .
                    ", le ha sido aceptado en esta empresa el desarrollo de su Estancia " .
                    "Profesional, en el entendido que una vez que " . $student_data['gender'] . " alumno/a cubra un máximo de 200 horas, se le emitirá " .
                    "por parte de la empresa, la Constancia de Validación de la Estancia Profesional.";
        $pdf->MultiCell(0, 6, utf8_decode($texto_p1));
        $pdf->Ln(10);

        $texto_p2 = "El C. " . strtoupper($student_data['student_name']) . " desarrollará su prestación en el área de " .
                   $student_data['area'] . ", realizando actividades en el proyecto \"" . $student_data['project_name'] . "\".";
        $pdf->MultiCell(0, 6, utf8_decode($texto_p2));
        $pdf->Ln(10);

        // Despedida
        $pdf->MultiCell(0, 6, utf8_decode("Agradezco de antemano su atención, y me despido quedando a sus órdenes para cualquier información adicional."));
        $pdf->Ln(15);
        
        // Firma
        $pdf->Cell(0, 6, 'Atentamente', 0, 1, 'C');
        $pdf->Ln(25);
        $pdf->Cell(0, 0, '', 'B', 1, 'C'); // Línea para firma
        $pdf->Cell(0, 6, utf8_decode($company_data['contact_person_name']), 0, 1, 'C');
        $pdf->Cell(0, 6, utf8_decode($company_data['contact_person_position']), 0, 1, 'C'); // Necesitaremos el puesto
        $pdf->Cell(0, 6, utf8_decode($company_data['company_name']), 0, 1, 'C');
        
        $pdf->Output('I', 'Carta_Aceptacion_' . $student_data['student_boleta'] . '.pdf', true);
    }

    
    public function generateValidationLetter(array $validation_data, array $company_data) {
        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();
        $pdf->SetMargins(25, 20, 25);
        $pdf->SetFont('Arial', 'B', 12);

        // Título
        $title = 'CONSTANCIA DE VALIDACIÓN DE ESTANCIA PROFESIONAL';
        $pdf->Cell(0, 5, utf8_decode($title), 0, 1, 'C');
        $pdf->Ln(20);

        // Cuerpo
        $pdf->SetFont('Arial', '', 11);
        $texto_p1 = "Por este medio, se hace constar que " . $validation_data['gender'] . " C. " .
                    strtoupper($validation_data['student_name']) . ", estudiante de la carrera de " .
                    $validation_data['student_career'] . " con número de boleta " . $validation_data['student_boleta'] .
                    ", concluyó satisfactoriamente en ésta empresa, su Estancia Profesional desarrollada durante el periodo " .
                    date('d/m/Y', strtotime($validation_data['start_date'])) . " - " . date('d/m/Y', strtotime($validation_data['end_date'])) .
                    ", cubriendo un total de " . $validation_data['total_hours'] . " horas.";
        $pdf->MultiCell(0, 6, utf8_decode($texto_p1));
        $pdf->Ln(10);

        $texto_p2 = "El C. " . strtoupper($validation_data['student_name']) . " desarrolló su prestación en el área de " .
                   $validation_data['area'] . ", realizando actividades en el proyecto \"" . $validation_data['project_name'] . "\".";
        $pdf->MultiCell(0, 6, utf8_decode($texto_p2));
        $pdf->Ln(10);
        
        // Fecha de emisión
        setlocale(LC_TIME, 'es_MX.UTF-8', 'Spanish_Mexico', 'Spanish');
        $fecha_emision = "Para los fines que al/a la estudiante convengan, se emite la presente constancia, el día " .
                         date('d') . " del mes de " . strftime('%B') . " del año " . date('Y') . ".";
        $pdf->MultiCell(0, 6, utf8_decode($fecha_emision));
        $pdf->Ln(20);

        // Firma
        $pdf->Cell(0, 6, 'Atentamente', 0, 1, 'C');
        $pdf->Ln(25);
        $pdf->Cell(0, 0, '', 'B', 1, 'C');
        $pdf->Cell(0, 6, utf8_decode($company_data['contact_person_name']), 0, 1, 'C');
        $pdf->Cell(0, 6, 'Responsable de la empresa', 0, 1, 'C');
        
        $pdf->Output('I', 'Constancia_Validacion_' . $validation_data['student_boleta'] . '.pdf', true);
    }

    public function generateHistoryReport(array $processes) {
        $pdf = new FPDF('L', 'mm', 'A4'); // 'L' para Landscape (horizontal)
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Título del Reporte
        $pdf->Cell(0, 10, utf8_decode('Reporte Histórico de Trámites Completados'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 7, utf8_decode('Generado el: ' . date('d/m/Y H:i')), 0, 1, 'C');
        $pdf->Ln(10);

        // Cabecera de la tabla
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(230, 230, 230); // Un gris claro para el fondo
        $pdf->Cell(80, 7, 'Estudiante', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Boleta', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Fecha Solicitud Carta', 1, 0, 'C', true);
        $pdf->Cell(50, 7, utf8_decode('Fecha Finalización'), 1, 0, 'C', true);
        $pdf->Cell(40, 7, utf8_decode('Duración (Días)'), 1, 1, 'C', true);

        // Filas de la tabla
        $pdf->SetFont('Arial', '', 9);
        foreach ($processes as $process) {
            $pdf->Cell(80, 6, utf8_decode($process['student_name']), 1);
            $pdf->Cell(30, 6, $process['student_boleta'], 1);
            $pdf->Cell(50, 6, date('d/m/Y', strtotime($process['presentation_letter_date'])), 1);
            $pdf->Cell(50, 6, date('d/m/Y', strtotime($process['accreditation_completed_date'])), 1);
            $pdf->Cell(40, 6, $process['total_duration_days'], 1, 1);
        }
        
        $pdf->Output('I', 'Reporte_Historico_' . date('Y-m-d') . '.pdf', true);
    }

        /**
     * Genera el expediente completo de acreditación en PDF
     * Historial completo del estudiante para archivo de UPIS
     * 
     * @param array $accreditation - Datos de la acreditación
     * @param array $metadata - Metadata con información del estudiante y empresa
     */
    public function generateAccreditationExpediente($accreditation, $metadata) {
        // Verificar que TCPDF esté disponible
        if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            die("Error: Librería TCPDF no instalada.");
        }
        
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        
        // ✅ CONFIGURACIÓN CORRECTA PARA UTF-8
        $pdf->SetCreator('SIEP - IPN UPIICSA');
        $pdf->SetAuthor('Unidad Politécnica de Integración Social');
        $pdf->SetTitle('Expediente de Acreditación - ' . $accreditation['boleta']);
        $pdf->SetSubject('Historial de Estancia Profesional');
        
        // ✅ Establecer fuente con soporte UTF-8
        $pdf->SetFont('dejavusans', '', 10, '', true);
        
        // Remover header/footer por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        
        // Extraer datos
        $student_info = $metadata['student_info'] ?? [];
        $company_info = $metadata['company_info'] ?? [];
        $tipo = $accreditation['tipo_acreditacion'];
        
        // ========================================
        // ENCABEZADO PRINCIPAL
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 22);
        $pdf->SetTextColor(0, 74, 153);
        $pdf->Cell(0, 12, 'EXPEDIENTE DE ESTANCIA PROFESIONAL', 0, 1, 'C');
        $pdf->Ln(2);
        
        // Estado de aprobación
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->SetFillColor(40, 167, 69);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, 'APROBADO', 0, 1, 'C', true);
        $pdf->Ln(8);
        
        // ========================================
        // SECCIÓN 1: INFORMACIÓN DEL ESTUDIANTE
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(17, 153, 142);
        $pdf->Cell(0, 8, 'INFORMACIÓN DEL ESTUDIANTE', 0, 1, 'L');
        $this->drawSectionLine($pdf);
        $pdf->Ln(3);
        
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        $this->addLabelValue($pdf, 'Boleta:', $accreditation['boleta']);
        $this->addLabelValue($pdf, 'Nombre Completo:', $accreditation['first_name'] . ' ' . $accreditation['last_name_p'] . ' ' . $accreditation['last_name_m']);
        $this->addLabelValue($pdf, 'Carrera:', $accreditation['career']);
        $this->addLabelValue($pdf, 'Email Institucional:', $student_info['email_institucional'] ?? $accreditation['email']);
        $this->addLabelValue($pdf, 'Teléfono:', $student_info['telefono'] ?? 'N/A');
        $this->addLabelValue($pdf, 'Semestre Cursado:', ($student_info['semestre'] ?? 'N/A') . '°');
        $pdf->Ln(5);
        
        // ========================================
        // SECCIÓN 2: INFORMACIÓN DE LA EMPRESA
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(17, 153, 142);
        $pdf->Cell(0, 8, 'INFORMACIÓN DE LA EMPRESA', 0, 1, 'L');
        $this->drawSectionLine($pdf);
        $pdf->Ln(3);
        
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        $this->addLabelValue($pdf, 'Nombre Comercial:', $company_info['nombre_comercial'] ?? 'N/A');
        $this->addLabelValue($pdf, 'Razón Social:', $company_info['razon_social'] ?? 'N/A');
        $this->addLabelValue($pdf, 'Tipo de Empresa:', ucfirst($company_info['tipo_empresa'] ?? 'N/A'));
        $this->addLabelValue($pdf, 'Giro:', ucfirst($company_info['giro'] ?? 'N/A'));
        $this->addLabelValue($pdf, 'Nombre del Contacto:', $company_info['nombre_contacto'] ?? 'N/A');
        $this->addLabelValue($pdf, 'Email de Contacto:', $company_info['email_contacto'] ?? 'N/A');
        $this->addLabelValue($pdf, 'Teléfono de Contacto:', $company_info['telefono_contacto'] ?? 'N/A');
        
        // Agencia de colocación
        $agencia = isset($company_info['agencia_colocacion']) && $company_info['agencia_colocacion'] === 'si' ? 'Sí' : 'No';
        $this->addLabelValue($pdf, '¿Agencia de Colocación?:', $agencia);
        
        $pdf->Ln(5);
        
        // ========================================
        // SECCIÓN 3: PERIODO DE ESTANCIA
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(17, 153, 142);
        $pdf->Cell(0, 8, 'PERIODO DE ESTANCIA', 0, 1, 'L');
        $this->drawSectionLine($pdf);
        $pdf->Ln(3);
        
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        $fecha_inicio = date('d/m/Y', strtotime($accreditation['fecha_inicio']));
        $fecha_fin = date('d/m/Y', strtotime($accreditation['fecha_fin']));
        
        // Calcular duración en días
        $inicio = new DateTime($accreditation['fecha_inicio']);
        $fin = new DateTime($accreditation['fecha_fin']);
        $duracion = $inicio->diff($fin)->days;
        
        $this->addLabelValue($pdf, 'Fecha de Inicio:', $fecha_inicio);
        $this->addLabelValue($pdf, 'Fecha de Conclusión:', $fecha_fin);
        $this->addLabelValue($pdf, 'Duración Total:', $duracion . ' día(s)');
        
        // Días de estancia
        $dias_estancia = isset($company_info['dias_estancia']) && is_array($company_info['dias_estancia']) 
            ? implode(', ', array_map('ucfirst', $company_info['dias_estancia'])) 
            : 'N/A';
        $this->addLabelValue($pdf, 'Días de Asistencia:', $dias_estancia);
        
        $pdf->Ln(5);
        
        // ========================================
        // SECCIÓN 4: TIPO DE ACREDITACIÓN
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(17, 153, 142);
        $pdf->Cell(0, 8, 'TIPO DE ACREDITACIÓN', 0, 1, 'L');
        $this->drawSectionLine($pdf);
        $pdf->Ln(3);
        
        // Badge del tipo
        $pdf->SetFont('dejavusans', 'B', 12);
        if ($tipo === 'A') {
            $pdf->SetFillColor(255, 107, 107);
            $tipo_texto = 'TIPO A - EMPRESA NO REGISTRADA';
        } else {
            $pdf->SetFillColor(78, 205, 196);
            $tipo_texto = 'TIPO B - EMPRESA REGISTRADA';
        }
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 8, $tipo_texto, 0, 1, 'C', true);
        
        $pdf->Ln(3);
        $pdf->SetFont('dejavusans', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        if ($tipo === 'A') {
            $descripcion = 'La empresa NO está registrada en el sistema SIEP. El estudiante presentó: Boleta Global, Recibos de Nómina y Constancia Laboral.';
        } else {
            $descripcion = 'La empresa SÍ está registrada en el sistema SIEP. El estudiante presentó: Boleta Global, Carta de Aceptación, Constancia de Validación y Reporte Final.';
        }
        $pdf->MultiCell(0, 5, $descripcion, 0, 'C');
        $pdf->Ln(5);
        
        // ========================================
        // SECCIÓN 5: REVISIÓN Y APROBACIÓN UPIS
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(17, 153, 142);
        $pdf->Cell(0, 8, 'REVISIÓN Y APROBACIÓN UPIS', 0, 1, 'L');
        $this->drawSectionLine($pdf);
        $pdf->Ln(3);
        
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        $fecha_aprobacion = date('d/m/Y H:i', strtotime($accreditation['reviewed_at']));
        $this->addLabelValue($pdf, 'Fecha de Aprobación:', $fecha_aprobacion);
        
        // Revisor UPIS
        $revisor = isset($accreditation['upis_first_name']) 
            ? $accreditation['upis_first_name'] . ' ' . $accreditation['upis_last_name_p']
            : 'N/A';
        $this->addLabelValue($pdf, 'Revisado por:', $revisor);
        
        // Comentarios
        $comentarios = !empty($accreditation['upis_comments']) 
            ? $accreditation['upis_comments'] 
            : 'Sin comentarios adicionales';
        $this->addLabelValue($pdf, 'Comentarios UPIS:', $comentarios);
        
        $pdf->Ln(8);
        
        // ========================================
        // SECCIÓN 6: DOCUMENTACIÓN PRESENTADA
        // ========================================
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetTextColor(17, 153, 142);
        $pdf->Cell(0, 8, 'DOCUMENTACIÓN PRESENTADA', 0, 1, 'L');
        $this->drawSectionLine($pdf);
        $pdf->Ln(3);
        
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        if ($tipo === 'A') {
            // Documentos Tipo A
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Boleta Global de Calificaciones', 0, 1, 'L');
            
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Recibo(s) de Nómina', 0, 1, 'L');
            
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Constancia Laboral de la Empresa', 0, 1, 'L');
        } else {
            // Documentos Tipo B
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Boleta Global de Calificaciones', 0, 1, 'L');
            
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Carta de Aceptación de la Empresa', 0, 1, 'L');
            
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Constancia de Validación de la Empresa', 0, 1, 'L');
            
            $pdf->Cell(10, 6, '✓', 0, 0, 'L');
            $pdf->Cell(0, 6, 'Reporte Final de Estancia', 0, 1, 'L');
        }
        
        $pdf->Ln(5);
        
        // ========================================
        // PIE DE PÁGINA
        // ========================================
        $pdf->SetY(-25);
        $pdf->SetFont('dejavusans', 'I', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 4, '───────────────────────────────────────────────────────────────', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Documento generado automáticamente por SIEP - UPIICSA IPN', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Sistema Integral de Estancias Profesionales', 0, 1, 'C');
        $pdf->Cell(0, 4, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        
        // Output
        $pdf->Output('Expediente_' . $accreditation['boleta'] . '.pdf', 'I');
        exit;
    }
    
    /**
     * Método auxiliar para agregar label y valor
     */
    private function addLabelValue($pdf, $label, $value) {
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(60, 6, $label, 0, 0, 'L');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->MultiCell(0, 6, $value, 0, 'L');
    }
    
    /**
     * Método auxiliar para dibujar línea de sección
     */
    private function drawSectionLine($pdf) {
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(17, 153, 142);
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
    }
}