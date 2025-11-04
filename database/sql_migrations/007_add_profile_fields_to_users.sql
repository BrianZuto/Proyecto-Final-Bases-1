-- Migración SQL: Agregar campos de perfil a la tabla users
-- Descripción: Agrega campos adicionales para completar el perfil del usuario

ALTER TABLE users
ADD COLUMN primer_nombre VARCHAR(255) NULL AFTER name,
ADD COLUMN segundo_nombre VARCHAR(255) NULL AFTER primer_nombre,
ADD COLUMN primer_apellido VARCHAR(255) NULL AFTER segundo_nombre,
ADD COLUMN segundo_apellido VARCHAR(255) NULL AFTER primer_apellido,
ADD COLUMN nombre_usuario VARCHAR(255) NULL UNIQUE AFTER segundo_apellido,
ADD COLUMN telefonos VARCHAR(255) NULL AFTER nombre_usuario,
ADD COLUMN direccion TEXT NULL AFTER telefonos;

-- Crear índice para nombre_usuario
CREATE INDEX idx_nombre_usuario ON users(nombre_usuario);

