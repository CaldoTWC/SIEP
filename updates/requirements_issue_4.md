---
**Actualización de requerimientos:**

**Al rechazar un registro:**
- Motivo obligatorio y envío FORZOSO por correo al email del contacto. El correo deberá incluir el motivo específico ingresado por UPIS en el mensaje.
- No guardar "quién" rechazó (solo fecha/motivo/nombre/email).
- Permitir que la empresa pueda registrarse nuevamente con el mismo email; mantener el historial de rechazos.

**Notas para implementación:**
- Validar campo motivo como obligatorio en la vista de rechazo de empresa.
- El correo debe tener formato concreto, por ejemplo:
  - Asunto: "Su solicitud de registro en SIEP ha sido rechazada"
  - Cuerpo: Incluir motivo específico ingresado
- Asegurarse de enviarlo a email de contacto inmediatamente después de registrar la acción.

**Estimación:** 8-10 horas de desarrollo
