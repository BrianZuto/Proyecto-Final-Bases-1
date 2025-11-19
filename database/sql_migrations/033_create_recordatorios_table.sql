-- Migración SQL: Crear tabla recordatorios
-- Descripción: Tabla para almacenar recordatorios de entrenamientos para deportistas

CREATE TABLE IF NOT EXISTS recordatorios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deportista_id BIGINT UNSIGNED NOT NULL,
    frecuencia VARCHAR(255) NOT NULL,
    estado BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_asignacion DATE NOT NULL,
    hora_alerta TIME NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_recordatorios_deportista FOREIGN KEY (deportista_id) REFERENCES deportistas(id) ON DELETE CASCADE,
    INDEX idx_deportista_id (deportista_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

