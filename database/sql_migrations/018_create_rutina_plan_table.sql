-- Migración SQL: Crear tabla rutina_plan
-- Descripción: Tabla pivot para la relación muchos a muchos entre rutinas y planes

CREATE TABLE IF NOT EXISTS rutina_plan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rutina_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_rutina_plan_rutina FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
    CONSTRAINT fk_rutina_plan_plan FOREIGN KEY (plan_id) REFERENCES planes(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_rutina_plan (rutina_id, plan_id),
    INDEX idx_rutina_id (rutina_id),
    INDEX idx_plan_id (plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

