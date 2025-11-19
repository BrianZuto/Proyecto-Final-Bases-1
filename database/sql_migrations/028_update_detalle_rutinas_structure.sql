-- Migración SQL: Actualizar estructura de detalle_rutinas (antes rutina_ejercicio)
-- Descripción: Actualiza la tabla detalle_rutinas con los nuevos campos requeridos

-- Renombrar tabla si existe como rutina_ejercicio
-- RENAME TABLE rutina_ejercicio TO detalle_rutinas;

-- Agregar nuevos campos si la tabla ya existe
ALTER TABLE rutina_ejercicio
ADD COLUMN tiempo INT NULL AFTER peso,
ADD COLUMN tiempo_descanso INT NULL AFTER tiempo;

-- Renombrar tabla después de agregar campos
RENAME TABLE rutina_ejercicio TO detalle_rutinas;

-- Actualizar índices si es necesario
-- CREATE INDEX idx_rutina_id ON detalle_rutinas(rutina_id);
-- CREATE INDEX idx_ejercicio_id ON detalle_rutinas(ejercicio_id);

