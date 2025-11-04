# Migraciones SQL

Esta carpeta contiene las migraciones SQL para la base de datos `bases1`.

## Uso

Para ejecutar las migraciones SQL, usa el siguiente comando:

```bash
php artisan migrate:sql
```

Para ejecutar todas las migraciones desde cero (eliminar todas las tablas y re-ejecutar):

```bash
php artisan migrate:sql --fresh
```

## Formato de las migraciones

- Las migraciones deben tener extensión `.sql`
- Se ejecutan en orden alfabético según el nombre del archivo
- Usa prefijos numéricos para controlar el orden (ej: `001_`, `002_`, etc.)
- Las migraciones ya ejecutadas se omiten automáticamente

## Ejemplo de migración

```sql
-- Crear tabla
CREATE TABLE IF NOT EXISTS mi_tabla (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos
INSERT INTO mi_tabla (nombre, created_at, updated_at) VALUES
('Ejemplo 1', NOW(), NOW()),
('Ejemplo 2', NOW(), NOW());
```

## Notas

- Las migraciones se registran en la tabla `sql_migrations` para evitar ejecuciones duplicadas
- Usa transacciones cuando sea necesario para mantener la integridad de los datos
- Siempre incluye comentarios descriptivos en tus migraciones SQL

