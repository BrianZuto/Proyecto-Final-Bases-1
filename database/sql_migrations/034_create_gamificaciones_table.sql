-- Migración SQL: Crear tabla gamificaciones
-- Descripción: Tabla para almacenar logros y puntos de gamificación para deportistas

CREATE TABLE IF NOT EXISTS gamificaciones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deportista_id BIGINT UNSIGNED NOT NULL,
    fecha_asignacion DATE NOT NULL,
    logro VARCHAR(255) NOT NULL,
    puntos INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_gamificaciones_deportista FOREIGN KEY (deportista_id) REFERENCES deportistas(id) ON DELETE CASCADE,
    INDEX idx_deportista_id (deportista_id),
    INDEX idx_fecha_asignacion (fecha_asignacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

