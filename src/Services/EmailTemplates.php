/**
 * Notificación al estudiante: Estado de solicitud de acreditación
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
    
    $comments_html = !empty($comments)
        ? "<div class='info-box'><h3>Comentarios de UPIS:</h3><p>" . nl2br(htmlspecialchars($comments)) . "</p></div>"
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
    
    $comments_text = !empty($comments) ? "\n\nCOMENTARIOS DE UPIS:\n{$comments}\n" : '';
    
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

// ========================================================================
// PLANTILLAS PARA EMPRESAS
// ========================================================================

/**
 * Confirmación a empresa: Registro recibido
 */
public function companyRegistrationReceived($company) {
    $content = <<<HTML
<h2>¡Registro Recibido!</h2>
<p>Estimado/a <strong>{$company['contact_name']}</strong>,</p>

<div class="success-box">
    <p>Su solicitud de registro como empresa ha sido recibida exitosamente.</p>
</div>

<div class="info-box">
    <h3>Datos registrados:</h3>
    <p><strong>Empresa:</strong> {$company['company_name']}</p>
    <p><strong>RFC:</strong> {$company['rfc']}</p>
    <p><strong>Email de contacto:</strong> {$company['email']}</p>
    <p><strong>Fecha de registro:</strong> {$company['created_at']}</p>
</div>

<p>Su solicitud será revisada por el equipo de UPIS. Recibirá una notificación cuando su cuenta sea aprobada.</p>

<p><strong>Próximos pasos:</strong></p>
<ol>
    <li>El equipo UPIS revisará su información</li>
    <li>Recibirá una notificación sobre el estado de su registro</li>
    <li>Una vez aprobado, podrá publicar vacantes en el sistema</li>
</ol>

<p>Si tiene dudas, puede contactar a UPIS.</p>
HTML;
    
    return $this->getBaseTemplate($content);
}

/**
 * Versión texto plano: Confirmación a empresa
 */
public function companyRegistrationReceivedPlainText($company) {
    return <<<TEXT
REGISTRO RECIBIDO - SIEP UPIICSA

Estimado/a {$company['contact_name']},

Su solicitud de registro como empresa ha sido recibida exitosamente.

DATOS REGISTRADOS:
Empresa: {$company['company_name']}
RFC: {$company['rfc']}
Email de contacto: {$company['email']}
Fecha de registro: {$company['created_at']}

Su solicitud será revisada por el equipo de UPIS. Recibirá una notificación cuando su cuenta sea aprobada.

PRÓXIMOS PASOS:
1. El equipo UPIS revisará su información
2. Recibirá una notificación sobre el estado de su registro
3. Una vez aprobado, podrá publicar vacantes en el sistema

Si tiene dudas, puede contactar a UPIS.

---
Sistema Integral de Estancias Profesionales
UPIICSA - IPN
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
        ? '<p>Ya puede acceder al sistema y publicar vacantes.</p><p><a href="' . getenv('SITE_URL') . '/index.php?action=showLogin" class="btn">Iniciar Sesión</a></p>'
        : '<p>Puede registrarse nuevamente corrigiendo la información proporcionada.</p>';
    
    $comments_html = !empty($comments)
        ? "<div class='info-box'><h3>Comentarios de UPIS:</h3><p>" . nl2br(htmlspecialchars($comments)) . "</p></div>"
        : '';
    
    $content = <<<HTML
<h2>{$icon} Solicitud {$status_text}</h2>
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
    
    $comments_text = !empty($comments) ? "\n\nCOMENTARIOS DE UPIS:\n{$comments}\n" : '';
    
    return <<<TEXT
SOLICITUD {$status_text} - SIEP UPIICSA

Estimado/a {$company['contact_name']},

{$message}{$comments_text}{$next_steps}

Si tiene dudas, puede contactar a UPIS.

---
Sistema Integral de Estancias Profesionales
UPIICSA - IPN
TEXT;
}