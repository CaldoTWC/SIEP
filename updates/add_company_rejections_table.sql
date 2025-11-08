-- Script de actualización de base de datos
-- Fecha: 2025-11-08
-- Descripción: Crear tabla para historial de rechazos de empresas
-- Issue #4: Módulo de gestión de empresas en UPIS

-- Crear tabla para historial de rechazos
CREATE TABLE IF NOT EXISTS `company_rejections_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL COMMENT 'Razón social de la empresa',
  `contact_email` varchar(255) NOT NULL COMMENT 'Email del contacto',
  `contact_name` varchar(255) NOT NULL COMMENT 'Nombre completo del contacto',
  `rejection_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha y hora del rechazo',
  `rejection_reason` text NOT NULL COMMENT 'Motivo del rechazo',
  `rfc` varchar(13) DEFAULT NULL COMMENT 'RFC de la empresa',
  `commercial_name` varchar(255) DEFAULT NULL COMMENT 'Nombre comercial',
  PRIMARY KEY (`id`),
  KEY `idx_email` (`contact_email`),
  KEY `idx_rejection_date` (`rejection_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial de rechazos de empresas - permite re-registro';

-- Nota: Esta tabla NO tiene relación con la tabla users para permitir
-- que las empresas rechazadas puedan volver a registrarse con el mismo email
