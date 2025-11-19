-- Migración SQL: Crear tabla ejercicio_favoritos
-- Descripción: Tabla para almacenar ejercicios favoritos de deportistas con calificación

CREATE TABLE IF NOT EXISTS ejercicio_favoritos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    deportista_id BIGINT UNSIGNED NOT NULL,
    ejercicio_id BIGINT UNSIGNED NOT NULL,
    fecha_agregado DATE NOT NULL,
    nivel_gusto INT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_favoritos_deportista FOREIGN KEY (deportista_id) REFERENCES deportistas(id) ON DELETE CASCADE,
    CONSTRAINT fk_favoritos_ejercicio FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_deportista_ejercicio (deportista_id, ejercicio_id),
    INDEX idx_deportista_id (deportista_id),
    INDEX idx_ejercicio_id (ejercicio_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

