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

        // ========================================================================
    // REPORTES DE ESTUDIANTES - TIEMPO DE PROCESAMIENTO
    // ========================================================================
    
    /**
     * Exportar PDF de tiempo de procesamiento de estudiantes
     */
    public function exportStudentProcessingPDF() {
        $this->session->guard(['upis', 'admin']);
        
        $students = $this->getStudentProcessingData();
        
        if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            die("Error: Librería TCPDF no instalada.");
        }
        
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        $pdf->SetCreator('SIEP - IPN UPIICSA');
        $pdf->SetAuthor('Unidad Politécnica de Integración Social');
        $pdf->SetTitle('Tiempo de Procesamiento de Estudiantes');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 10, 'UPIICSA - IPN', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Tiempo de Procesamiento de Estudiantes', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Desde el Registro hasta Acreditación Aprobada', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Estadísticas
        $total = count($students);
        $avg_days = $total > 0 ? array_sum(array_column($students, 'dias_procesamiento')) / $total : 0;
        
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y H:i') . '  |  Total: ' . $total . '  |  Promedio: ' . round($avg_days, 1) . ' días', 0, 1, 'L', true);
        $pdf->Ln(5);
        
        // Tabla
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(0, 90, 156);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(20, 7, 'Boleta', 1, 0, 'C', true);
        $pdf->Cell(60, 7, 'Nombre Completo', 1, 0, 'C', true);
        $pdf->Cell(50, 7, 'Carrera', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'F. Registro', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'F. Aprobación', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Días', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Estado', 1, 1, 'C', true);
        
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(249, 249, 249);
        
        $fill = false;
        foreach ($students as $s) {
            $pdf->Cell(20, 6, $s['boleta'], 1, 0, 'C', $fill);
            $pdf->Cell(60, 6, substr($s['nombre_completo'], 0, 35), 1, 0, 'L', $fill);
            $pdf->Cell(50, 6, substr($s['career'], 0, 25), 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, date('d/m/Y', strtotime($s['fecha_registro'])), 1, 0, 'C', $fill);
            $pdf->Cell(30, 6, $s['fecha_aprobacion'] ? date('d/m/Y', strtotime($s['fecha_aprobacion'])) : 'Pendiente', 1, 0, 'C', $fill);
            $pdf->Cell(20, 6, $s['dias_procesamiento'] ?? '-', 1, 0, 'C', $fill);
            $pdf->Cell(25, 6, $this->getStatusBadge($s['estado_acreditacion']), 1, 1, 'C', $fill);
            $fill = !$fill;
        }
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'Documento generado automáticamente por SIEP - UPIICSA IPN', 0, 1, 'C');
        
        $pdf->Output('Tiempo_Procesamiento_' . date('Y-m-d') . '.pdf', 'D');
        exit;
    }
    
    /**
     * Exportar Excel de tiempo de procesamiento
     */
    public function exportStudentProcessingExcel() {
        $this->session->guard(['upis', 'admin']);
        
        $students = $this->getStudentProcessingData();
        
        $filename = 'Tiempo_Procesamiento_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'Boleta',
            'Nombre Completo',
            'Carrera',
            'Email',
            'Fecha Registro',
            'Fecha Aprobación UPIS',
            'Días de Procesamiento',
            'Estado',
            'Revisor UPIS',
            'Comentarios'
        ]);
        
        foreach ($students as $s) {
            fputcsv($output, [
                $s['boleta'],
                $s['nombre_completo'],
                $s['career'],
                $s['email'],
                $s['fecha_registro'],
                $s['fecha_aprobacion'] ?? 'Pendiente',
                $s['dias_procesamiento'] ?? 'N/A',
                $this->getStatusLabel($s['estado_acreditacion']),
                $s['revisor_nombre'] ?? 'N/A',
                $s['comentarios_upis'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Obtener datos de procesamiento de estudiantes
     */
    private function getStudentProcessingData() {
        require_once(__DIR__ . '/../Config/Database.php');
        $database = new Database();
        $conn = $database->getConnection();
        
        $sql = "SELECT 
                    u.id,
                    sp.boleta,
                    CONCAT(u.first_name, ' ', u.last_name_p, ' ', u.last_name_m) as nombre_completo,
                    sp.career,
                    u.email,
                    u.created_at as fecha_registro,
                    acc.reviewed_at as fecha_aprobacion,
                    acc.status as estado_acreditacion,
                    acc.upis_comments as comentarios_upis,
                    CONCAT(upis.first_name, ' ', upis.last_name_p) as revisor_nombre,
                    DATEDIFF(acc.reviewed_at, u.created_at) as dias_procesamiento
                FROM users u
                INNER JOIN student_profiles sp ON u.id = sp.user_id
                LEFT JOIN accreditation_submissions acc ON u.id = acc.student_user_id
                LEFT JOIN users upis ON acc.upis_reviewer_id = upis.id
                WHERE u.role = 'student'
                  AND acc.status IN ('approved', 'rejected')
                ORDER BY u.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ========================================================================
    // REPORTES DE EMPRESAS Y ESTUDIANTES
    // ========================================================================
    
    /**
     * Exportar PDF de empresas con estudiantes
     */
    public function exportCompanyStudentsPDF() {
        $this->session->guard(['upis', 'admin']);
        
        $companies = $this->getCompanyStudentsData();
        
        if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
            die("Error: Librería TCPDF no instalada.");
        }
        
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        
        $pdf->SetCreator('SIEP - IPN UPIICSA');
        $pdf->SetTitle('Empresas y Estudiantes en Servicio');
        
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();
        
        // Título
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->Cell(0, 10, 'UPIICSA - IPN', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 8, 'Empresas y Estudiantes en Servicio', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, 'Listado de Estudiantes por Empresa', 0, 1, 'C');
        $pdf->Ln(5);
        
        $total_empresas = count(array_unique(array_column($companies, 'empresa_id')));
        $total_estudiantes = count($companies);
        
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 6, 'Fecha: ' . date('d/m/Y') . '  |  Empresas: ' . $total_empresas . '  |  Estudiantes: ' . $total_estudiantes, 0, 1, 'L', true);
        $pdf->Ln(5);
        
        // Tabla
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(0, 90, 156);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(55, 7, 'Empresa', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'RFC', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Boleta', 1, 0, 'C', true);
        $pdf->Cell(55, 7, 'Estudiante', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'Carrera', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Inicio', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Días', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Estado', 1, 1, 'C', true);
        
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(249, 249, 249);
        
        $fill = false;
        foreach ($companies as $c) {
            $pdf->Cell(55, 5, substr($c['company_name'], 0, 30), 1, 0, 'L', $fill);
            $pdf->Cell(25, 5, $c['rfc'] ?? 'N/A', 1, 0, 'C', $fill);
            $pdf->Cell(20, 5, $c['boleta'] ?? '-', 1, 0, 'C', $fill);
            $pdf->Cell(55, 5, substr($c['estudiante_nombre'] ?? 'Sin estudiantes', 0, 30), 1, 0, 'L', $fill);
            $pdf->Cell(45, 5, substr($c['career'] ?? '-', 0, 25), 1, 0, 'L', $fill);
            $pdf->Cell(25, 5, $c['fecha_inicio'] ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '-', 1, 0, 'C', $fill);
            $pdf->Cell(20, 5, $c['dias_servicio'] ?? '-', 1, 0, 'C', $fill);
            $pdf->Cell(20, 5, $c['estado'] ?? '-', 1, 1, 'C', $fill);
            $fill = !$fill;
        }
        
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'Documento generado automáticamente por SIEP - UPIICSA IPN', 0, 1, 'C');
        
        $pdf->Output('Empresas_Estudiantes_' . date('Y-m-d') . '.pdf', 'D');
        exit;
    }
    
    /**
     * Exportar Excel de empresas con estudiantes
     */
    public function exportCompanyStudentsExcel() {
        $this->session->guard(['upis', 'admin']);
        
        $companies = $this->getCompanyStudentsData();
        
        $filename = 'Empresas_Estudiantes_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'Empresa',
            'Nombre Comercial',
            'RFC',
            'Email Empresa',
            'Boleta',
            'Estudiante',
            'Carrera',
            'Email Estudiante',
            'Fecha Inicio Servicio',
            'Fecha Fin Servicio',
            'Días de Servicio',
            'Estado Vínculo'
        ]);
        
        foreach ($companies as $c) {
            fputcsv($output, [
                $c['company_name'],
                $c['commercial_name'] ?? '',
                $c['rfc'] ?? 'N/A',
                $c['empresa_email'],
                $c['boleta'] ?? '-',
                $c['estudiante_nombre'] ?? 'Sin estudiantes',
                $c['career'] ?? '-',
                $c['estudiante_email'] ?? '-',
                $c['fecha_inicio'] ?? '-',
                $c['fecha_fin'] ?? 'En curso',
                $c['dias_servicio'] ?? '-',
                $c['estado'] == 'active' ? 'Activo' : 'Completado'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Obtener datos de empresas con estudiantes
     */
    private function getCompanyStudentsData() {
        require_once(__DIR__ . '/../Config/Database.php');
        $database = new Database();
        $conn = $database->getConnection();
        
        $sql = "SELECT 
                    cp.id as empresa_id,
                    cp.company_name,
                    cp.commercial_name,
                    cp.rfc,
                    u_contact.email as empresa_email,
                    u_student.id as estudiante_id,
                    sp.boleta,
                    CONCAT(u_student.first_name, ' ', u_student.last_name_p, ' ', u_student.last_name_m) as estudiante_nombre,
                    u_student.email as estudiante_email,
                    sp.career,
                    csl.acceptance_date as fecha_inicio,
                    csl.completion_date as fecha_fin,
                    csl.status as estado,
                    DATEDIFF(IFNULL(csl.completion_date, CURDATE()), csl.acceptance_date) as dias_servicio
                FROM company_profiles cp
                LEFT JOIN company_student_links csl ON cp.id = csl.company_profile_id
                LEFT JOIN users u_student ON csl.student_user_id = u_student.id
                LEFT JOIN student_profiles sp ON u_student.id = sp.user_id
                INNER JOIN users u_contact ON cp.contact_person_user_id = u_contact.id
                ORDER BY cp.company_name, csl.acceptance_date DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Helper: Obtener badge de estado
     */
    private function getStatusBadge($status) {
        $badges = [
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'pending' => 'Pendiente',
            'completed' => 'Completado'
        ];
        return $badges[$status] ?? $status;
    }
}