-- Migración SQL: Crear tabla rutinas
-- Descripción: Tabla para almacenar las rutinas de entrenamiento

CREATE TABLE IF NOT EXISTS rutinas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    tipo_rutina_id BIGINT UNSIGNED NULL,
    nivel ENUM('Principiante', 'Intermedio', 'Avanzado') NOT NULL DEFAULT 'Principiante',
    tiempo_estimado_minutos INT NULL,
    calorias_estimadas INT NULL,
    imagen_url VARCHAR(500) NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (tipo_rutina_id) REFERENCES tipo_rutinas(id) ON DELETE SET NULL,
    INDEX idx_nombre (nombre),
    INDEX idx_tipo_rutina_id (tipo_rutina_id),
    INDEX idx_nivel (nivel),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

