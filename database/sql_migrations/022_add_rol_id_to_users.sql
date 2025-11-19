-- Migración SQL: Agregar rol_id a users y migrar datos
-- Descripción: Agrega rol_id a users y migra los datos del campo rol anterior

-- Agregar columna rol_id (solo si no existe)
SET @col_rol_id_exists = 0;
SELECT COUNT(*) INTO @col_rol_id_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'users'
AND COLUMN_NAME = 'rol_id';

SET @sql_add_col = IF(@col_rol_id_exists = 0,
    'ALTER TABLE users ADD COLUMN rol_id BIGINT UNSIGNED NULL AFTER direccion',
    'SELECT ''Columna rol_id ya existe'' as mensaje');
PREPARE stmt_add_col FROM @sql_add_col;
EXECUTE stmt_add_col;
DEALLOCATE PREPARE stmt_add_col;

-- Migrar datos: Asignar rol_id basado en el campo rol existente
-- Obtener IDs de roles
SET @rol_admin_id = (SELECT id FROM roles WHERE nombre = 'Administrador' LIMIT 1);
SET @rol_entrenador_id = (SELECT id FROM roles WHERE nombre = 'Entrenador' LIMIT 1);
SET @rol_deportista_id = (SELECT id FROM roles WHERE nombre = 'Deportista' LIMIT 1);

-- Verificar si el campo 'rol' existe
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'users'
AND COLUMN_NAME = 'rol';

-- Si el campo 'rol' existe, migrar los datos usuario por usuario
-- Migrar Administradores
UPDATE users SET rol_id = @rol_admin_id WHERE @col_exists > 0 AND rol = 'Administrador' AND rol_id IS NULL;

-- Migrar Entrenadores (incluyendo Coach)
UPDATE users SET rol_id = @rol_entrenador_id WHERE @col_exists > 0 AND rol IN ('Entrenador', 'Coach') AND rol_id IS NULL;

-- Migrar Deportistas
UPDATE users SET rol_id = @rol_deportista_id WHERE @col_exists > 0 AND rol = 'Deportista' AND rol_id IS NULL;

-- Asignar rol "Deportista" por defecto a usuarios sin rol_id (incluyendo NULL o valores desconocidos)
UPDATE users SET rol_id = @rol_deportista_id WHERE rol_id IS NULL;

-- Hacer rol_id NOT NULL después de migrar
ALTER TABLE users 
MODIFY COLUMN rol_id BIGINT UNSIGNED NOT NULL;

-- Agregar foreign key
ALTER TABLE users
ADD CONSTRAINT fk_users_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT;

-- Crear índice
CREATE INDEX idx_rol_id ON users(rol_id);

-- Eliminar columna rol antigua (comentado para seguridad, descomentar después de verificar)
-- ALTER TABLE users DROP COLUMN rol;
-- DROP INDEX idx_rol ON users;

