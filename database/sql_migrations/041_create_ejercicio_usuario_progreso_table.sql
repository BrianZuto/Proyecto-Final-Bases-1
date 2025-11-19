-- Migración SQL: Crear tabla ejercicio_usuario_progreso
-- Descripción: Tabla para almacenar el progreso de usuarios en ejercicios individuales (fuera de rutinas)

CREATE TABLE IF NOT EXISTS ejercicio_usuario_progreso (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    ejercicio_id BIGINT UNSIGNED NOT NULL,
    fecha_ejecucion DATE NOT NULL,
    veces_completado INT NOT NULL DEFAULT 1,
    rendimiento TEXT NULL,
    calorias_quemadas INT NULL,
    comentarios TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_ejercicio_progreso_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ejercicio_progreso_ejercicio FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_ejercicio_id (ejercicio_id),
    INDEX idx_fecha_ejecucion (fecha_ejecucion),
    INDEX idx_user_ejercicio_fecha (user_id, ejercicio_id, fecha_ejecucion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

