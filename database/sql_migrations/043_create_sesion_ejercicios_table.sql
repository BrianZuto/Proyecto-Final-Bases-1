-- Migración SQL: Crear tabla sesion_ejercicios
-- Descripción: Tabla pivot para relacionar sesiones con ejercicios completados en esa sesión

CREATE TABLE IF NOT EXISTS sesion_ejercicios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sesion_id BIGINT UNSIGNED NOT NULL,
    ejercicio_id BIGINT UNSIGNED NOT NULL,
    detalle_rutina_id BIGINT UNSIGNED NULL,
    series_completadas INT NULL,
    repeticiones_completadas VARCHAR(255) NULL,
    peso_usado VARCHAR(255) NULL,
    tiempo_segundos INT NULL,
    notas TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_sesion_ejercicios_sesion FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    CONSTRAINT fk_sesion_ejercicios_ejercicio FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    CONSTRAINT fk_sesion_ejercicios_detalle FOREIGN KEY (detalle_rutina_id) REFERENCES detalle_rutinas(id) ON DELETE SET NULL,
    
    INDEX idx_sesion_id (sesion_id),
    INDEX idx_ejercicio_id (ejercicio_id),
    INDEX idx_detalle_rutina_id (detalle_rutina_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

