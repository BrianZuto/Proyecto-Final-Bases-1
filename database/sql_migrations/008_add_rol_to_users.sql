-- Migración SQL: Agregar campo rol a la tabla users
-- Descripción: Agrega el campo rol para el sistema de roles (Administrador, Coach, Deportista)

ALTER TABLE users 
ADD COLUMN rol ENUM('Administrador', 'Coach', 'Deportista') NOT NULL DEFAULT 'Deportista' AFTER direccion;

-- Crear índice para rol
CREATE INDEX idx_rol ON users(rol);

