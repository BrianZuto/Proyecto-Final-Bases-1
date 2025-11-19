-- Migración SQL: Crear tabla progresos
-- Descripción: Tabla para almacenar el progreso de deportistas en ejercicios de rutinas

CREATE TABLE IF NOT EXISTS progresos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deportista_id BIGINT UNSIGNED NOT NULL,
    detalle_rutina_id BIGINT UNSIGNED NOT NULL,
    fecha_registro DATE NOT NULL,
    porcentaje_completado DECIMAL(5, 2) NULL,
    rendimiento TEXT NULL,
    calorias_quemadas INT NULL,
    comentarios TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_progresos_deportista FOREIGN KEY (deportista_id) REFERENCES deportistas(id) ON DELETE CASCADE,
    CONSTRAINT fk_progresos_detalle_rutina FOREIGN KEY (detalle_rutina_id) REFERENCES detalle_rutinas(id) ON DELETE CASCADE,
    INDEX idx_deportista_id (deportista_id),
    INDEX idx_detalle_rutina_id (detalle_rutina_id),
    INDEX idx_fecha_registro (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

