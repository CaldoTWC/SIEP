<?php
/**
 * Servicio de Exportación de Reportes
 * Genera PDFs y archivos Excel de vacantes
 * 
 * @package SIEP\Services
 * @version 1.0.0
 */

require_once(__DIR__ . '/../Config/Database.php');

class ExportService {
    
    /**
     * Generar PDF de vacantes activas
     */
    public function generateActivePDF($vacancies) {
        $this->generatePDF($vacancies, 'Vacantes Activas', 'active');
    }
    
    /**
     * Generar PDF de vacantes completadas
     */
    public function generateCompletedPDF($vacancies) {
        $this->generatePDF($vacancies, 'Vacantes Completadas', 'completed');
    }
    
    /**
     * Generar PDF de vacantes canceladas
     */
    public function generateCanceledPDF($vacancies) {
        $this->generatePDF($vacancies, 'Vacantes Canceladas', 'canceled');
    }
    
    /**
     * Método genérico para generar PDFs
     */
    private function generatePDF($vacancies, $title, $type) {
        // Configurar headers para PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . str_replace(' ', '_', $title) . '_' . date('Y-m-d') . '.pdf"');
        
        // Generar HTML del reporte
        $html = $this->generateReportHTML($vacancies, $title, $type);
        
        // Usar DomPDF o similar (si tienes instalada la librería)
        // Por ahora, generamos un HTML simple que se puede imprimir como PDF
        echo $html;
        exit;
    }
    
    /**
     * Generar HTML para el reporte
     */
    private function generateReportHTML($vacancies, $title, $type) {
        $fecha = date('d/m/Y H:i');
        $total = count($vacancies);
        
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #005a9c; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .info-box { background: #f0f0f0; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #005a9c; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>UPIICSA - IPN</h1>
        <h2>{$title}</h2>
        <p>Sistema Integral de Estancias Profesionales (SIEP)</p>
    </div>
    
    <div class="info-box">
        <strong>Fecha de generación:</strong> {$fecha}<br>
        <strong>Total de registros:</strong> {$total}
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Empresa</th>
                <th>RFC</th>
                <th>Vacante</th>
                <th>Plazas</th>
                <th>Apoyo</th>
                <th>Modalidad</th>
                <th>Carrera</th>
HTML;

        if ($type === 'active') {
            $html .= '<th>Fecha Aprobación</th>';
        } elseif ($type === 'completed') {
            $html .= '<th>Fecha Completada</th><th>Motivo</th>';
        } elseif ($type === 'canceled') {
            $html .= '<th>Origen</th><th>Motivo</th><th>Fecha</th>';
        }

        $html .= <<<HTML
            </tr>
        </thead>
        <tbody>
HTML;

        // Generar filas
        foreach ($vacancies as $v) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($v['id']) . '</td>';
            $html .= '<td>' . htmlspecialchars($v['company_name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($v['rfc'] ?? 'N/A') . '</td>';
            $html .= '<td>' . htmlspecialchars($v['title']) . '</td>';
            $html .= '<td>' . htmlspecialchars($v['num_vacancies']) . '</td>';
            $html .= '<td>$' . number_format($v['economic_support'], 2) . '</td>';
            $html .= '<td>' . htmlspecialchars($v['modality']) . '</td>';
            $html .= '<td>' . htmlspecialchars($v['related_career'] ?? 'N/A') . '</td>';
            
            if ($type === 'active') {
                $html .= '<td>' . date('d/m/Y', strtotime($v['approved_at'])) . '</td>';
            } elseif ($type === 'completed') {
                $html .= '<td>' . date('d/m/Y', strtotime($v['completed_at'])) . '</td>';
                $html .= '<td>' . htmlspecialchars($v['completion_reason'] ?? 'N/A') . '</td>';
            } elseif ($type === 'canceled') {
                $source = $v['rejection_source'] ?? 'N/A';
                $sourceLabel = [
                    'upis_review' => 'UPIS-Revisión',
                    'company_cancel' => 'Empresa',
                    'upis_takedown' => 'UPIS-Tumbada'
                ];
                $html .= '<td>' . ($sourceLabel[$source] ?? 'N/A') . '</td>';
                $html .= '<td>' . htmlspecialchars($v['rejection_reason'] ?? 'N/A') . '</td>';
                $html .= '<td>' . date('d/m/Y', strtotime($v['approved_at'])) . '</td>';
            }
            
            $html .= '</tr>';
        }

        $html .= <<<HTML
        </tbody>
    </table>
    
    <div class="footer">
        <p>Documento generado automáticamente por SIEP - UPIICSA IPN</p>
        <p>Este reporte es válido solo para fines informativos internos</p>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
HTML;

        return $html;
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