# 📋 Guía de Migración - Nueva Estructura de Base de Datos

Esta guía te ayudará a migrar tu proyecto a la nueva estructura de base de datos según los requerimientos funcionales.

## ⚠️ IMPORTANTE: Antes de Empezar

**Haz un respaldo completo de tu base de datos** antes de ejecutar las migraciones:

```bash
mysqldump -u root bases1 > backup_antes_migracion.sql
```

## 📝 Pasos de Migración

### Paso 1: Ejecutar las Migraciones SQL

Ejecuta todas las migraciones SQL nuevas:

```bash
php artisan migrate:sql
```

Este comando ejecutará todas las migraciones SQL en orden (001 a 039), incluyendo:
- ✅ Creación de tabla `roles`
- ✅ Actualización de `users` con `rol_id`
- ✅ Creación de tablas de herencia: `deportistas`, `entrenadores`, `administradores`
- ✅ Actualización de estructura de `rutinas`, `ejercicios`, `planes`
- ✅ Creación de nuevas tablas: `progresos`, `ejercicio_favoritos`, `recordatorios`, etc.

### Paso 2: Migrar Datos de Usuarios a Tablas de Herencia

Ejecuta el comando personalizado para migrar los usuarios existentes:

```bash
php artisan migrate:user-data
```

Este comando:
- ✅ Mapea usuarios sin `rol_id` a sus roles correspondientes
- ✅ Crea registros en `deportistas` para usuarios con rol "Deportista"
- ✅ Crea registros en `entrenadores` para usuarios con rol "Entrenador" o "Coach"
- ✅ Crea registros en `administradores` para usuarios con rol "Administrador"

### Paso 3: Verificar la Migración

Verifica que todo esté correcto:

```sql
-- Verificar que todos los usuarios tengan rol_id
SELECT COUNT(*) as usuarios_sin_rol FROM users WHERE rol_id IS NULL;
-- Debe ser 0

-- Verificar usuarios migrados a deportistas
SELECT COUNT(*) as deportistas FROM deportistas;

-- Verificar usuarios migrados a entrenadores
SELECT COUNT(*) as entrenadores FROM entrenadores;

-- Verificar usuarios migrados a administradores
SELECT COUNT(*) as administradores FROM administradores;
```

### Paso 4: Limpiar Caché de Laravel

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Paso 5: Probar la Aplicación

1. Inicia el servidor:
```bash
php artisan serve
```

2. Accede a la aplicación y verifica:
   - ✅ Login funciona correctamente
   - ✅ Los roles se cargan correctamente
   - ✅ Las rutas protegidas funcionan
   - ✅ Los usuarios pueden acceder según su rol

## 🔧 Solución de Problemas

### Error: "Column 'rol_id' cannot be null"

Esto significa que hay usuarios sin rol asignado. Solución:

```sql
-- Asignar rol "Deportista" por defecto a usuarios sin rol
UPDATE users u
INNER JOIN roles r ON r.nombre = 'Deportista'
SET u.rol_id = r.id
WHERE u.rol_id IS NULL;
```

### Error: "Foreign key constraint fails"

Verifica que la tabla `roles` tenga los roles necesarios:

```sql
SELECT * FROM roles;
```

Debe tener:
- Administrador
- Entrenador
- Deportista

Si faltan, ejecuta:

```sql
INSERT IGNORE INTO roles (nombre, created_at, updated_at) VALUES
('Administrador', NOW(), NOW()),
('Entrenador', NOW(), NOW()),
('Deportista', NOW(), NOW());
```

### Error: "Table 'detalle_rutinas' doesn't exist"

La migración 028 renombra `rutina_ejercicio` a `detalle_rutinas`. Si tienes datos existentes, verifica:

```sql
-- Verificar si existe rutina_ejercicio
SHOW TABLES LIKE 'rutina_ejercicio';

-- Si existe pero detalle_rutinas no, ejecuta manualmente:
RENAME TABLE rutina_ejercicio TO detalle_rutinas;
```

### Usuarios no aparecen en tablas de herencia

Si después de ejecutar `migrate:user-data` los usuarios no aparecen en las tablas de herencia, ejecuta manualmente:

```sql
-- Migrar a deportistas
INSERT IGNORE INTO deportistas (user_id, created_at, updated_at)
SELECT u.id, u.created_at, u.updated_at
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE r.nombre = 'Deportista';

-- Migrar a entrenadores
INSERT IGNORE INTO entrenadores (user_id, created_at, updated_at)
SELECT u.id, u.created_at, u.updated_at
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE r.nombre = 'Entrenador';

-- Migrar a administradores
INSERT IGNORE INTO administradores (user_id, created_at, updated_at)
SELECT u.id, u.created_at, u.updated_at
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE r.nombre = 'Administrador';
```

## 🗑️ Limpieza Opcional (Después de Verificar)

Una vez que hayas verificado que todo funciona correctamente, puedes eliminar el campo `rol` antiguo de la tabla `users`:

```sql
-- ⚠️ SOLO EJECUTAR DESPUÉS DE VERIFICAR QUE TODO FUNCIONA
ALTER TABLE users DROP COLUMN rol;
DROP INDEX IF EXISTS idx_rol ON users;
```

## 📊 Verificación Final

Ejecuta estas consultas para verificar que todo esté correcto:

```sql
-- Verificar estructura de roles
SELECT * FROM roles;

-- Verificar usuarios y sus roles
SELECT u.id, u.email, u.nombre_usuario, r.nombre as rol
FROM users u
LEFT JOIN roles r ON u.rol_id = r.id;

-- Verificar herencia de usuarios
SELECT 
    (SELECT COUNT(*) FROM deportistas) as total_deportistas,
    (SELECT COUNT(*) FROM entrenadores) as total_entrenadores,
    (SELECT COUNT(*) FROM administradores) as total_administradores,
    (SELECT COUNT(*) FROM users WHERE rol_id IS NOT NULL) as usuarios_con_rol;
```

## ✅ Checklist de Migración

- [ ] Respaldo de base de datos creado
- [ ] Migraciones SQL ejecutadas sin errores
- [ ] Comando `migrate:user-data` ejecutado exitosamente
- [ ] Todos los usuarios tienen `rol_id` asignado
- [ ] Usuarios migrados a tablas de herencia correctamente
- [ ] Caché de Laravel limpiado
- [ ] Aplicación probada y funcionando
- [ ] Login funciona correctamente
- [ ] Roles se aplican correctamente en rutas protegidas

## 🆘 Soporte

Si encuentras problemas durante la migración:

1. **Revisa los logs de Laravel**: `storage/logs/laravel.log`
2. **Revisa los errores de MySQL**: Verifica la consola de MySQL
3. **Restaura el respaldo** si es necesario: `mysql -u root bases1 < backup_antes_migracion.sql`

---

**¡Buena suerte con la migración!** 🚀

