-- Migración SQL: Crear tabla contenido
-- Descripción: Tabla para almacenar contenido educativo, artículos, videos y recursos

CREATE TABLE IF NOT EXISTS contenido (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    contenido TEXT NOT NULL,
    tipo ENUM('articulo', 'video', 'infografia', 'recurso', 'consejo') NOT NULL DEFAULT 'articulo',
    categoria VARCHAR(100) NULL,
    imagen_url VARCHAR(500) NULL,
    video_url VARCHAR(500) NULL,
    archivo_url VARCHAR(500) NULL,
    autor_id BIGINT UNSIGNED NULL,
    publicado BOOLEAN NOT NULL DEFAULT FALSE,
    fecha_publicacion DATE NULL,
    vistas INT NOT NULL DEFAULT 0,
    likes INT NOT NULL DEFAULT 0,
    tags VARCHAR(500) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT fk_contenido_autor FOREIGN KEY (autor_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_tipo (tipo),
    INDEX idx_categoria (categoria),
    INDEX idx_publicado (publicado),
    INDEX idx_fecha_publicacion (fecha_publicacion),
    INDEX idx_autor_id (autor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

