-- Migración SQL: Actualizar estructura de ejercicios
-- Descripción: Simplifica la estructura de ejercicios según requerimientos

-- Agregar material_necesario si no existe
ALTER TABLE ejercicios
ADD COLUMN material_necesario VARCHAR(255) NULL AFTER grupo_muscular;

-- Eliminar columnas que ya no se necesitan (comentado para seguridad)
-- ALTER TABLE ejercicios DROP COLUMN categoria_id;
-- ALTER TABLE ejercicios DROP COLUMN dificultad;
-- ALTER TABLE ejercicios DROP COLUMN duracion_minutos;
-- ALTER TABLE ejercicios DROP COLUMN calorias_estimadas;
-- ALTER TABLE ejercicios DROP COLUMN calificacion;
-- ALTER TABLE ejercicios DROP COLUMN equipo;
-- ALTER TABLE ejercicios DROP COLUMN instrucciones;
-- ALTER TABLE ejercicios DROP COLUMN imagen_url;
-- ALTER TABLE ejercicios DROP COLUMN video_url;
-- ALTER TABLE ejercicios DROP COLUMN activo;
-- DROP INDEX idx_categoria_id ON ejercicios;
-- DROP INDEX idx_dificultad ON ejercicios;
-- DROP INDEX idx_activo ON ejercicios;

