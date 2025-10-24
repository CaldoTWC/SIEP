<?php
/**
 * Plantillas HTML para correos electrónicos
 * 
 * @package SIEP\Services
 * @version 1.0.0
 */

class EmailTemplates {
    
    /**
     * Plantilla base HTML
     */
    private function getBaseTemplate($content) {
        $site_url = getenv('SITE_URL') ?: 'http://localhost/SIEP/public';
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #6f1d42; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background-color: #6f1d42; color: white; text-decoration: none; border-radius: 4px; margin: 10px 0; }
        .info-box { background-color: #e8f4f8; border-left: 4px solid #3498db; padding: 15px; margin: 15px 0; }
        .success-box { background-color: #e8f8e8; border-left: 4px solid #27ae60; padding: 15px; margin: 15px 0; }
        .warning-box { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SIEP - UPIICSA</h1>
            <p>Sistema Integral de Estancias Profesionales</p>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>Instituto Politécnico Nacional - UPIICSA</p>
            <p>Unidad Politécnica de Integración Social</p>
            <p><a href="{$site_url}">Acceder al sistema</a></p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Notificación a UPIS: Nueva solicitud de acreditación
     */
    public function newAccreditationUPIS($student, $submission) {
        $content = <<<HTML
<h2>Nueva Solicitud de Acreditación</h2>
<p>Se ha registrado una nueva solicitud de acreditación de estancia profesional.</p>

<div class="info-box">
    <h3>Datos del Estudiante:</h3>
    <p><strong>Nombre:</strong> {$student['full_name']}</p>
    <p><strong>Boleta:</strong> {$student['boleta']}</p>
    <p><strong>Carrera:</strong> {$student['career']}</p>
    <p><strong>Email:</strong> {$student['email']}</p>
</div>

<div class="info-box">
    <h3>Datos de la Estancia:</h3>
    <p><strong>Empresa:</strong> {$submission['company_name']}</p>
    <p><strong>Periodo:</strong> {$submission['start_date']} a {$submission['end_date']}</p>
    <p><strong>Fecha de solicitud:</strong> {$submission['created_at']}</p>
</div>

<div class="info-box">
    <h3>Documentos subidos:</h3>
    <ul>
        <li>✅ Carta de Aceptación</li>
        <li>✅ Reporte Técnico</li>
        <li>✅ Evidencias</li>
    </ul>
</div>

<p><a href="{$submission['review_url']}" class="btn">Revisar Solicitud</a></p>

<p>Por favor, revise y procese esta solicitud a la brevedad posible.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Notificación a UPIS
     */
    public function newAccreditationUPISPlainText($student, $submission) {
        return <<<TEXT
NUEVA SOLICITUD DE ACREDITACIÓN - SIEP UPIICSA

Se ha registrado una nueva solicitud de acreditación de estancia profesional.

DATOS DEL ESTUDIANTE:
Nombre: {$student['full_name']}
Boleta: {$student['boleta']}
Carrera: {$student['career']}
Email: {$student['email']}

DATOS DE LA ESTANCIA:
Empresa: {$submission['company_name']}
Periodo: {$submission['start_date']} a {$submission['end_date']}
Fecha de solicitud: {$submission['created_at']}

DOCUMENTOS SUBIDOS:
- Carta de Aceptación
- Reporte Técnico
- Evidencias

Por favor, revise la solicitud en el sistema.

---
Sistema Integral de Estancias Profesionales
UPIICSA - IPN
TEXT;
    }
    
    /**
     * Confirmación al estudiante: Solicitud recibida
     */
    public function accreditationReceivedStudent($student, $submission) {
        $content = <<<HTML
<h2>¡Solicitud Recibida!</h2>
<p>Estimado/a <strong>{$student['full_name']}</strong>,</p>

<div class="success-box">
    <p>Tu solicitud de acreditación de estancia profesional ha sido recibida exitosamente.</p>
</div>

<div class="info-box">
    <h3>Detalles de tu solicitud:</h3>
    <p><strong>Empresa:</strong> {$submission['company_name']}</p>
    <p><strong>Periodo:</strong> {$submission['start_date']} a {$submission['end_date']}</p>
    <p><strong>Fecha de envío:</strong> {$submission['created_at']}</p>
</div>

<p>Tu solicitud será revisada por el equipo de UPIS. Recibirás una notificación cuando haya una actualización.</p>

<p><strong>Próximos pasos:</strong></p>
<ol>
    <li>El equipo UPIS revisará tu documentación</li>
    <li>Recibirás una notificación sobre el estado de tu solicitud</li>
    <li>Si es aprobada, podrás continuar con el proceso de acreditación</li>
</ol>

<p>Si tienes dudas, puedes contactar a UPIS.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Confirmación al estudiante
     */
    public function accreditationReceivedStudentPlainText($student, $submission) {
        return <<<TEXT
SOLICITUD RECIBIDA - SIEP UPIICSA

Estimado/a {$student['full_name']},

Tu solicitud de acreditación de estancia profesional ha sido recibida exitosamente.

DETALLES DE TU SOLICITUD:
Empresa: {$submission['company_name']}
Periodo: {$submission['start_date']} a {$submission['end_date']}
Fecha de envío: {$submission['created_at']}

Tu solicitud será revisada por el equipo de UPIS. Recibirás una notificación cuando haya una actualización.

PRÓXIMOS PASOS:
1. El equipo UPIS revisará tu documentación
2. Recibirás una notificación sobre el estado de tu solicitud
3. Si es aprobada, podrás continuar con el proceso de acreditación

Si tienes dudas, puedes contactar a UPIS.

---
Sistema Integral de Estancias Profesionales
UPIICSA - IPN
TEXT;
    }
    
    /**
     * Notificación al estudiante: Estado de solicitud
     */
    public function accreditationStatusStudent($student, $status, $comments) {
        $is_approved = $status === 'approved';
        $status_text = $is_approved ? 'APROBADA' : 'REQUIERE REVISIÓN';
        $box_class = $is_approved ? 'success-box' : 'warning-box';
        $icon = $is_approved ? '✅' : '⚠️';
        
        $message = $is_approved 
            ? 'Tu solicitud de acreditación ha sido <strong>aprobada</strong>.' 
            : 'Tu solicitud de acreditación requiere revisión.';
        
        $next_steps = $is_approved
            ? '<p>Puedes proceder con el siguiente paso del proceso de acreditación.</p>'
            : '<p>Por favor, revisa los comentarios y realiza las correcciones necesarias.</p>';
        
        $comments_html = $comments 
            ? "<div class='info-box'><h3>Comentarios de UPIS:</h3><p>{$comments}</p></div>"
            : '';
        
        $content = <<<HTML
<h2>{$icon} Solicitud {$status_text}</h2>
<p>Estimado/a <strong>{$student['full_name']}</strong>,</p>

<div class="{$box_class}">
    <p>{$message}</p>
</div>

{$comments_html}

{$next_steps}

<p>Si tienes dudas, puedes contactar a UPIS.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Estado de solicitud
     */
    public function accreditationStatusStudentPlainText($student, $status, $comments) {
        $is_approved = $status === 'approved';
        $status_text = $is_approved ? 'APROBADA' : 'REQUIERE REVISIÓN';
        
        $message = $is_approved 
            ? 'Tu solicitud de acreditación ha sido APROBADA.' 
            : 'Tu solicitud de acreditación requiere revisión.';
        
        $comments_text = $comments ? "\n\nCOMENTARIOS DE UPIS:\n{$comments}\n" : '';
        
        return <<<TEXT
SOLICITUD {$status_text} - SIEP UPIICSA

Estimado/a {$student['full_name']},

{$message}{$comments_text}

Si tienes dudas, puedes contactar a UPIS.

---
Sistema Integral de Estancias Profesionales
UPIICSA - IPN
TEXT;
    }
}