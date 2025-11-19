-- Migración SQL: Crear tabla sesiones
-- Descripción: Tabla para almacenar las sesiones de entrenamiento de los usuarios

CREATE TABLE IF NOT EXISTS sesiones (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    rutina_id BIGINT UNSIGNED NULL,
    fecha_sesion DATE NOT NULL,
    hora_inicio TIME NULL,
    hora_fin TIME NULL,
    duracion_minutos INT NULL,
    calorias_quemadas INT NULL,
    ejercicios_completados INT NOT NULL DEFAULT 0,
    estado ENUM('en_progreso', 'completada', 'cancelada') NOT NULL DEFAULT 'en_progreso',
    notas TEXT NULL,
    rendimiento TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_sesiones_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_sesiones_rutina FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_rutina_id (rutina_id),
    INDEX idx_fecha_sesion (fecha_sesion),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

