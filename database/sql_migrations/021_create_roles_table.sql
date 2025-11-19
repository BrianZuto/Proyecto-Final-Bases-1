-- Migración SQL: Crear tabla roles
-- Descripción: Tabla para almacenar los roles del sistema (Administrador, Entrenador, Deportista)

CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar roles iniciales
INSERT IGNORE INTO roles (nombre, created_at, updated_at) VALUES
('Administrador', NOW(), NOW()),
('Entrenador', NOW(), NOW()),
('Deportista', NOW(), NOW());

