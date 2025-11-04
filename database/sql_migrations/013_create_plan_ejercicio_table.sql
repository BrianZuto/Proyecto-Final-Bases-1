-- Migración SQL: Crear tabla plan_ejercicio
-- Descripción: Tabla pivot para la relación muchos a muchos entre planes y ejercicios

CREATE TABLE IF NOT EXISTS plan_ejercicio (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    plan_id BIGINT UNSIGNED NOT NULL,
    ejercicio_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_plan_ejercicio_plan FOREIGN KEY (plan_id) REFERENCES planes(id) ON DELETE CASCADE,
    CONSTRAINT fk_plan_ejercicio_ejercicio FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_plan_ejercicio (plan_id, ejercicio_id),
    INDEX idx_plan_id (plan_id),
    INDEX idx_ejercicio_id (ejercicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

