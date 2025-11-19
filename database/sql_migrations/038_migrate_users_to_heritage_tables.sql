-- Migración SQL: Migrar usuarios existentes a tablas de herencia
-- Descripción: Crea registros en deportistas, entrenadores, administradores basados en los roles de usuarios existentes

-- Migrar usuarios con rol "Deportista" a tabla deportistas
INSERT IGNORE INTO deportistas (user_id, created_at, updated_at)
SELECT u.id, u.created_at, u.updated_at
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE r.nombre = 'Deportista'
AND NOT EXISTS (
    SELECT 1 FROM deportistas d WHERE d.user_id = u.id
);

-- Migrar usuarios con rol "Entrenador" o "Coach" a tabla entrenadores
INSERT IGNORE INTO entrenadores (user_id, created_at, updated_at)
SELECT u.id, u.created_at, u.updated_at
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE r.nombre IN ('Entrenador', 'Coach')
AND NOT EXISTS (
    SELECT 1 FROM entrenadores e WHERE e.user_id = u.id
);

-- Migrar usuarios con rol "Administrador" a tabla administradores
INSERT IGNORE INTO administradores (user_id, created_at, updated_at)
SELECT u.id, u.created_at, u.updated_at
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE r.nombre = 'Administrador'
AND NOT EXISTS (
    SELECT 1 FROM administradores a WHERE a.user_id = u.id
);

