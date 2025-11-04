-- Migración SQL: Crear tabla rutina_ejercicio
-- Descripción: Tabla pivot para la relación muchos a muchos entre rutinas y ejercicios

CREATE TABLE IF NOT EXISTS rutina_ejercicio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rutina_id BIGINT UNSIGNED NOT NULL,
    ejercicio_id BIGINT UNSIGNED NOT NULL,
    orden INT NOT NULL DEFAULT 0,
    series INT NULL,
    repeticiones VARCHAR(255) NULL,
    peso VARCHAR(255) NULL,
    descanso_segundos INT NULL,
    notas TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_rutina_ejercicio_rutina FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
    CONSTRAINT fk_rutina_ejercicio_ejercicio FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    
    INDEX idx_rutina_id (rutina_id),
    INDEX idx_ejercicio_id (ejercicio_id),
    INDEX idx_orden (orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

