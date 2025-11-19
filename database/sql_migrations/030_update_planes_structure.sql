-- Migración SQL: Actualizar estructura de planes
-- Descripción: Actualiza la tabla planes según los nuevos requerimientos

-- Agregar nuevas columnas (verificar si existen primero)
SET @col_categoria_exists = 0;
SELECT COUNT(*) INTO @col_categoria_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'planes'
AND COLUMN_NAME = 'categoria';

SET @sql_add_categoria = IF(@col_categoria_exists = 0,
    'ALTER TABLE planes ADD COLUMN categoria VARCHAR(255) NULL AFTER nombre',
    'SELECT ''Columna categoria ya existe'' as mensaje');
PREPARE stmt_cat FROM @sql_add_categoria;
EXECUTE stmt_cat;
DEALLOCATE PREPARE stmt_cat;

-- Verificar si existe duracion o duracion_dias
SET @col_duracion_exists = 0;
SET @col_duracion_dias_exists = 0;
SELECT COUNT(*) INTO @col_duracion_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'planes'
AND COLUMN_NAME = 'duracion';
SELECT COUNT(*) INTO @col_duracion_dias_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'planes'
AND COLUMN_NAME = 'duracion_dias';

-- Si existe duracion_dias, renombrar a duracion y convertir a VARCHAR
SET @sql_rename = IF(@col_duracion_dias_exists > 0 AND @col_duracion_exists = 0,
    'ALTER TABLE planes CHANGE COLUMN duracion_dias duracion VARCHAR(255) NOT NULL DEFAULT ''30 días''',
    IF(@col_duracion_exists > 0,
        'ALTER TABLE planes MODIFY COLUMN duracion VARCHAR(255) NOT NULL DEFAULT ''30 días''',
        'SELECT ''No se encontró columna duracion o duracion_dias'' as mensaje'));
PREPARE stmt_rename FROM @sql_rename;
EXECUTE stmt_rename;
DEALLOCATE PREPARE stmt_rename;

-- Agregar caracteristica después de duracion
SET @col_caracteristica_exists = 0;
SELECT COUNT(*) INTO @col_caracteristica_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'planes'
AND COLUMN_NAME = 'caracteristica';

SET @sql_add_caracteristica = IF(@col_caracteristica_exists = 0,
    'ALTER TABLE planes ADD COLUMN caracteristica TEXT NULL AFTER duracion',
    'SELECT ''Columna caracteristica ya existe'' as mensaje');
PREPARE stmt_car FROM @sql_add_caracteristica;
EXECUTE stmt_car;
DEALLOCATE PREPARE stmt_car;

-- Eliminar columnas obsoletas (comentado para seguridad)
-- ALTER TABLE planes DROP COLUMN descripcion;
-- ALTER TABLE planes DROP COLUMN precio;
-- ALTER TABLE planes DROP COLUMN activo;
-- DROP INDEX idx_activo ON planes;

