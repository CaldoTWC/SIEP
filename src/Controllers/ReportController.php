<?php
/**
 * Controlador para generación de reportes
 * Maneja la lógica de negocio y exportación de reportes
 * 
 * @package SIEP\Controllers
 * @version 2.0.0 - Integración de reportes de vacantes
 */

require_once(__DIR__ . '/../Lib/Session.php');
require_once(__DIR__ . '/../Models/Vacancy.php');

class ReportController {
    private $reportModel;
    private $session;
    
    public function __construct() {
        $this->session = new Session();
        
        // Cargar Report model solo si existe (para reportes legacy)
        if (file_exists(__DIR__ . '/../Models/Report.php')) {
            require_once(__DIR__ . '/../Models/Report.php');
            $this->reportModel = new Report();
        }
    }
    
    // ========================================================================
    // REPORTES DE VACANTES (NUEVO SISTEMA)
    // ========================================================================
    
    /**
     * Exportar PDF de vacantes activas
     */
    public function exportActivePDF() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getVacanciesByStatus('approved');
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateActivePDF($vacancies);
    }
    
    /**
     * Exportar PDF de vacantes completadas
     */
    public function exportCompletedPDF() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getVacanciesByStatus('completed');
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateCompletedPDF($vacancies);
    }
    
    /**
     * Exportar PDF de vacantes canceladas
     */
    public function exportCanceledPDF() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getVacanciesByStatus('rejected');
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateCanceledPDF($vacancies);
    }
    
    /**
     * Exportar Excel de todas las vacantes
     */
    public function exportAllExcel() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $vacancies = $vacancyModel->getAllVacanciesForReports();
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateAllVacanciesExcel($vacancies);
    }
    
    /**
     * Exportar Excel de análisis de empresas
     */
    public function exportCompanyAnalysisExcel() {
        $this->session->guard(['upis', 'admin']);
        
        $vacancyModel = new Vacancy();
        $companies = $vacancyModel->getVacanciesGroupedByCompany();
        
        require_once(__DIR__ . '/../Services/ExportService.php');
        $exportService = new ExportService();
        $exportService->generateCompanyAnalysisExcel($companies);
    }
    
    // ========================================================================
    // REPORTES LEGACY (SISTEMA ANTIGUO - MANTENER)
    // ========================================================================
    
    /**
     * Dashboard ejecutivo
     */
    public function dashboard() {
        $this->session->guard(['upis', 'admin']);
        
        if ($this->reportModel) {
            $stats = $this->reportModel->getDashboardStats();
            require_once(__DIR__ . '/../Views/reports/dashboard.php');
        } else {
            $_SESSION['error'] = "Modelo de reportes no disponible.";
            header('Location: /SIEP/public/index.php?action=upisDashboard');
            exit;
        }
    }
    
    /**
     * Historial de estancias
     */
    public function estancias() {
        $this->session->guard(['upis', 'admin']);
        
        if (!$this->reportModel) {
            $_SESSION['error'] = "Modelo de reportes no disponible.";
            header('Location: /SIEP/public/index.php?action=upisDashboard');
            exit;
        }
        
        $filtros = [
            'carrera' => $_GET['carrera'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? ''
        ];
        
        $estancias = $this->reportModel->getEstanciasCompletadas($filtros);
        $carreras = $this->reportModel->getCarreras();
        
        // Si se solicita exportación
        if (isset($_GET['export'])) {
            if ($_GET['export'] === 'pdf') {
                $this->exportEstanciasPDF($estancias);
            } elseif ($_GET['export'] === 'excel') {
                $this->exportEstanciasExcel($estancias);
            }
            exit;
        }
        
        require_once(__DIR__ . '/../Views/reports/estancias.php');
    }
    
    /**
     * Reporte de vacantes (legacy - mantener si existe)
     */
    public function vacantes() {
        $this->session->guard(['upis', 'admin']);
        
        if ($this->reportModel) {
            $vacantes = $this->reportModel->getVacantesActivas();
            require_once(__DIR__ . '/../Views/reports/vacantes.php');
        } else {
            // Redirigir al nuevo sistema
            header('Location: /SIEP/public/index.php?action=vacancyHub');
            exit;
        }
    }
    
    /**
     * Reporte de empresas
     */
    public function empresas() {
        $this->session->guard(['upis', 'admin']);
        
        if ($this->reportModel) {
            $empresas = $this->reportModel->getEmpresasConEstudiantes();
            require_once(__DIR__ . '/../Views/reports/empresas.php');
        } else {
            $_SESSION['error'] = "Modelo de reportes no disponible.";
            header('Location: /SIEP/public/index.php?action=upisDashboard');
            exit;
        }
    }
    
    /**
     * Exportar estancias a PDF
     */
    private function exportEstanciasPDF($estancias) {
        // Verificar si TCPDF está disponible
        if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            $_SESSION['error'] = "Librería TCPDF no disponible.";
            header('Location: /SIEP/public/index.php?action=showHistory');
            exit;
        }
        
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        // Configuración del documento
        $pdf->SetCreator('SIEP - IPN UPIICSA');
        $pdf->SetAuthor('Unidad Politécnica de Integración Social');
        $pdf->SetTitle('Historial de Estancias Completadas');
        $pdf->SetSubject('Reporte de Estancias');
        
        // Remover header/footer por defecto
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Márgenes
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        
        // Agregar página
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'HISTORIAL DE ESTANCIAS COMPLETADAS', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Fecha de generación
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 5, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
        $pdf->Ln(5);
        
        // Tabla
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(41, 128, 185);
        $pdf->SetTextColor(255, 255, 255);
        
        // Encabezados
        $pdf->Cell(25, 7, 'Boleta', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Estudiante', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Carrera', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Empresa', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Tipo Doc.', 1, 0, 'C', true);
        $pdf->Cell(35, 7, 'Fecha', 1, 1, 'C', true);
        
        // Datos
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(240, 240, 240);
        
        $fill = false;
        foreach ($estancias as $estancia) {
            $pdf->Cell(25, 6, $estancia['boleta'], 1, 0, 'C', $fill);
            $pdf->Cell(50, 6, substr($estancia['estudiante'], 0, 30), 1, 0, 'L', $fill);
            $pdf->Cell(60, 6, substr($estancia['carrera'], 0, 35), 1, 0, 'L', $fill);
            $pdf->Cell(60, 6, substr($estancia['empresa'], 0, 35), 1, 0, 'L', $fill);
            $pdf->Cell(40, 6, $estancia['tipo_documento'], 1, 0, 'C', $fill);
            $pdf->Cell(35, 6, date('d/m/Y', strtotime($estancia['fecha_actualizacion'])), 1, 1, 'C', $fill);
            $fill = !$fill;
        }
        
        // Total
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 7, 'Total de registros: ' . count($estancias), 0, 1, 'R');
        
        // Output
        $pdf->Output('estancias_completadas_' . date('Y-m-d') . '.pdf', 'D');
    }
    
    /**
     * Exportar estancias a Excel
     */
    private function exportEstanciasExcel($estancias) {
        // Verificar si PhpSpreadsheet está disponible
        if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            $_SESSION['error'] = "Librería PhpSpreadsheet no disponible.";
            header('Location: /SIEP/public/index.php?action=showHistory');
            exit;
        }
        
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Título
        $sheet->setCellValue('A1', 'HISTORIAL DE ESTANCIAS COMPLETADAS');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Fecha de generación
        $sheet->setCellValue('A2', 'Fecha de generación: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:G2');
        
        // Encabezados
        $headers = ['Boleta', 'Estudiante', 'Carrera', 'Empresa', 'Tipo Documento', 'Estatus', 'Fecha Actualización'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        
        // Estilo de encabezados
        $sheet->getStyle('A4:G4')->getFont()->setBold(true);
        $sheet->getStyle('A4:G4')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF2980B9');
        $sheet->getStyle('A4:G4')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        // Datos
        $row = 5;
        foreach ($estancias as $estancia) {
            $sheet->setCellValue('A' . $row, $estancia['boleta']);
            $sheet->setCellValue('B' . $row, $estancia['estudiante']);
            $sheet->setCellValue('C' . $row, $estancia['carrera']);
            $sheet->setCellValue('D' . $row, $estancia['empresa']);
            $sheet->setCellValue('E' . $row, $estancia['tipo_documento']);
            $sheet->setCellValue('F' . $row, $estancia['estatus']);
            $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($estancia['fecha_actualizacion'])));
            $row++;
        }
        
        // Autoajustar columnas
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Bordes
        $sheet->getStyle('A4:G' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="estancias_completadas_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
    }
}