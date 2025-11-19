-- Migración SQL: Crear tabla deportista_ejercicio
-- Descripción: Tabla pivot para relación muchos-a-muchos entre deportistas y ejercicios (puedeSer)

CREATE TABLE IF NOT EXISTS deportista_ejercicio (
    deportista_id BIGINT UNSIGNED NOT NULL,
    ejercicio_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (deportista_id, ejercicio_id),
    CONSTRAINT fk_deportista_ejercicio_deportista FOREIGN KEY (deportista_id) REFERENCES deportistas(id) ON DELETE CASCADE,
    CONSTRAINT fk_deportista_ejercicio_ejercicio FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    INDEX idx_deportista_id (deportista_id),
    INDEX idx_ejercicio_id (ejercicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

