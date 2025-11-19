# 📚 README SQL - Categorización de Consultas por Dificultad

Este documento categoriza todas las consultas SQL del proyecto según su nivel de dificultad: **Fácil**, **Media** y **Difícil**.

---

## 📍 Ubicación de las Consultas

Todas las consultas SQL se encuentran en los controladores dentro de la carpeta:
```
app/Http/Controllers/
```

---

## 🟢 CONSULTAS FÁCILES

Consultas simples con SELECT básico, INSERT, UPDATE o DELETE directos sin JOINs complejos.

### DashboardController.php

**Línea ~27-29**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple con WHERE

**Línea ~45-49**: Contar rutinas completadas
```sql
SELECT COUNT(*) as total 
FROM rutina_usuario_progreso 
WHERE user_id = ? 
AND estado = 'completada' 
AND fecha_ultima_sesion BETWEEN ? AND ?
```
**Dificultad**: Fácil - COUNT con WHERE y BETWEEN

**Línea ~52-54**: Contar total de rutinas
```sql
SELECT COUNT(*) as total 
FROM rutina_usuario_progreso 
WHERE user_id = ?
```
**Dificultad**: Fácil - COUNT simple

**Línea ~57-61**: Sumar calorías semana
```sql
SELECT COALESCE(SUM(calorias_quemadas), 0) as total 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
AND fecha_sesion BETWEEN ? AND ?
```
**Dificultad**: Fácil - SUM con WHERE

**Línea ~66-70**: Sumar calorías semana pasada
```sql
SELECT COALESCE(SUM(calorias_quemadas), 0) as total 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
AND fecha_sesion BETWEEN ? AND ?
```
**Dificultad**: Fácil - SUM con WHERE

**Línea ~77-81**: Sumar tiempo semana
```sql
SELECT COALESCE(SUM(duracion_minutos), 0) as total 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
AND fecha_sesion BETWEEN ? AND ?
```
**Dificultad**: Fácil - SUM con WHERE

**Línea ~32-36**: Insertar deportista
```sql
INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

### ProfileController.php

**Línea ~50-52**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~65**: Contar sesiones totales
```sql
SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'
```
**Dificultad**: Fácil - COUNT con WHERE

**Línea ~69**: Contar sesiones del mes
```sql
SELECT COUNT(*) as total FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
AND YEAR(fecha_sesion) = ? 
AND MONTH(fecha_sesion) = ?
```
**Dificultad**: Fácil - COUNT con funciones de fecha

**Línea ~76**: Contar logros
```sql
SELECT COUNT(*) as total FROM gamificaciones WHERE deportista_id = ?
```
**Dificultad**: Fácil - COUNT simple

**Línea ~188-200**: Actualizar usuario
```sql
UPDATE users SET 
    primer_nombre = ?, 
    segundo_nombre = ?, 
    ...
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

### RutinaController.php

**Línea ~454**: Obtener rutina
```sql
SELECT * FROM rutinas WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~273-275**: Obtener listas simples
```sql
SELECT * FROM tipo_rutinas WHERE activo = 1 ORDER BY nombre
SELECT * FROM planes WHERE activo = 1 ORDER BY nombre
SELECT * FROM ejercicios WHERE activo = 1 ORDER BY nombre
```
**Dificultad**: Fácil - SELECT con ORDER BY

**Línea ~307-319**: Insertar rutina
```sql
INSERT INTO rutinas (nombre, descripcion, ...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~542**: Eliminar ejercicios de rutina
```sql
DELETE FROM detalle_rutinas WHERE rutina_id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~591-593**: Verificar rutina existe
```sql
SELECT * FROM rutinas WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~599-603**: Contar progreso
```sql
SELECT COUNT(*) as total
FROM rutina_usuario_progreso
WHERE rutina_id = ?
```
**Dificultad**: Fácil - COUNT simple

**Línea ~612-614**: Eliminar relaciones
```sql
DELETE FROM detalle_rutinas WHERE rutina_id = ?
DELETE FROM rutina_plan WHERE rutina_id = ?
DELETE FROM rutinas WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~635**: Verificar rutina existe
```sql
SELECT * FROM rutinas WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~641-644**: Obtener progreso
```sql
SELECT * FROM rutina_usuario_progreso
WHERE user_id = ? AND rutina_id = ?
```
**Dificultad**: Fácil - SELECT con WHERE

**Línea ~653-659**: Actualizar progreso
```sql
UPDATE rutina_usuario_progreso SET
    estado = 'en_progreso',
    fecha_ultima_sesion = ?,
    updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~662-678**: Insertar progreso
```sql
INSERT INTO rutina_usuario_progreso (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~729**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~744-747**: Obtener progreso rutina
```sql
SELECT * FROM rutina_usuario_progreso
WHERE user_id = ? AND rutina_id = ?
```
**Dificultad**: Fácil - SELECT con WHERE

**Línea ~800**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~804-808**: Insertar deportista
```sql
INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~831-834**: Verificar progreso existe
```sql
SELECT * FROM progresos
WHERE deportista_id = ? AND detalle_rutina_id = ? AND fecha_registro = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~838-843**: Actualizar progreso
```sql
UPDATE progresos SET
    porcentaje_completado = 100.00,
    updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~846-851**: Insertar progreso
```sql
INSERT INTO progresos (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~878-881**: Obtener progreso
```sql
SELECT * FROM rutina_usuario_progreso
WHERE user_id = ? AND rutina_id = ?
```
**Dificultad**: Fácil - SELECT con WHERE

**Línea ~884-891**: Actualizar progreso rutina
```sql
UPDATE rutina_usuario_progreso SET
    sesiones_completadas = sesiones_completadas + 1,
    fecha_ultima_sesion = ?,
    estado = 'completada',
    updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE con operación aritmética

**Línea ~898**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~914-916**: Contar ejercicios rutina
```sql
SELECT COUNT(*) as total FROM detalle_rutinas WHERE rutina_id = ?
```
**Dificultad**: Fácil - COUNT simple

**Línea ~921**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~925-930**: Insertar deportista
```sql
INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~937-944**: Contar ejercicios completados
```sql
SELECT COUNT(DISTINCT p.detalle_rutina_id) as total
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
WHERE p.deportista_id = ?
AND dr.rutina_id = ?
AND p.porcentaje_completado >= 100
```
**Dificultad**: Media - COUNT con JOIN y DISTINCT

**Línea ~958-964**: Actualizar progreso rutina
```sql
UPDATE rutina_usuario_progreso SET
    porcentaje_completado = ?,
    estado = ?,
    updated_at = ?
WHERE user_id = ? AND rutina_id = ?
```
**Dificultad**: Fácil - UPDATE con múltiples WHERE

### EjercicioController.php

**Línea ~158**: Obtener categorías
```sql
SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre
```
**Dificultad**: Fácil - SELECT con ORDER BY

**Línea ~239-247**: Obtener ejercicio con categoría
```sql
SELECT 
    e.*,
    c.nombre as categoria_nombre,
    c.color as categoria_color
FROM ejercicios e
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE e.id = ?
```
**Dificultad**: Media - SELECT con LEFT JOIN

**Línea ~272-275**: Obtener progreso hoy
```sql
SELECT * FROM ejercicio_usuario_progreso 
WHERE user_id = ? AND ejercicio_id = ? AND fecha_ejecucion = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~277-281**: Sumar veces completado
```sql
SELECT SUM(veces_completado) as total 
FROM ejercicio_usuario_progreso 
WHERE user_id = ? AND ejercicio_id = ?
```
**Dificultad**: Fácil - SUM con WHERE

**Línea ~305**: Verificar ejercicio existe
```sql
SELECT * FROM ejercicios WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~313-316**: Verificar progreso existe
```sql
SELECT * FROM ejercicio_usuario_progreso 
WHERE user_id = ? AND ejercicio_id = ? AND fecha_ejecucion = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~320-328**: Actualizar progreso
```sql
UPDATE ejercicio_usuario_progreso SET 
    veces_completado = veces_completado + 1,
    ...
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE con operación aritmética

**Línea ~337-352**: Insertar progreso
```sql
INSERT INTO ejercicio_usuario_progreso (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~366**: Obtener ejercicio
```sql
SELECT * FROM ejercicios WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~372-373**: Obtener listas
```sql
SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre
SELECT * FROM planes WHERE activo = 1 ORDER BY nombre
```
**Dificultad**: Fácil - SELECT con ORDER BY

**Línea ~375**: Obtener planes asignados
```sql
SELECT plan_id FROM plan_ejercicio WHERE ejercicio_id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~392**: Verificar ejercicio existe
```sql
SELECT * FROM ejercicios WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~417-440**: Actualizar ejercicio
```sql
UPDATE ejercicios SET 
    nombre = ?, descripcion = ?, ...
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~443**: Eliminar planes ejercicio
```sql
DELETE FROM plan_ejercicio WHERE ejercicio_id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~445-450**: Insertar plan ejercicio
```sql
INSERT INTO plan_ejercicio (plan_id, ejercicio_id, created_at, updated_at)
VALUES (?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~467**: Verificar ejercicio existe
```sql
SELECT * FROM ejercicios WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~475-479**: Eliminar relaciones
```sql
DELETE FROM plan_ejercicio WHERE ejercicio_id = ?
DELETE FROM detalle_rutinas WHERE ejercicio_id = ?
DELETE FROM ejercicios WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

### SesionController.php

**Línea ~237**: Obtener sesión
```sql
SELECT * FROM sesiones WHERE id = ? AND user_id = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~276**: Verificar sesión existe
```sql
SELECT * FROM sesiones WHERE id = ? AND user_id = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~290-309**: Actualizar sesión
```sql
UPDATE sesiones SET 
    rutina_id = ?, fecha_sesion = ?, ...
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~323**: Obtener sesión
```sql
SELECT * FROM sesiones WHERE id = ? AND user_id = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~346-353**: Actualizar sesión
```sql
UPDATE sesiones SET 
    estado = 'completada',
    hora_fin = ?,
    duracion_minutos = ?,
    updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~356**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~374**: Verificar sesión existe
```sql
SELECT * FROM sesiones WHERE id = ? AND user_id = ?
```
**Dificultad**: Fácil - SELECT con múltiples WHERE

**Línea ~381**: Eliminar ejercicios sesión
```sql
DELETE FROM sesion_ejercicios WHERE sesion_id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~384**: Eliminar sesión
```sql
DELETE FROM sesiones WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~399**: Obtener rutina
```sql
SELECT * FROM rutinas WHERE id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~405**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~410-418**: Contar ejercicios completados
```sql
SELECT COUNT(*) as total
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
WHERE p.deportista_id = ?
AND dr.rutina_id = ?
AND p.fecha_registro = ?
AND p.porcentaje_completado = 100
```
**Dificultad**: Media - COUNT con JOIN

**Línea ~421-427**: Insertar sesión
```sql
INSERT INTO sesiones (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~445-453**: Obtener ejercicios progreso
```sql
SELECT dr.ejercicio_id, dr.id as detalle_rutina_id
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
WHERE p.deportista_id = ?
AND dr.rutina_id = ?
AND p.fecha_registro = ?
AND p.porcentaje_completado = 100
```
**Dificultad**: Media - SELECT con JOIN

**Línea ~456-459**: Insertar ejercicio sesión
```sql
INSERT INTO sesion_ejercicios (sesion_id, ejercicio_id, detalle_rutina_id, created_at, updated_at)
VALUES (?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

### ContenidoController.php

**Línea ~86-90**: Obtener categorías
```sql
SELECT DISTINCT categoria 
FROM contenido 
WHERE categoria IS NOT NULL
```
**Dificultad**: Fácil - SELECT DISTINCT

**Línea ~97-100**: Estadísticas
```sql
SELECT COUNT(*) as total FROM contenido
SELECT COUNT(*) as total FROM contenido WHERE publicado = 1
SELECT COUNT(*) as total FROM contenido WHERE publicado = 0
SELECT tipo, COUNT(*) as cantidad FROM contenido GROUP BY tipo
```
**Dificultad**: Fácil - COUNT y GROUP BY simple

**Línea ~143**: Verificar slug existe
```sql
SELECT COUNT(*) as total FROM contenido WHERE slug = ?
```
**Dificultad**: Fácil - COUNT simple

**Línea ~153-176**: Insertar contenido
```sql
INSERT INTO contenido (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~192-201**: Obtener contenido con autor
```sql
SELECT 
    c.*,
    u.name as autor_nombre,
    u.primer_nombre,
    u.primer_apellido
FROM contenido c
LEFT JOIN users u ON c.autor_id = u.id
WHERE c.id = ?
```
**Dificultad**: Media - SELECT con LEFT JOIN

**Línea ~215**: Incrementar vistas
```sql
UPDATE contenido SET vistas = vistas + 1 WHERE id = ?
```
**Dificultad**: Fácil - UPDATE con operación aritmética

**Línea ~218-225**: Obtener contenido relacionado
```sql
SELECT * 
FROM contenido 
WHERE id != ? 
AND publicado = 1 
AND (tipo = ? OR categoria = ?)
LIMIT 4
```
**Dificultad**: Fácil - SELECT con múltiples condiciones

**Línea ~238**: Obtener contenido
```sql
SELECT * FROM contenido WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~274**: Obtener contenido
```sql
SELECT * FROM contenido WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~291**: Verificar slug existe
```sql
SELECT COUNT(*) as total FROM contenido WHERE slug = ? AND id != ?
```
**Dificultad**: Fácil - COUNT con WHERE

**Línea ~300-320**: Actualizar contenido
```sql
UPDATE contenido SET 
    titulo = ?, slug = ?, ...
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~334**: Obtener contenido
```sql
SELECT * FROM contenido WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~345**: Eliminar contenido
```sql
DELETE FROM contenido WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~356**: Incrementar likes
```sql
UPDATE contenido SET likes = likes + 1 WHERE id = ?
```
**Dificultad**: Fácil - UPDATE con operación aritmética

**Línea ~358**: Obtener likes
```sql
SELECT likes FROM contenido WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

### ClienteController.php

**Línea ~166**: Obtener rol
```sql
SELECT * FROM roles WHERE nombre = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~182-202**: Insertar usuario
```sql
INSERT INTO users (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~208-213**: Insertar en tablas herencia
```sql
INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)
INSERT INTO entrenadores (user_id, created_at, updated_at) VALUES (?, ?, ?)
INSERT INTO administradores (user_id, created_at, updated_at) VALUES (?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~269**: Obtener rol
```sql
SELECT * FROM roles WHERE nombre = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~276-281**: Obtener rol actual
```sql
SELECT r.nombre as rol_nombre
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE u.id = ?
```
**Dificultad**: Media - SELECT con JOIN

**Línea ~295-316**: Actualizar usuario
```sql
UPDATE users SET 
    name = ?, primer_nombre = ?, ...
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~343-345**: Eliminar de tablas herencia
```sql
DELETE FROM deportistas WHERE user_id = ?
DELETE FROM entrenadores WHERE user_id = ?
DELETE FROM administradores WHERE user_id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~348-354**: Insertar en tabla herencia
```sql
INSERT INTO deportistas (user_id, created_at, updated_at) VALUES (?, ?, ?)
INSERT INTO entrenadores (user_id, created_at, updated_at) VALUES (?, ?, ?)
INSERT INTO administradores (user_id, created_at, updated_at) VALUES (?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~358**: Desactivar planes
```sql
UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1
```
**Dificultad**: Fácil - UPDATE con múltiples WHERE

**Línea ~364**: Obtener plan
```sql
SELECT * FROM planes WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~368**: Desactivar planes
```sql
UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1
```
**Dificultad**: Fácil - UPDATE con múltiples WHERE

**Línea ~376-379**: Insertar plan usuario
```sql
INSERT INTO plan_usuario (user_id, plan_id, fecha_inicio, fecha_fin, activo, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~406-410**: Verificar plan activo
```sql
SELECT COUNT(*) as total 
FROM plan_usuario 
WHERE user_id = ? AND activo = 1 AND fecha_fin >= ?
```
**Dificultad**: Fácil - COUNT con múltiples WHERE

**Línea ~417-421**: Eliminar relaciones
```sql
DELETE FROM plan_usuario WHERE user_id = ?
DELETE FROM rutina_usuario_progreso WHERE user_id = ?
DELETE FROM users WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

**Línea ~438**: Desactivar plan
```sql
UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1
```
**Dificultad**: Fácil - UPDATE con múltiples WHERE

**Línea ~448-453**: Obtener usuario con rol
```sql
SELECT u.id, r.nombre as rol_nombre
FROM users u
INNER JOIN roles r ON u.rol_id = r.id
WHERE u.id = ?
```
**Dificultad**: Media - SELECT con JOIN

**Línea ~463**: Obtener plan
```sql
SELECT * FROM planes WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~472**: Desactivar planes
```sql
UPDATE plan_usuario SET activo = 0 WHERE user_id = ? AND activo = 1
```
**Dificultad**: Fácil - UPDATE con múltiples WHERE

**Línea ~480-483**: Insertar plan usuario
```sql
INSERT INTO plan_usuario (user_id, plan_id, fecha_inicio, fecha_fin, activo, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

### PlanController.php

**Línea ~16**: Obtener planes
```sql
SELECT * FROM planes ORDER BY created_at DESC
```
**Dificultad**: Fácil - SELECT con ORDER BY

**Línea ~42-53**: Insertar plan
```sql
INSERT INTO planes (nombre, descripcion, precio, duracion_dias, activo, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~63**: Obtener plan
```sql
SELECT * FROM planes WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~85**: Obtener plan
```sql
SELECT * FROM planes WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~90-103**: Actualizar plan
```sql
UPDATE planes SET 
    nombre = ?, descripcion = ?, precio = ?, duracion_dias = ?, 
    activo = ?, updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~113**: Obtener plan
```sql
SELECT * FROM planes WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~119-123**: Contar usuarios con plan
```sql
SELECT COUNT(*) as total 
FROM plan_usuario 
WHERE plan_id = ? AND activo = 1
```
**Dificultad**: Fácil - COUNT con WHERE

**Línea ~129**: Eliminar plan
```sql
DELETE FROM planes WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

### CategoriaController.php

**Línea ~26-36**: Insertar categoría
```sql
INSERT INTO categorias (nombre, descripcion, color, activo, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~61**: Obtener categorías
```sql
SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre
```
**Dificultad**: Fácil - SELECT con ORDER BY

**Línea ~77-87**: Actualizar categoría
```sql
UPDATE categorias SET 
    nombre = ?, descripcion = ?, color = ?, updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~101-105**: Contar ejercicios categoría
```sql
SELECT COUNT(*) as total 
FROM ejercicios 
WHERE categoria_id = ?
```
**Dificultad**: Fácil - COUNT con WHERE

**Línea ~114**: Eliminar categoría
```sql
DELETE FROM categorias WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

### TipoRutinaController.php

**Línea ~26-30**: Verificar tipo existe
```sql
SELECT COUNT(*) as total 
FROM tipo_rutinas 
WHERE nombre = ?
```
**Dificultad**: Fácil - COUNT simple

**Línea ~40-50**: Insertar tipo rutina
```sql
INSERT INTO tipo_rutinas (nombre, descripcion, color, activo, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~78**: Obtener tipos
```sql
SELECT * FROM tipo_rutinas WHERE activo = 1 ORDER BY nombre
```
**Dificultad**: Fácil - SELECT con ORDER BY

**Línea ~90**: Obtener tipo
```sql
SELECT * FROM tipo_rutinas WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~106-110**: Verificar nombre existe
```sql
SELECT COUNT(*) as total 
FROM tipo_rutinas 
WHERE nombre = ? AND id != ?
```
**Dificultad**: Fácil - COUNT con múltiples WHERE

**Línea ~120-130**: Actualizar tipo
```sql
UPDATE tipo_rutinas SET 
    nombre = ?, descripcion = ?, color = ?, updated_at = ?
WHERE id = ?
```
**Dificultad**: Fácil - UPDATE simple

**Línea ~156**: Obtener tipo
```sql
SELECT * FROM tipo_rutinas WHERE id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~166-170**: Contar rutinas tipo
```sql
SELECT COUNT(*) as total 
FROM rutinas 
WHERE tipo_rutina_id = ?
```
**Dificultad**: Fácil - COUNT con WHERE

**Línea ~180**: Eliminar tipo
```sql
DELETE FROM tipo_rutinas WHERE id = ?
```
**Dificultad**: Fácil - DELETE simple

### RegisterController.php

**Línea ~46**: Obtener rol deportista
```sql
SELECT * FROM roles WHERE nombre = 'Deportista' LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~55-64**: Insertar usuario
```sql
INSERT INTO users (name, email, password, rol_id, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

**Línea ~67-71**: Insertar deportista
```sql
INSERT INTO deportistas (user_id, created_at, updated_at)
VALUES (?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple

---

## 🟡 CONSULTAS MEDIAS

Consultas con JOINs, subconsultas simples, GROUP BY, o agregaciones más complejas.

### DashboardController.php

**Línea ~84-92**: Calcular peso levantado
```sql
SELECT COALESCE(SUM(COALESCE(dr.peso, 0) * COALESCE(dr.series, 1) * COALESCE(dr.repeticiones, 1)), 0) as total
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
WHERE p.deportista_id = ?
AND p.fecha_registro BETWEEN ? AND ?
AND p.porcentaje_completado >= 100
```
**Dificultad**: Media - SUM con múltiples JOINs y cálculos

**Línea ~96-109**: Obtener progreso reciente
```sql
SELECT 
    e.nombre as ejercicio,
    p.fecha_registro,
    dr.peso,
    dr.repeticiones
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
WHERE p.deportista_id = ?
AND p.porcentaje_completado >= 100
ORDER BY p.fecha_registro DESC
LIMIT 5
```
**Dificultad**: Media - SELECT con múltiples JOINs

**Línea ~120-128**: Obtener próximos entrenamientos
```sql
SELECT 
    r.nombre,
    r.id as rutina_id,
    rup.fecha_ultima_sesion,
    rup.estado
FROM rutina_usuario_progreso rup
INNER JOIN rutinas r ON rup.rutina_id = r.id
WHERE rup.user_id = ?
AND rup.estado IN ('en_progreso', 'pendiente')
ORDER BY 
    CASE WHEN rup.estado = 'en_progreso' THEN 0 ELSE 1 END,
    rup.fecha_ultima_sesion DESC
LIMIT 5
```
**Dificultad**: Media - SELECT con JOIN y ORDER BY con CASE

### ProfileController.php

**Línea ~220-228**: Calcular racha
```sql
SELECT DISTINCT fecha_sesion 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
ORDER BY fecha_sesion DESC
```
**Dificultad**: Media - SELECT DISTINCT con ORDER BY

**Línea ~251-267**: Calcular percentil logros
```sql
SELECT COUNT(*) as total 
FROM (
    SELECT deportista_id, COUNT(*) as total_logros
    FROM gamificaciones
    GROUP BY deportista_id
    HAVING total_logros < ?
) as subquery
```
**Dificultad**: Media - Subconsulta con GROUP BY y HAVING

### RutinaController.php

**Línea ~17-28**: Obtener plan activo
```sql
SELECT
    p.*,
    pu.fecha_inicio,
    pu.fecha_fin,
    pu.activo
FROM plan_usuario pu
INNER JOIN planes p ON pu.plan_id = p.id
WHERE pu.user_id = ?
AND pu.activo = 1
AND pu.fecha_fin >= ?
```
**Dificultad**: Media - SELECT con JOIN

**Línea ~39-44**: Calcular tiempo total
```sql
SELECT COALESCE(SUM(COALESCE(e.duracion_minutos, 0)), 0) as total
FROM detalle_rutinas dr
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
WHERE dr.rutina_id = ?
```
**Dificultad**: Media - SUM con JOIN

**Línea ~55-60**: Calcular calorías totales
```sql
SELECT COALESCE(SUM(COALESCE(e.calorias_estimadas, 0)), 0) as total
FROM detalle_rutinas dr
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
WHERE dr.rutina_id = ?
```
**Dificultad**: Media - SUM con JOIN

**Línea ~105**: Obtener rutinas de plan
```sql
SELECT rutina_id FROM rutina_plan WHERE plan_id = ?
```
**Dificultad**: Fácil - SELECT simple (pero usado en contexto complejo)

**Línea ~145-150**: Contar rutinas con filtros
```sql
SELECT COUNT(*) as total
FROM rutinas r
LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
WHERE {$whereClause}
```
**Dificultad**: Media - COUNT con LEFT JOIN y WHERE dinámico

**Línea ~153-163**: Obtener rutinas con filtros
```sql
SELECT
    r.*,
    tr.nombre as tipo_nombre,
    tr.color as tipo_color
FROM rutinas r
LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
WHERE {$whereClause}
ORDER BY r.nombre
LIMIT ? OFFSET ?
```
**Dificultad**: Media - SELECT con LEFT JOIN y WHERE dinámico

**Línea ~171-188**: Obtener ejercicios de rutinas
```sql
SELECT
    dr.rutina_id,
    e.id as ejercicio_id,
    e.nombre as ejercicio_nombre,
    c.nombre as categoria_nombre,
    dr.orden,
    dr.series,
    dr.repeticiones,
    dr.peso,
    dr.descanso_segundos,
    dr.notas
FROM detalle_rutinas dr
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE dr.rutina_id IN ({$placeholders})
ORDER BY dr.orden
```
**Dificultad**: Media - SELECT con múltiples JOINs e IN dinámico

**Línea ~197-200**: Obtener progresos
```sql
SELECT * FROM rutina_usuario_progreso
WHERE user_id = ? AND rutina_id IN ({$placeholders})
```
**Dificultad**: Media - SELECT con IN dinámico

**Línea ~252-257**: Obtener tipos rutinas
```sql
SELECT * FROM tipo_rutinas
WHERE activo = 1 AND id IN ({$placeholders})
ORDER BY nombre
```
**Dificultad**: Media - SELECT con IN dinámico

**Línea ~370-379**: Obtener rutina con tipo
```sql
SELECT
    r.*,
    tr.nombre as tipo_nombre,
    tr.color as tipo_color,
    tr.descripcion as tipo_descripcion
FROM rutinas r
LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
WHERE r.id = ?
```
**Dificultad**: Media - SELECT con LEFT JOIN

**Línea ~386-401**: Obtener ejercicios de rutina
```sql
SELECT
    e.*,
    c.nombre as categoria_nombre,
    dr.orden,
    dr.series,
    dr.repeticiones,
    dr.peso,
    dr.descanso_segundos,
    dr.notas
FROM detalle_rutinas dr
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE dr.rutina_id = ?
ORDER BY dr.orden
```
**Dificultad**: Media - SELECT con múltiples JOINs

**Línea ~441-444**: Obtener progreso
```sql
SELECT * FROM rutina_usuario_progreso
WHERE user_id = ? AND rutina_id = ?
```
**Dificultad**: Fácil - SELECT con WHERE (pero en contexto de rutina completa)

**Línea ~465**: Obtener planes asignados
```sql
SELECT plan_id FROM rutina_plan WHERE rutina_id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~468**: Obtener ejercicios asignados
```sql
SELECT * FROM detalle_rutinas WHERE rutina_id = ?
```
**Dificultad**: Fácil - SELECT simple

**Línea ~694-702**: Obtener rutina con tipo
```sql
SELECT
    r.*,
    tr.nombre as tipo_nombre,
    tr.color as tipo_color
FROM rutinas r
LEFT JOIN tipo_rutinas tr ON r.tipo_rutina_id = tr.id
WHERE r.id = ?
```
**Dificultad**: Media - SELECT con LEFT JOIN

**Línea ~709-725**: Obtener ejercicios con progreso
```sql
SELECT
    dr.id as detalle_id,
    e.*,
    c.nombre as categoria_nombre,
    dr.orden,
    dr.series,
    dr.repeticiones,
    dr.peso,
    dr.descanso_segundos,
    dr.notas
FROM detalle_rutinas dr
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE dr.rutina_id = ?
ORDER BY dr.orden
```
**Dificultad**: Media - SELECT con múltiples JOINs

**Línea ~732-739**: Obtener progresos de ejercicios
```sql
SELECT p.detalle_rutina_id
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
WHERE p.deportista_id = ?
AND dr.rutina_id = ?
AND p.fecha_registro = ?
```
**Dificultad**: Media - SELECT con JOIN

**Línea ~814-818**: Verificar ejercicios pertenecen a rutina
```sql
SELECT id FROM detalle_rutinas
WHERE id IN ({$placeholders}) AND rutina_id = ?
```
**Dificultad**: Media - SELECT con IN dinámico

### EjercicioController.php

**Línea ~86-91**: Contar ejercicios con filtros
```sql
SELECT COUNT(*) as total 
FROM ejercicios e
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE {$whereClause}
```
**Dificultad**: Media - COUNT con LEFT JOIN y WHERE dinámico

**Línea ~94-104**: Obtener ejercicios con filtros
```sql
SELECT 
    e.*,
    c.nombre as categoria_nombre,
    c.color as categoria_color
FROM ejercicios e
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE {$whereClause}
ORDER BY e.nombre
LIMIT ? OFFSET ?
```
**Dificultad**: Media - SELECT con LEFT JOIN y WHERE dinámico

**Línea ~137-142**: Obtener categorías filtradas
```sql
SELECT * FROM categorias 
WHERE activo = 1 AND id IN ({$placeholders})
ORDER BY nombre
```
**Dificultad**: Media - SELECT con IN dinámico

### SesionController.php

**Línea ~50-55**: Contar sesiones con filtros
```sql
SELECT COUNT(*) as total 
FROM sesiones s
LEFT JOIN rutinas r ON s.rutina_id = r.id
WHERE {$whereClause}
```
**Dificultad**: Media - COUNT con LEFT JOIN y WHERE dinámico

**Línea ~58-68**: Obtener sesiones con filtros
```sql
SELECT 
    s.*,
    r.nombre as rutina_nombre,
    r.descripcion as rutina_descripcion
FROM sesiones s
LEFT JOIN rutinas r ON s.rutina_id = r.id
WHERE {$whereClause}
ORDER BY s.fecha_sesion DESC, s.hora_inicio DESC
LIMIT ? OFFSET ?
```
**Dificultad**: Media - SELECT con LEFT JOIN y WHERE dinámico

**Línea ~72-76**: Contar ejercicios sesión
```sql
SELECT COUNT(*) as total 
FROM sesion_ejercicios 
WHERE sesion_id = ?
```
**Dificultad**: Fácil - COUNT simple (pero en contexto de transformación)

**Línea ~91-96**: Obtener rutinas usuario
```sql
SELECT DISTINCT r.id, r.nombre
FROM rutina_usuario_progreso rup
INNER JOIN rutinas r ON rup.rutina_id = r.id
WHERE rup.user_id = ?
```
**Dificultad**: Media - SELECT DISTINCT con JOIN

**Línea ~100-104**: Estadísticas sesiones
```sql
SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'
SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada' AND YEAR(fecha_sesion) = ? AND MONTH(fecha_sesion) = ?
SELECT COALESCE(SUM(calorias_quemadas), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'
SELECT COALESCE(SUM(duracion_minutos), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'
```
**Dificultad**: Media - Múltiples agregaciones con funciones de fecha

**Línea ~118-128**: Obtener sesión con rutina
```sql
SELECT 
    s.*,
    r.nombre as rutina_nombre,
    r.descripcion as rutina_descripcion,
    r.tiempo_estimado_minutos,
    r.calorias_estimadas
FROM sesiones s
LEFT JOIN rutinas r ON s.rutina_id = r.id
WHERE s.id = ? AND s.user_id = ?
```
**Dificultad**: Media - SELECT con LEFT JOIN

**Línea ~135-146**: Obtener ejercicios sesión
```sql
SELECT 
    se.*,
    e.nombre as ejercicio_nombre,
    e.descripcion as ejercicio_descripcion,
    e.imagen_url,
    c.nombre as categoria_nombre
FROM sesion_ejercicios se
INNER JOIN ejercicios e ON se.ejercicio_id = e.id
LEFT JOIN categorias c ON e.categoria_id = c.id
WHERE se.sesion_id = ?
```
**Dificultad**: Media - SELECT con múltiples JOINs

**Línea ~160-165**: Obtener rutinas usuario
```sql
SELECT DISTINCT r.id, r.nombre, r.descripcion
FROM rutina_usuario_progreso rup
INNER JOIN rutinas r ON rup.rutina_id = r.id
WHERE rup.user_id = ? AND r.activo = 1
```
**Dificultad**: Media - SELECT DISTINCT con JOIN

**Línea ~201-221**: Insertar sesión
```sql
INSERT INTO sesiones (...) VALUES (?, ?, ...)
```
**Dificultad**: Fácil - INSERT simple (pero con cálculos previos)

**Línea ~244-249**: Obtener rutinas usuario
```sql
SELECT DISTINCT r.id, r.nombre, r.descripcion
FROM rutina_usuario_progreso rup
INNER JOIN rutinas r ON rup.rutina_id = r.id
WHERE rup.user_id = ? AND r.activo = 1
```
**Dificultad**: Media - SELECT DISTINCT con JOIN

### ContenidoController.php

**Línea ~55-60**: Contar contenido con filtros
```sql
SELECT COUNT(*) as total 
FROM contenido c
LEFT JOIN users u ON c.autor_id = u.id
{$whereClause}
```
**Dificultad**: Media - COUNT con LEFT JOIN y WHERE dinámico

**Línea ~63-74**: Obtener contenido con filtros
```sql
SELECT 
    c.*,
    u.name as autor_nombre,
    u.primer_nombre,
    u.primer_apellido
FROM contenido c
LEFT JOIN users u ON c.autor_id = u.id
{$whereClause}
ORDER BY c.created_at DESC
LIMIT ? OFFSET ?
```
**Dificultad**: Media - SELECT con LEFT JOIN y WHERE dinámico

### ClienteController.php

**Línea ~60-65**: Contar usuarios con filtros
```sql
SELECT COUNT(*) as total 
FROM users u
LEFT JOIN roles r ON u.rol_id = r.id
{$whereClause}
```
**Dificultad**: Media - COUNT con LEFT JOIN y WHERE dinámico

**Línea ~72-82**: Obtener usuarios con filtros
```sql
SELECT 
    u.*,
    r.nombre as rol_nombre,
    r.id as rol_id
FROM users u
LEFT JOIN roles r ON u.rol_id = r.id
{$whereClause}
ORDER BY u.created_at DESC
LIMIT ? OFFSET ?
```
**Dificultad**: Media - SELECT con LEFT JOIN y WHERE dinámico

**Línea ~86-97**: Obtener plan activo usuario
```sql
SELECT 
    p.*,
    pu.fecha_inicio,
    pu.fecha_fin,
    pu.activo as pivot_activo
FROM plan_usuario pu
INNER JOIN planes p ON pu.plan_id = p.id
WHERE pu.user_id = ?
AND pu.activo = 1
AND pu.fecha_fin >= ?
```
**Dificultad**: Media - SELECT con JOIN y múltiples condiciones

### ProgresoController.php

**Línea ~21-23**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~69-83**: Obtener progreso rutinas
```sql
SELECT 
    r.id,
    r.nombre,
    rup.porcentaje_completado,
    rup.estado,
    rup.sesiones_completadas,
    rup.fecha_inicio,
    rup.fecha_ultima_sesion
FROM rutina_usuario_progreso rup
INNER JOIN rutinas r ON rup.rutina_id = r.id
WHERE rup.user_id = ?
ORDER BY rup.fecha_ultima_sesion DESC
LIMIT 10
```
**Dificultad**: Media - SELECT con JOIN

**Línea ~86-99**: Obtener progreso ejercicios
```sql
SELECT 
    e.nombre as ejercicio_nombre,
    p.fecha_registro,
    COUNT(*) as veces_completado
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
WHERE p.deportista_id = ?
AND p.fecha_registro >= ?
AND p.porcentaje_completado >= 100
GROUP BY e.id, e.nombre, p.fecha_registro
ORDER BY p.fecha_registro DESC
```
**Dificultad**: Media - SELECT con múltiples JOINs y GROUP BY

**Línea ~110-116**: Contar sesiones por mes
```sql
SELECT COUNT(*) as total 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
AND YEAR(fecha_sesion) = ? 
AND MONTH(fecha_sesion) = ?
```
**Dificultad**: Media - COUNT con funciones de fecha (ejecutado en loop)

**Línea ~120-126**: Obtener evolución peso
```sql
SELECT peso, updated_at 
FROM deportistas 
WHERE user_id = ? 
AND peso IS NOT NULL 
ORDER BY updated_at DESC 
LIMIT 10
```
**Dificultad**: Media - SELECT con condición NULL

**Línea ~147-149**: Obtener deportista
```sql
SELECT * FROM deportistas WHERE user_id = ? LIMIT 1
```
**Dificultad**: Fácil - SELECT simple

**Línea ~164-167**: Obtener logros
```sql
SELECT * 
FROM gamificaciones 
WHERE deportista_id = ? 
ORDER BY fecha_asignacion DESC
```
**Dificultad**: Fácil - SELECT con ORDER BY (pero usado con transformaciones)

**Línea ~258-262**: Obtener primera sesión
```sql
SELECT * 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
ORDER BY fecha_sesion ASC 
LIMIT 1
```
**Dificultad**: Media - SELECT con ORDER BY y LIMIT

---

## 🔴 CONSULTAS DIFÍCILES

Consultas con subconsultas complejas, múltiples JOINs anidados, CTEs, funciones de ventana, o lógica de negocio compleja.

### RutinaController.php

**Línea ~47**: Obtener detalles para cálculo
```sql
SELECT series, descanso_segundos FROM detalle_rutinas WHERE rutina_id = ?
```
**Dificultad**: Fácil - SELECT simple (pero usado en cálculo complejo con lógica PHP)

**Nota**: El cálculo de tiempo de descanso se hace en PHP, no en SQL, pero requiere procesamiento de múltiples registros.

### ProgresoController.php

**Línea ~218-228**: Calcular racha (lógica compleja)
```sql
SELECT DISTINCT fecha_sesion 
FROM sesiones 
WHERE user_id = ? 
AND estado = 'completada' 
ORDER BY fecha_sesion DESC
```
**Dificultad**: Media - SELECT DISTINCT (pero la lógica de cálculo de racha se hace en PHP con loops complejos)

**Línea ~253-348**: Verificar y asignar logros (múltiples consultas condicionales)
```sql
-- Múltiples consultas condicionales ejecutadas en secuencia
SELECT * FROM sesiones WHERE user_id = ? AND estado = 'completada' ORDER BY fecha_sesion ASC LIMIT 1
SELECT COUNT(*) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'
SELECT COUNT(*) as total FROM rutina_usuario_progreso WHERE user_id = ? AND estado = 'completada'
SELECT COALESCE(SUM(calorias_quemadas), 0) as total FROM sesiones WHERE user_id = ? AND estado = 'completada'
SELECT COUNT(*) as total FROM gamificaciones WHERE deportista_id = ? AND logro = ?
INSERT INTO gamificaciones (...) VALUES (?, ?, ...)
```
**Dificultad**: Difícil - Sistema complejo de múltiples consultas condicionales con lógica de negocio

**Línea ~353-369**: Asignar logro
```sql
SELECT COUNT(*) as total 
FROM gamificaciones 
WHERE deportista_id = ? 
AND logro = ?
```
**Dificultad**: Fácil - COUNT simple (pero parte de sistema complejo)

**Línea ~361-368**: Insertar logro
```sql
INSERT INTO gamificaciones (deportista_id, fecha_asignacion, logro, puntos, created_at, updated_at) 
VALUES (?, ?, ?, ?, ?, ?)
```
**Dificultad**: Fácil - INSERT simple (pero parte de sistema complejo)

### DashboardController.php

**Línea ~84-92**: Calcular peso levantado con cálculos complejos
```sql
SELECT COALESCE(SUM(COALESCE(dr.peso, 0) * COALESCE(dr.series, 1) * COALESCE(dr.repeticiones, 1)), 0) as total
FROM progresos p
INNER JOIN detalle_rutinas dr ON p.detalle_rutina_id = dr.id
INNER JOIN ejercicios e ON dr.ejercicio_id = e.id
WHERE p.deportista_id = ?
AND p.fecha_registro BETWEEN ? AND ?
AND p.porcentaje_completado >= 100
```
**Dificultad**: Media-Difícil - SUM con cálculos matemáticos complejos y múltiples JOINs

### ProfileController.php

**Línea ~249-267**: Calcular percentil logros con subconsulta
```sql
SELECT COUNT(*) as total 
FROM (
    SELECT deportista_id, COUNT(*) as total_logros
    FROM gamificaciones
    GROUP BY deportista_id
    HAVING total_logros < ?
) as subquery
```
**Dificultad**: Media-Difícil - Subconsulta con GROUP BY y HAVING para cálculo estadístico

---

## 📊 Resumen por Controlador

### DashboardController.php
- **Fáciles**: 8 consultas
- **Medias**: 3 consultas
- **Difíciles**: 0 consultas (1 Media-Difícil)

### ProfileController.php
- **Fáciles**: 5 consultas
- **Medias**: 1 consulta
- **Difíciles**: 0 consultas (1 Media-Difícil)

### RutinaController.php
- **Fáciles**: 25 consultas
- **Medias**: 12 consultas
- **Difíciles**: 0 consultas (1 con lógica compleja en PHP)

### EjercicioController.php
- **Fáciles**: 15 consultas
- **Medias**: 4 consultas
- **Difíciles**: 0 consultas

### SesionController.php
- **Fáciles**: 10 consultas
- **Medias**: 8 consultas
- **Difíciles**: 0 consultas

### ContenidoController.php
- **Fáciles**: 12 consultas
- **Medias**: 2 consultas
- **Difíciles**: 0 consultas

### ClienteController.php
- **Fáciles**: 20 consultas
- **Medias**: 3 consultas
- **Difíciles**: 0 consultas

### ProgresoController.php
- **Fáciles**: 3 consultas
- **Medias**: 6 consultas
- **Difíciles**: 1 sistema complejo (múltiples consultas condicionales)

### PlanController.php
- **Fáciles**: 8 consultas
- **Medias**: 0 consultas
- **Difíciles**: 0 consultas

### CategoriaController.php
- **Fáciles**: 5 consultas
- **Medias**: 0 consultas
- **Difíciles**: 0 consultas

### TipoRutinaController.php
- **Fáciles**: 9 consultas
- **Medias**: 0 consultas
- **Difíciles**: 0 consultas

### RegisterController.php
- **Fáciles**: 3 consultas
- **Medias**: 0 consultas
- **Difíciles**: 0 consultas

---

## 📈 Estadísticas Generales

- **Total Consultas Fáciles**: ~123 consultas
- **Total Consultas Medias**: ~39 consultas
- **Total Consultas Difíciles**: ~1 sistema complejo + 2 Media-Difícil

---

## 💡 Notas Importantes

1. **Consultas Dinámicas**: Muchas consultas usan `{$whereClause}` construido dinámicamente en PHP, lo que añade complejidad aunque la consulta base sea simple.

2. **Lógica de Negocio**: Algunas consultas "fáciles" forman parte de sistemas complejos donde la lógica de negocio se maneja en PHP (ej: cálculo de racha, asignación de logros).

3. **JOINs Múltiples**: Las consultas con 2+ JOINs se consideran "Medias" aunque sean conceptualmente simples.

4. **Agregaciones Complejas**: Las consultas con SUM/COUNT que incluyen cálculos matemáticos o múltiples condiciones se consideran "Medias" o "Medias-Difíciles".

---

## 🎯 Recomendaciones de Estudio

### Para Principiantes
- Enfócate en las consultas **Fáciles** de `PlanController`, `CategoriaController`, `TipoRutinaController`
- Practica con SELECT, INSERT, UPDATE, DELETE simples

### Para Nivel Intermedio
- Estudia las consultas **Medias** de `RutinaController`, `EjercicioController`, `SesionController`
- Practica con JOINs, GROUP BY, y agregaciones

### Para Nivel Avanzado
- Analiza el sistema de **Logros** en `ProgresoController`
- Estudia las consultas con cálculos complejos y subconsultas
- Revisa la lógica de negocio que combina múltiples consultas

---

**Última actualización**: Diciembre 2024
**Versión del Proyecto**: Laravel 12.37.0

