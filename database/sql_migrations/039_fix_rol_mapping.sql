-- Migración SQL: Corregir mapeo de roles (Coach -> Entrenador)
-- Descripción: Si hay usuarios con rol "Coach" en el campo rol, actualizar a "Entrenador" en roles

-- Actualizar rol_id de usuarios que tengan el campo rol = 'Coach' pero no estén mapeados correctamente
UPDATE users u
INNER JOIN roles r ON r.nombre = 'Entrenador'
SET u.rol_id = r.id
WHERE u.rol = 'Coach' AND u.rol_id != r.id;

-- Nota: Esta migración asume que el campo 'rol' todavía existe en la tabla users
-- Si ya fue eliminado, esta migración no causará errores (simplemente no hará nada)

