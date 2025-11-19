-- Migración SQL: Crear tabla entrenadores
-- Descripción: Tabla para almacenar información específica de entrenadores (herencia de tabla)

CREATE TABLE IF NOT EXISTS entrenadores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    certificaciones TEXT NULL,
    especialidad VARCHAR(255) NULL,
    centro_trabajo VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_entrenadores_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

