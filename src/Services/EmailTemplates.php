<?php
/**
 * Plantillas de Email Simplificadas - SIEP
 * 
 * Solo contiene las plantillas esenciales:
 * 1. Rechazo de empresa (con motivo detallado)
 * 2. Notificación genérica
 * 
 * @package SIEP\Services
 * @version 4.0.0 - Simplificado para Issue #4
 * @date 2025-11-09
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
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .danger-box {
            background: #ffebee;
            border-left: 4px solid #f44336;
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
    // PLANTILLA: RECHAZO DE EMPRESA
    // ========================================
    
    /**
     * Notificación de rechazo de empresa (HTML)
     */
    public function companyStatusNotification($company, $status, $rejection_reason) {
        $content = <<<HTML
<h2 style="color: #f44336;">❌ Registro de Empresa Rechazado</h2>

<p>Estimado/a <strong>{$company['contact_name']}</strong>,</p>

<p>Lamentamos informarle que su solicitud de registro de la empresa <strong>{$company['company_name']}</strong> 
ha sido <strong style="color: #f44336;">RECHAZADA</strong> por UPIS.</p>

<div class="danger-box">
    <h3 style="margin-top: 0; color: #c62828;">📋 Motivo del rechazo:</h3>
    <div style="background: white; padding: 15px; border-radius: 5px; margin-top: 10px;">
        <p style="margin: 0; white-space: pre-wrap;">{$rejection_reason}</p>
    </div>
</div>

<div class="info-box">
    <h3 style="margin-top: 0;">Datos de su solicitud:</h3>
    <p style="margin: 5px 0;"><strong>Empresa:</strong> {$company['company_name']}</p>
    <p style="margin: 5px 0;"><strong>RFC:</strong> {$company['rfc']}</p>
    <p style="margin: 5px 0;"><strong>Email:</strong> {$company['email']}</p>
</div>

<h3>¿Qué puede hacer?</h3>
<ol>
    <li>Revise cuidadosamente el motivo del rechazo</li>
    <li>Corrija la información señalada</li>
    <li>Vuelva a registrarse con los datos correctos</li>
</ol>

<div class="warning-box">
    <p><strong>💡 Nota importante:</strong> Puede registrarse nuevamente utilizando el mismo correo electrónico 
    una vez que haya corregido la información.</p>
</div>

<p style="text-align: center; margin-top: 30px;">
    <a href="http://localhost/SIEP/public/index.php?action=showCompanyRegisterForm" class="btn">
        Registrarse Nuevamente
    </a>
</p>

<p style="margin-top: 30px;">Si tiene dudas, puede contactar a UPIS para más información.</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Notificación de rechazo de empresa (Texto plano)
     */
    public function companyStatusNotificationPlainText($company, $status, $rejection_reason) {
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

REGISTRO DE EMPRESA RECHAZADO

Estimado/a {$company['contact_name']},

Lamentamos informarle que su solicitud de registro de la empresa {$company['company_name']} 
ha sido RECHAZADA por UPIS.

MOTIVO DEL RECHAZO:
{$rejection_reason}

DATOS DE SU SOLICITUD:
- Empresa: {$company['company_name']}
- RFC: {$company['rfc']}
- Email: {$company['email']}

¿QUÉ PUEDE HACER?
1. Revise cuidadosamente el motivo del rechazo
2. Corrija la información señalada
3. Vuelva a registrarse con los datos correctos

NOTA: Puede registrarse nuevamente utilizando el mismo correo electrónico.

Registrarse nuevamente: http://localhost/SIEP/public/index.php?action=showCompanyRegisterForm

Si tiene dudas, puede contactar a UPIS para más información.

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }
    
    // ========================================
    // PLANTILLA: NOTIFICACIÓN GENÉRICA
    // ========================================
    
    /**
     * Notificación genérica (HTML)
     */
    public function genericNotification($name, $notification_type = 'general') {
        $content = <<<HTML
<h2 style="color: #6f1d33;">🔔 Nueva Notificación</h2>

<p>Hola <strong>{$name}</strong>,</p>

<p>Tienes una nueva notificación en tu cuenta del Sistema Integral de Estancias Profesionales (SIEP).</p>

<div class="info-box">
    <p style="margin: 0;"><strong>Por favor, ingresa a la plataforma para revisar los detalles.</strong></p>
</div>

<p style="text-align: center; margin-top: 30px;">
    <a href="http://localhost/SIEP/public/index.php?action=showLogin" class="btn">
        Ir al SIEP
    </a>
</p>
HTML;
        
        return $this->getBaseTemplate($content);
    }
    
    /**
     * Notificación genérica (Texto plano)
     */
    public function genericNotificationPlainText($name, $notification_type = 'general') {
        return <<<TEXT
SIEP - UPIICSA
Sistema Integral de Estancias Profesionales

NUEVA NOTIFICACIÓN

Hola {$name},

Tienes una nueva notificación en tu cuenta del SIEP.

Por favor, ingresa a la plataforma para revisar los detalles.

Ir al SIEP: http://localhost/SIEP/public/index.php?action=showLogin

---
UPIS - Unidad Politécnica de Integración Social
UPIICSA - Instituto Politécnico Nacional
TEXT;
    }
}