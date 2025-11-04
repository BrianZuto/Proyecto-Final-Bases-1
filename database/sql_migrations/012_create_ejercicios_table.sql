-- Migración SQL: Crear tabla ejercicios
-- Descripción: Tabla para almacenar los ejercicios del sistema

CREATE TABLE IF NOT EXISTS ejercicios (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    categoria_id BIGINT UNSIGNED NULL,
    grupo_muscular VARCHAR(255) NULL,
    dificultad ENUM('Principiante', 'Intermedio', 'Avanzado') NOT NULL DEFAULT 'Principiante',
    duracion_minutos INT NULL,
    calorias_estimadas INT NULL,
    calificacion DECIMAL(3, 1) NULL DEFAULT 0.0,
    equipo VARCHAR(255) NULL,
    instrucciones TEXT NULL,
    imagen_url VARCHAR(500) NULL,
    video_url VARCHAR(500) NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    INDEX idx_nombre (nombre),
    INDEX idx_categoria_id (categoria_id),
    INDEX idx_grupo_muscular (grupo_muscular),
    INDEX idx_dificultad (dificultad),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

