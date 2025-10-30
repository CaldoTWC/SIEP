<?php
/**
 * Servicio de Exportación de Reportes
 * Genera PDFs y archivos Excel de vacantes
 * 
 * @package SIEP\Services
 * @version 2.0.0 - Integración con TCPDF
 */

require_once(__DIR__ . '/../Config/Database.php');

class ExportService {
    
    /**
     * Generar PDF de vacantes activas
     */
    public function generateActivePDF($vacancies) {
        $this->generatePDFWithTCPDF($vacancies, 'Vacantes Activas', 'active');
    }
    
    /**
     * Generar PDF de vacantes completadas
     */
    public function generateCompletedPDF($vacancies) {
        $this->generatePDFWithTCPDF($vacancies, 'Vacantes Completadas', 'completed');
    }
    
    /**
     * Generar PDF de vacantes canceladas
     */
    public function generateCanceledPDF($vacancies) {
        $this->generatePDFWithTCPDF($vacancies, 'Vacantes Canceladas', 'canceled');
    }
    
    /**
     * Generar PDF usando TCPDF
     */
    private function generatePDFWithTCPDF($vacancies, $title, $type) {
        // Verificar si TCPDF está disponible
        if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            die("Error: Librería TCPDF no instalada. Ejecuta: composer require tecnickcom/tcpdf");
        }
        
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        // Crear PDF en orientación horizontal
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        // Configuración del documento
        $pdf->SetCreator('SIEP - IPN UPIICSA');
        $pdf->SetAuthor('Unidad Politécnica de Integración Social');
        $pdf->SetTitle($title);
        $pdf->SetSubject('Reporte de Vacantes');
        
        // Remover header/footer por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        
        // Agregar página
        $pdf->AddPage();
        
        // Título principal
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 10, 'UPIICSA - IPN', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, $title, 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Sistema Integral de Estancias Profesionales (SIEP)', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Info box
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, 'Fecha de generación: ' . date('d/m/Y H:i') . '     Total de registros: ' . count($vacancies), 0, 1, 'L', true);
        $pdf->Ln(5);
        
        // Encabezados de tabla
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(0, 90, 156);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(12, 7, 'ID', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'Empresa', 1, 0, 'C', true);
        $pdf->Cell(55, 7, 'Vacante', 1, 0, 'C', true);
        $pdf->Cell(15, 7, 'Plazas', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Apoyo', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Modalidad', 1, 0, 'C', true);
        
        if ($type === 'active') {
            $pdf->Cell(30, 7, 'Aprobación', 1, 1, 'C', true);
        } elseif ($type === 'completed') {
            $pdf->Cell(30, 7, 'Completada', 1, 0, 'C', true);
            $pdf->Cell(45, 7, 'Motivo', 1, 1, 'C', true);
        } elseif ($type === 'canceled') {
            $pdf->Cell(25, 7, 'Origen', 1, 0, 'C', true);
            $pdf->Cell(45, 7, 'Motivo', 1, 1, 'C', true);
        }
        
        // Datos de la tabla
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(249, 249, 249);
        
        $fill = false;
        foreach ($vacancies as $v) {
            $pdf->Cell(12, 6, $v['id'], 1, 0, 'C', $fill);
            $pdf->Cell(45, 6, substr($v['company_name'], 0, 30), 1, 0, 'L', $fill);
            $pdf->Cell(55, 6, substr($v['title'], 0, 40), 1, 0, 'L', $fill);
            $pdf->Cell(15, 6, $v['num_vacancies'], 1, 0, 'C', $fill);
            $pdf->Cell(25, 6, '$' . number_format($v['economic_support'], 2), 1, 0, 'R', $fill);
            $pdf->Cell(25, 6, $v['modality'], 1, 0, 'C', $fill);
            
            if ($type === 'active') {
                $pdf->Cell(30, 6, date('d/m/Y', strtotime($v['approved_at'])), 1, 1, 'C', $fill);
            } elseif ($type === 'completed') {
                $pdf->Cell(30, 6, date('d/m/Y', strtotime($v['completed_at'])), 1, 0, 'C', $fill);
                $pdf->Cell(45, 6, substr($v['completion_reason'] ?? 'N/A', 0, 30), 1, 1, 'L', $fill);
            } elseif ($type === 'canceled') {
                $source = $v['rejection_source'] ?? 'N/A';
                $sourceLabels = [
                    'upis_review' => 'UPIS-Rev',
                    'company_cancel' => 'Empresa',
                    'upis_takedown' => 'UPIS-Tumb'
                ];
                $pdf->Cell(25, 6, $sourceLabels[$source] ?? 'N/A', 1, 0, 'C', $fill);
                $pdf->Cell(45, 6, substr($v['rejection_reason'] ?? 'N/A', 0, 30), 1, 1, 'L', $fill);
            }
            
            $fill = !$fill;
        }
        
        // Footer
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'Documento generado automáticamente por SIEP - UPIICSA IPN', 0, 1, 'C');
        
        // Output
        $filename = str_replace(' ', '_', $title) . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
    
    /**
     * Generar Excel de todas las vacantes
     */
    public function generateAllVacanciesExcel($vacancies) {
        $filename = 'Todas_Vacantes_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Empresa',
            'RFC',
            'Email',
            'Vacante',
            'Num Plazas',
            'Apoyo Económico',
            'Modalidad',
            'Carrera',
            'Fecha Inicio',
            'Fecha Fin',
            'Estado',
            'Fecha Publicación',
            'Fecha Aprobación',
            'Motivo Rechazo',
            'Notas Rechazo'
        ]);
        
        // Datos
        foreach ($vacancies as $v) {
            fputcsv($output, [
                $v['id'],
                $v['company_name'],
                $v['rfc'] ?? 'N/A',
                $v['company_email'],
                $v['title'],
                $v['num_vacancies'],
                $v['economic_support'],
                $v['modality'],
                $v['related_career'] ?? 'N/A',
                $v['start_date'],
                $v['end_date'],
                $this->getStatusLabel($v['status']),
                $v['posted_at'],
                $v['approved_at'] ?? 'N/A',
                $v['rejection_reason'] ?? 'N/A',
                $v['rejection_notes'] ?? 'N/A'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Generar Excel de análisis de empresas
     */
    public function generateCompanyAnalysisExcel($companies) {
        $filename = 'Analisis_Empresas_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'Empresa',
            'RFC',
            'Email',
            'Total Vacantes',
            'Pendientes',
            'Activas',
            'Completadas',
            'Canceladas',
            'Tasa de Éxito (%)'
        ]);
        
        // Datos
        foreach ($companies as $company) {
            $stats = $company['stats'];
            fputcsv($output, [
                $company['company_name'],
                $company['rfc'],
                $company['company_email'],
                $stats['total'],
                $stats['pending'],
                $stats['active'],
                $stats['completed'],
                $stats['cancelled'],
                $stats['success_rate']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Obtener etiqueta de estado
     */
    private function getStatusLabel($status) {
        $labels = [
            'pending' => 'Pendiente',
            'approved' => 'Activa',
            'completed' => 'Completada',
            'rejected' => 'Cancelada'
        ];
        
        return $labels[$status] ?? $status;
    }
}