-- Migración SQL: Agregar campo estado a rutina_usuario_progreso
-- Descripción: Agrega el campo estado para rastrear el estado de la rutina (pendiente, en_progreso, completada)

ALTER TABLE rutina_usuario_progreso
ADD COLUMN estado ENUM('pendiente', 'en_progreso', 'completada') NOT NULL DEFAULT 'pendiente' AFTER porcentaje_completado;

-- Actualizar estados existentes basándose en porcentaje_completado
UPDATE rutina_usuario_progreso
SET estado = CASE
    WHEN porcentaje_completado = 0 THEN 'pendiente'
    WHEN porcentaje_completado >= 100 THEN 'completada'
    ELSE 'en_progreso'
END;

