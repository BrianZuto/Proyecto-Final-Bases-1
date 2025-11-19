-- Migración SQL: Actualizar estructura de rutinas
-- Descripción: Actualiza la tabla rutinas según los nuevos requerimientos

-- Agregar nuevas columnas
ALTER TABLE rutinas
ADD COLUMN user_id BIGINT UNSIGNED NULL AFTER id,
ADD COLUMN objetivo VARCHAR(255) NULL AFTER descripcion,
ADD COLUMN estado VARCHAR(50) NULL DEFAULT 'Borrador' AFTER nivel,
ADD COLUMN fecha_inicio DATE NULL AFTER estado,
ADD COLUMN fecha_fin DATE NULL AFTER fecha_inicio,
ADD COLUMN fecha_publicacion DATE NULL AFTER fecha_fin;

-- Agregar foreign key a users
ALTER TABLE rutinas
ADD CONSTRAINT fk_rutinas_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Crear índices
CREATE INDEX idx_user_id ON rutinas(user_id);
CREATE INDEX idx_estado ON rutinas(estado);
CREATE INDEX idx_fecha_publicacion ON rutinas(fecha_publicacion);

-- Eliminar columnas obsoletas (comentado para seguridad)
-- ALTER TABLE rutinas DROP COLUMN tipo_rutina_id;
-- ALTER TABLE rutinas DROP COLUMN tiempo_estimado_minutos;
-- ALTER TABLE rutinas DROP COLUMN calorias_estimadas;
-- ALTER TABLE rutinas DROP COLUMN imagen_url;
-- ALTER TABLE rutinas DROP COLUMN activo;
-- DROP INDEX idx_tipo_rutina_id ON rutinas;

