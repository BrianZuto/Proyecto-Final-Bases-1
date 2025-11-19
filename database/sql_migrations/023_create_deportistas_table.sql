-- Migración SQL: Crear tabla deportistas
-- Descripción: Tabla para almacenar información específica de deportistas (herencia de tabla)

CREATE TABLE IF NOT EXISTS deportistas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    entrenador_id BIGINT UNSIGNED NULL,
    peso DECIMAL(5, 2) NULL,
    genero VARCHAR(50) NULL,
    altura DECIMAL(5, 2) NULL,
    fecha_nacimiento DATE NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_deportistas_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_entrenador_id (entrenador_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

