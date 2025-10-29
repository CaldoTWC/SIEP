<?php
/**
 * Plantillas de Email para el Sistema SIEP
 * 
 * Contiene todas las plantillas HTML y texto plano para notificaciones
 * 
 * @package SIEP\Services
 * @version 2.0.0
 */

class EmailTemplates {
    
    /**
     * Plantilla base HTML
     */
    private function getBaseTemplate($content) {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #6f1d33 0%, #8b1538 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #6f1d33;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #6f1d33;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎓 SIEP - UPIICSA</h1>
            <p style="margin: 5px 0 0 0; font-size: 14px;">Sistema Integral de Estancias Profesionales</p>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p><strong>UPIS - Unidad Politécnica de Integración Social</strong></p>
            <p>UPIICSA - Instituto Politécnico Nacional</p>
            <p style="margin-top: 10px; font-size: 11px;">
                Este es un correo automático, por favor no responder.<br>
                Para dudas o soporte, contacta a la UPIS.
            </p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    // ========================================
    // PLANTILLAS PARA ESTUDIANTES
    // ========================================
    
    /**
     * Confirmación de recepción de documentos de acreditación
     */
    public function accreditationReceivedStudent($student, $submission) {
        $tipo_badge = $submission['tipo'] === 'A' 
            ? '<span style="background: #ff9800; color: white; padding: 5px 10px; border-radius: 15px;">TIPO A - Empresa NO Registrada</span>'
            : '<span style="background: #4caf50; color: white; padding: 5px 10px; border-radius: 15px;">TIPO B - Empresa Registrada</span>';
        
        $content = <<<HTML
<h2 style="color: #6f1d33;">✅ Documentación Recibida</h2>

<p>Estimado/a <strong>{$student['full_name']}</strong>,</p>

<p>Hemos recibido exitosamente tu documentación para la acreditación de la Estancia Profesional.</p>

<div class="info-box">
    <h3 style="margin-top: 0; color: #6f1d33;">Detalles de tu solicitud:</h3>
    <p><strong>Boleta:</strong> {$student['boleta']}</p>
    <p><strong>Carrera:</strong> {$student['career']}</p>
    <p><strong>Empresa:</strong> {$submission['empresa_nombre']}</p>
    <p><strong>Tipo de acreditación:</strong> {$tipo_badge}</p>
    <p><strong>Fecha de recepción:</strong> {$submission['created_at']}</p>
</div>

<div class="success-box">
    <p><strong>📋 Próximos pasos:</strong></p>
    <ol style="margin: 10px 0 0 20px; padding-left: 0;">
        <li>Tu documentación será revisada por la UPIS</li>
        <li>Recibirás una notificación sobre el estado de tu solicitud</li>
        <li>Si es aprobada, se generará tu constancia de acreditación</li>
    </ol>
</div>

<p style="margin-top: 20px;">El tiempo de revisión es de aproximadamente <strong>5 a 10 días hábiles</strong>.</p>

<p>Puedes consultar el estado de tu solicitud en tu panel de estudiante.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Confirmación de acreditación
     */
    public function accreditationReceivedStudentPlainText($student, $submission) {
        $tipo = $submission['tipo'] === 'A' ? 'TIPO A - Empresa NO Registrada' : 'TIPO B - Empresa Registrada';
        
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

DOCUMENTACIÓN RECIBIDA

Estimado/a {$student['full_name']},

Hemos recibido exitosamente tu documentación para la acreditación de la Estancia Profesional.

DETALLES DE TU SOLICITUD:
- Boleta: {$student['boleta']}
- Carrera: {$student['career']}
- Empresa: {$submission['empresa_nombre']}
- Tipo de acreditación: {$tipo}
- Fecha de recepción: {$submission['created_at']}

PRÓXIMOS PASOS:
1. Tu documentación será revisada por la UPIS
2. Recibirás una notificación sobre el estado de tu solicitud
3. Si es aprobada, se generará tu constancia de acreditación

El tiempo de revisión es de aproximadamente 5 a 10 días hábiles.

Puedes consultar el estado de tu solicitud en tu panel de estudiante.

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }
    
    /**
     * Notificación al estudiante: Estado de solicitud de acreditación
     */
    public function accreditationStatusStudent($student, $status, $comments) {
        $is_approved = $status === 'approved';
        $status_text = $is_approved ? 'APROBADA' : 'REQUIERE REVISIÓN';
        $box_class = $is_approved ? 'success-box' : 'warning-box';
        $icon = $is_approved ? '✅' : '⚠️';
        
        $message = $is_approved 
            ? 'Tu solicitud de acreditación ha sido aprobada.'
            : 'Tu solicitud de acreditación requiere revisión.';
        
        $next_steps = $is_approved
            ? '<p>Puedes proceder con el siguiente paso del proceso de acreditación.</p>'
            : '<p>Por favor, revisa los comentarios y realiza las correcciones necesarias.</p>';
        
        $comments_html = !empty($comments) 
            ? "<div class='info-box'><h4>Comentarios de UPIS:</h4><p>" . nl2br(htmlspecialchars($comments)) . "</p></div>"
            : '';
        
        $content = <<<HTML
<h2 style="color: #6f1d33;">{$icon} Solicitud {$status_text}</h2>

<p>Estimado/a <strong>{$student['full_name']}</strong>,</p>

<div class="{$box_class}">
    <p><strong>{$message}</strong></p>
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
        
        $comments_text = !empty($comments) 
            ? "\n\nCOMENTARIOS DE UPIS:\n{$comments}\n"
            : '';
        
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

SOLICITUD {$status_text}

Estimado/a {$student['full_name']},

{$message}
{$comments_text}

Si tienes dudas, puedes contactar a UPIS.

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }
    
    // ========================================
    // PLANTILLAS PARA EMPRESAS
    // ========================================
    
    /**
     * Confirmación de registro a empresa
     */
    public function companyRegistrationReceived($company) {
        $content = <<<HTML
<h2 style="color: #6f1d33;">✅ ¡Registro Recibido!</h2>

<p>Estimado/a <strong>{$company['contact_name']}</strong>,</p>

<p>Su solicitud de registro como empresa ha sido recibida exitosamente.</p>

<div class="info-box">
    <h3 style="margin-top: 0;">Datos registrados:</h3>
    <p><strong>Empresa:</strong> {$company['company_name']}</p>
    <p><strong>RFC:</strong> {$company['rfc']}</p>
    <p><strong>Email de contacto:</strong> {$company['email']}</p>
    <p><strong>Fecha de registro:</strong> {$company['created_at']}</p>
</div>

<p>Su solicitud será revisada por el equipo de UPIS. Recibirá una notificación cuando su cuenta sea aprobada.</p>

<div class="success-box">
    <p><strong>📋 Próximos pasos:</strong></p>
    <ol style="margin: 10px 0 0 20px; padding-left: 0;">
        <li>El equipo UPIS revisará su información</li>
        <li>Recibirá una notificación sobre el estado de su registro</li>
        <li>Una vez aprobado, podrá publicar vacantes en el sistema</li>
    </ol>
</div>

<p>Si tiene dudas, puede contactar a UPIS.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Confirmación a empresa
     */
    public function companyRegistrationReceivedPlainText($company) {
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

REGISTRO RECIBIDO

Estimado/a {$company['contact_name']},

Su solicitud de registro como empresa ha sido recibida exitosamente.

DATOS REGISTRADOS:
- Empresa: {$company['company_name']}
- RFC: {$company['rfc']}
- Email: {$company['email']}
- Fecha de registro: {$company['created_at']}

Su solicitud será revisada por el equipo de UPIS.

PRÓXIMOS PASOS:
1. El equipo UPIS revisará su información
2. Recibirá una notificación sobre el estado de su registro
3. Una vez aprobado, podrá publicar vacantes en el sistema

Si tiene dudas, puede contactar a UPIS.

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }
    
    /**
     * Notificación a empresa: Estado de registro
     */
    public function companyStatusNotification($company, $status, $comments) {
        $is_approved = $status === 'approved';
        $status_text = $is_approved ? 'APROBADA' : 'RECHAZADA';
        $box_class = $is_approved ? 'success-box' : 'warning-box';
        $icon = $is_approved ? '✅' : '❌';
        
        $message = $is_approved
            ? 'Su solicitud de registro como empresa ha sido <strong>aprobada</strong>.'
            : 'Su solicitud de registro como empresa ha sido <strong>rechazada</strong>.';
        
        $next_steps = $is_approved
            ? '<p>Ya puede acceder al sistema y publicar vacantes.</p><a href="' . getenv('SITE_URL') . '/index.php?action=login" class="btn">Iniciar Sesión</a>'
            : '<p>Puede registrarse nuevamente corrigiendo la información proporcionada.</p>';
        
        $comments_html = !empty($comments)
            ? "<div class='info-box'><h4>Comentarios de UPIS:</h4><p>" . nl2br(htmlspecialchars($comments)) . "</p></div>"
            : '';
        
        $content = <<<HTML
<h2 style="color: #6f1d33;">{$icon} Solicitud {$status_text}</h2>

<p>Estimado/a <strong>{$company['contact_name']}</strong>,</p>

<div class="{$box_class}">
    <p>{$message}</p>
</div>

{$comments_html}

{$next_steps}

<p>Si tiene dudas, puede contactar a UPIS.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Estado de registro de empresa
     */
    public function companyStatusNotificationPlainText($company, $status, $comments) {
        $is_approved = $status === 'approved';
        $status_text = $is_approved ? 'APROBADA' : 'RECHAZADA';
        
        $message = $is_approved
            ? 'Su solicitud de registro como empresa ha sido APROBADA.'
            : 'Su solicitud de registro como empresa ha sido RECHAZADA.';
        
        $next_steps = $is_approved
            ? "\nYa puede acceder al sistema y publicar vacantes."
            : "\nPuede registrarse nuevamente corrigiendo la información proporcionada.";
        
        $comments_text = !empty($comments)
            ? "\n\nCOMENTARIOS DE UPIS:\n{$comments}\n"
            : '';
        
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

SOLICITUD {$status_text}

Estimado/a {$company['contact_name']},

{$message}
{$comments_text}
{$next_steps}

Si tiene dudas, puede contactar a UPIS.

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }
    
    // ========================================
    // PLANTILLAS PARA UPIS (NOTIFICACIONES INTERNAS)
    // ========================================
    
    /**
     * Alerta a UPIS: Nueva solicitud de acreditación recibida
     */
    public function newAccreditationAlertUPIS($student, $submission) {
        $tipo_badge = $submission['tipo'] === 'A'
            ? '<span style="background: #ff9800; color: white; padding: 5px 10px; border-radius: 15px; font-weight: bold;">TIPO A</span>'
            : '<span style="background: #4caf50; color: white; padding: 5px 10px; border-radius: 15px; font-weight: bold;">TIPO B</span>';
        
        $review_url = $submission['review_url'] ?? '#';
        
        $content = <<<HTML
<h2 style="color: #6f1d33;">🔔 Nueva Solicitud de Acreditación</h2>

<p>Se ha recibido una nueva solicitud de acreditación de estancia profesional.</p>

<div class="info-box">
    <h3 style="margin-top: 0;">Datos del estudiante:</h3>
    <p><strong>Nombre:</strong> {$student['full_name']}</p>
    <p><strong>Boleta:</strong> {$student['boleta']}</p>
    <p><strong>Carrera:</strong> {$student['career']}</p>
    <p><strong>Email:</strong> {$student['email']}</p>
</div>

<div class="info-box">
    <h3 style="margin-top: 0;">Detalles de la estancia:</h3>
    <p><strong>Empresa:</strong> {$submission['empresa_nombre']}</p>
    <p><strong>Tipo:</strong> {$tipo_badge}</p>
    <p><strong>Periodo:</strong> {$submission['fecha_inicio']} a {$submission['fecha_fin']}</p>
    <p><strong>Fecha de solicitud:</strong> {$submission['created_at']}</p>
</div>

<p style="text-align: center; margin-top: 30px;">
    <a href="{$review_url}" class="btn">📋 Revisar Solicitud</a>
</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Versión texto plano: Alerta a UPIS
     */
    public function newAccreditationAlertUPISPlainText($student, $submission) {
        $tipo = $submission['tipo'] === 'A' ? 'TIPO A - Empresa NO Registrada' : 'TIPO B - Empresa Registrada';
        
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

NUEVA SOLICITUD DE ACREDITACIÓN

Se ha recibido una nueva solicitud de acreditación de estancia profesional.

DATOS DEL ESTUDIANTE:
- Nombre: {$student['full_name']}
- Boleta: {$student['boleta']}
- Carrera: {$student['career']}
- Email: {$student['email']}

DETALLES DE LA ESTANCIA:
- Empresa: {$submission['empresa_nombre']}
- Tipo: {$tipo}
- Periodo: {$submission['fecha_inicio']} a {$submission['fecha_fin']}
- Fecha de solicitud: {$submission['created_at']}

Revisar solicitud en: {$submission['review_url']}

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }

    // ========================================
// TEMPLATES PARA VACANTES
// ========================================

/**
 * Template HTML: Vacante Aprobada
 */
public function vacancyApproved($vacancy_data, $company_data, $comments = '') {
    $content = "
        <h2 style='color: #28a745;'>✅ ¡Vacante Aprobada!</h2>
        
        <p>Estimado/a equipo de <strong>{$company_data['company_name']}</strong>,</p>
        
        <p>Nos complace informarle que su vacante ha sido <strong style='color: #28a745;'>APROBADA</strong> por UPIS.</p>
        
        <div class='success-box'>
            <h3 style='margin-top: 0;'>📋 Detalles de la vacante:</h3>
            <p style='margin: 5px 0;'><strong>Título:</strong> {$vacancy_data['title']}</p>
            <p style='margin: 5px 0;'><strong>Modalidad:</strong> {$vacancy_data['modality']}</p>
            <p style='margin: 5px 0;'><strong>Plazas:</strong> {$vacancy_data['num_vacancies']}</p>
            <p style='margin: 5px 0;'><strong>Periodo:</strong> " . date('d/m/Y', strtotime($vacancy_data['start_date'])) . " - " . date('d/m/Y', strtotime($vacancy_data['end_date'])) . "</p>
        </div>
        
        " . (!empty($comments) ? "
        <div class='warning-box'>
            <strong>💬 Comentarios de UPIS:</strong><br>
            " . nl2br(htmlspecialchars($comments)) . "
        </div>
        " : "") . "
        
        <h3>¿Qué sigue?</h3>
        <ul>
            <li>Su vacante ya es visible para estudiantes en el catálogo</li>
            <li>Recibirá notificaciones cuando estudiantes se postulen</li>
            <li>Puede gestionar postulaciones desde su panel</li>
        </ul>
        
        <p style='text-align: center;'>
            <a href='http://localhost/SIEP/public/index.php?action=companyDashboard' class='btn'>
                Ver Mis Vacantes
            </a>
        </p>
    ";
    
    return $this->getBaseTemplate($content);
}

public function vacancyApprovedPlainText($vacancy_data, $company_data, $comments = '') {
    $text = "VACANTE APROBADA\n\n";
    $text .= "Estimado/a equipo de {$company_data['company_name']},\n\n";
    $text .= "Su vacante '{$vacancy_data['title']}' ha sido APROBADA.\n\n";
    $text .= "Detalles:\n";
    $text .= "- Modalidad: {$vacancy_data['modality']}\n";
    $text .= "- Plazas: {$vacancy_data['num_vacancies']}\n";
    
    if (!empty($comments)) {
        $text .= "\nComentarios de UPIS:\n{$comments}\n";
    }
    
    $text .= "\nIngrese a su panel para gestionar la vacante.\n";
    return $text;
}

/**
 * Template HTML: Vacante Rechazada
 */
public function vacancyRejected($vacancy_data, $company_data, $rejection_reason) {
    $content = "
        <h2 style='color: #f44336;'>⚠️ Vacante Requiere Correcciones</h2>
        
        <p>Estimado/a equipo de <strong>{$company_data['company_name']}</strong>,</p>
        
        <p>Le informamos que su vacante <strong>{$vacancy_data['title']}</strong> requiere correcciones antes de ser publicada.</p>
        
        <div style='background: #ffebee; border-left: 4px solid #f44336; padding: 20px; margin: 20px 0; border-radius: 4px;'>
            <strong style='color: #c62828;'>📝 Razón del rechazo:</strong><br><br>
            <div style='background: white; padding: 15px; border-radius: 5px; color: #333;'>
                " . nl2br(htmlspecialchars($rejection_reason)) . "
            </div>
        </div>
        
        <h3>¿Qué debe hacer?</h3>
        <ol>
            <li>Revise los comentarios de UPIS</li>
            <li>Corrija la información señalada</li>
            <li>Publique la vacante nuevamente</li>
        </ol>
        
        <p style='color: #666;'><em>Nota: Esta vacante no será visible para estudiantes hasta que sea aprobada.</em></p>
        
        <p style='text-align: center;'>
            <a href='http://localhost/SIEP/public/index.php?action=showPostVacancyForm' class='btn'>
                Publicar Nueva Vacante
            </a>
        </p>
    ";
    
    return $this->getBaseTemplate($content);
}

public function vacancyRejectedPlainText($vacancy_data, $company_data, $rejection_reason) {
    $text = "VACANTE REQUIERE CORRECCIONES\n\n";
    $text .= "Estimado/a equipo de {$company_data['company_name']},\n\n";
    $text .= "Su vacante '{$vacancy_data['title']}' requiere correcciones.\n\n";
    $text .= "Razón del rechazo:\n{$rejection_reason}\n\n";
    $text .= "Por favor corrija la información y vuelva a publicar.\n";
    return $text;
}

/**
 * Template HTML: Nueva Vacante para UPIS
 */
public function newVacancyUPIS($vacancy_data, $company_data) {
    $content = "
        <h2 style='color: #005a9c;'>🆕 Nueva Vacante para Revisar</h2>
        
        <p>Se ha publicado una nueva vacante que requiere revisión.</p>
        
        <div class='info-box'>
            <p style='margin: 5px 0;'><strong>🏢 Empresa:</strong> {$company_data['company_name']}</p>
            <p style='margin: 5px 0;'><strong>📋 Vacante:</strong> {$vacancy_data['title']}</p>
            <p style='margin: 5px 0;'><strong>👥 Plazas:</strong> {$vacancy_data['num_vacancies']}</p>
            <p style='margin: 5px 0;'><strong>💰 Apoyo:</strong> $" . number_format($vacancy_data['economic_support'], 2) . " MXN/mes</p>
            <p style='margin: 5px 0;'><strong>📅 Periodo:</strong> " . date('d/m/Y', strtotime($vacancy_data['start_date'])) . " - " . date('d/m/Y', strtotime($vacancy_data['end_date'])) . "</p>
        </div>
        
        <p>Por favor revise y apruebe/rechace la vacante desde el panel de administración.</p>
        
        <p style='text-align: center;'>
            <a href='http://localhost/SIEP/public/index.php?action=reviewVacancies' class='btn'>
                Revisar Vacante
            </a>
        </p>
    ";
    
    return $this->getBaseTemplate($content);
}

public function newVacancyUPISPlainText($vacancy_data, $company_data) {
    $text = "NUEVA VACANTE PENDIENTE\n\n";
    $text .= "Empresa: {$company_data['company_name']}\n";
    $text .= "Vacante: {$vacancy_data['title']}\n";
    $text .= "Plazas: {$vacancy_data['num_vacancies']}\n";
    $text .= "Apoyo: $" . number_format($vacancy_data['economic_support'], 2) . " MXN/mes\n\n";
    $text .= "Ingrese al panel para revisar.\n";
    return $text;
}

/**
 * Template HTML: Nueva Empresa para UPIS
 */
public function newCompanyUPIS($company_data) {
    $content = "
        <h2 style='color: #005a9c;'>🏢 Nueva Empresa Registrada</h2>
        
        <p>Se ha registrado una nueva empresa que requiere aprobación.</p>
        
        <div class='info-box'>
            <p style='margin: 5px 0;'><strong>Razón Social:</strong> {$company_data['company_name']}</p>
            <p style='margin: 5px 0;'><strong>RFC:</strong> {$company_data['rfc']}</p>
            <p style='margin: 5px 0;'><strong>Email:</strong> {$company_data['email']}</p>
        </div>
        
        <p>Por favor revise y apruebe/rechace desde el panel de administración.</p>
        
        <p style='text-align: center;'>
            <a href='http://localhost/SIEP/public/index.php?action=reviewCompanies' class='btn'>
                Revisar Empresa
            </a>
        </p>
    ";
    
    return $this->getBaseTemplate($content);
}

public function newCompanyUPISPlainText($company_data) {
    $text = "NUEVA EMPRESA REGISTRADA\n\n";
    $text .= "Razón Social: {$company_data['company_name']}\n";
    $text .= "RFC: {$company_data['rfc']}\n";
    $text .= "Email: {$company_data['email']}\n\n";
    $text .= "Ingrese al panel para revisar.\n";
    return $text;
}
}