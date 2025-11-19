-- Migración SQL: Agregar FK de entrenador a deportistas
-- Descripción: Agrega la relación entre deportistas y entrenadores

ALTER TABLE deportistas
ADD CONSTRAINT fk_deportistas_entrenador FOREIGN KEY (entrenador_id) REFERENCES entrenadores(id) ON DELETE SET NULL;

