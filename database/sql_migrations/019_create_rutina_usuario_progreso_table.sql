-- Migración SQL: Crear tabla rutina_usuario_progreso
-- Descripción: Tabla para almacenar el progreso de los usuarios en las rutinas

CREATE TABLE IF NOT EXISTS rutina_usuario_progreso (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    rutina_id BIGINT UNSIGNED NOT NULL,
    porcentaje_completado DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
    fecha_inicio DATE NULL,
    fecha_ultima_sesion DATE NULL,
    sesiones_completadas INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    CONSTRAINT fk_rutina_progreso_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_rutina_progreso_rutina FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,

    UNIQUE KEY unique_user_rutina (user_id, rutina_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rutina_id (rutina_id),
    INDEX idx_porcentaje_completado (porcentaje_completado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

