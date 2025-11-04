-- Migración SQL: Crear tabla tipo_rutinas
-- Descripción: Tabla para almacenar los tipos de rutinas (Fuerza, Cardio, Body, etc.)

CREATE TABLE IF NOT EXISTS tipo_rutinas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    descripcion TEXT NULL,
    icono VARCHAR(255) NULL,
    color VARCHAR(50) NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

