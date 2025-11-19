# FitTracker - Sistema de Gestión de Entrenamientos

## 📋 Descripción del Proyecto

**FitTracker** es una aplicación web desarrollada como proyecto final de la asignatura **Bases de Datos 1**. Es un sistema integral de gestión de entrenamientos que permite a administradores, coaches y deportistas gestionar ejercicios, rutinas, planes de suscripción y seguimiento de progreso.

### Características Principales

- **Gestión de Usuarios**: Sistema de roles (Administrador, Coach, Deportista) con control de acceso basado en roles
- **Gestión de Ejercicios**: Catálogo completo de ejercicios con categorías, dificultad, y asignación a planes
- **Gestión de Rutinas**: Creación y edición de rutinas de entrenamiento con ejercicios organizados
- **Gestión de Planes**: Sistema de suscripciones con precios en pesos colombianos (COP)
- **Seguimiento de Progreso**: Sistema de seguimiento del progreso de los deportistas en sus rutinas
- **Dashboard**: Panel de control con estadísticas y métricas de entrenamiento

---

## 👥 Autores

Este proyecto fue desarrollado por:

- **Luis Osorio**
- **Luis Torres**
- **Brian Zuleta**

**Asignatura**: Bases de Datos 1  
**Proyecto**: Final

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.2+**
- **Laravel 12.37.0**
- **MySQL/MariaDB** (Base de datos)

### Frontend
- **Tailwind CSS** (via CDN)
- **JavaScript (Vanilla)**
- **Blade Templates** (Motor de plantillas de Laravel)

### Base de Datos
- **MySQL/MariaDB** como motor de base de datos
- **SQL directo** para migraciones y consultas
- **Sistema de migraciones SQL personalizado**

---

## 📁 Estructura del Proyecto

```
bases1/
├── app/
│   ├── Console/Commands/
│   │   └── RunSqlMigrations.php      # Comando personalizado para migraciones SQL
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                 # Controladores de autenticación
│   │   │   ├── CategoriaController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── EjercicioController.php
│   │   │   ├── PlanController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── RutinaController.php
│   │   │   └── TipoRutinaController.php
│   │   └── Middleware/
│   │       └── CheckRole.php         # Middleware para control de roles
│   └── Models/                        # Modelos Eloquent (simplificados)
│
├── database/
│   └── sql_migrations/               # Migraciones SQL personalizadas
│       ├── 001_create_users_table.sql
│       ├── 002_create_password_reset_tokens_table.sql
│       ├── 003_create_sessions_table.sql
│       ├── 004_create_cache_tables.sql
│       ├── 005_create_jobs_tables.sql
│       ├── 006_create_migrations_table.sql
│       ├── 007_add_profile_fields_to_users.sql
│       ├── 008_add_rol_to_users.sql
│       ├── 009_create_planes_table.sql
│       ├── 010_create_plan_usuario_table.sql
│       ├── 011_create_categorias_table.sql
│       ├── 012_create_ejercicios_table.sql
│       ├── 013_create_plan_ejercicio_table.sql
│       ├── 014_insert_categorias_grupos_musculares.sql
│       ├── 015_create_tipo_rutinas_table.sql
│       ├── 016_create_rutinas_table.sql
│       ├── 017_create_rutina_ejercicio_table.sql
│       ├── 018_create_rutina_plan_table.sql
│       ├── 019_create_rutina_usuario_progreso_table.sql
│       └── 020_insert_tipo_rutinas.sql
│
├── resources/
│   └── views/
│       ├── auth/                      # Vistas de autenticación
│       ├── clientes/                   # CRUD de clientes
│       ├── ejercicios/                 # CRUD de ejercicios
│       ├── planes/                     # CRUD de planes
│       ├── rutinas/                    # CRUD de rutinas
│       ├── profile/                    # Perfil de usuario
│       ├── layouts/
│       │   └── app.blade.php          # Layout principal
│       ├── dashboard.blade.php
│       └── profile.blade.php
│
├── routes/
│   └── web.php                        # Rutas de la aplicación
│
├── config/
│   └── database.php                   # Configuración de base de datos
│
└── .env                               # Variables de entorno (crear manualmente)
```

### Estructura de Base de Datos

**Tablas Principales:**
- `users` - Usuarios del sistema (Administradores, Coaches, Deportistas)
- `planes` - Planes de suscripción
- `plan_usuario` - Relación muchos-a-muchos entre usuarios y planes
- `categorias` - Categorías de ejercicios
- `ejercicios` - Catálogo de ejercicios
- `plan_ejercicio` - Relación entre planes y ejercicios
- `tipo_rutinas` - Tipos de rutinas (Fuerza, Cardio, Body, etc.)
- `rutinas` - Rutinas de entrenamiento
- `rutina_ejercicio` - Relación entre rutinas y ejercicios con detalles
- `rutina_plan` - Relación entre rutinas y planes
- `rutina_usuario_progreso` - Seguimiento del progreso de usuarios

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- **PHP 8.2** o superior
- **Composer** (Gestor de dependencias de PHP)
- **MySQL/MariaDB** 5.7+ o superior
- **XAMPP** (o servidor web similar con Apache/Nginx)
- **Git** (para clonar el repositorio)

### Pasos para Ejecutar el Proyecto

#### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd bases1
```

#### 2. Instalar Dependencias de PHP

```bash
composer install
```

#### 3. Configurar Variables de Entorno

Copia el archivo `.env.example` a `.env` (si existe) o crea un nuevo archivo `.env`:

```bash
cp .env.example .env
```

O crea manualmente el archivo `.env` con la siguiente configuración:

```env
APP_NAME="FitTracker"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bases1
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

#### 4. Generar la Clave de la Aplicación

```bash
php artisan key:generate
```

#### 5. Crear la Base de Datos

Crea una base de datos MySQL llamada `bases1` (o el nombre que prefieras):

```sql
CREATE DATABASE bases1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Actualiza el archivo `.env` con el nombre de tu base de datos si es diferente.

#### 6. Ejecutar las Migraciones SQL

⚠️ **IMPORTANTE**: Este proyecto utiliza un sistema de migraciones SQL personalizado ubicado en `database/sql_migrations/`. **NO uses** `php artisan migrate` estándar de Laravel.

Para ejecutar las migraciones SQL, usa el siguiente comando:

```bash
php artisan migrate:sql
```

Este comando:
- ✅ Ejecuta todas las migraciones SQL en orden numérico (001, 002, 003, etc.)
- ✅ Crea todas las tablas necesarias
- ✅ Inserta datos iniciales (categorías y tipos de rutinas)
- ✅ Rastrea las migraciones ejecutadas en la tabla `sql_migrations`
- ✅ Omite automáticamente las migraciones ya ejecutadas

**Para ejecutar las migraciones desde cero** (elimina todas las tablas y las recrea):

```bash
php artisan migrate:sql --fresh
```

⚠️ **Advertencia**: El flag `--fresh` eliminará todas las tablas existentes antes de ejecutar las migraciones. Úsalo solo si necesitas reiniciar completamente la base de datos.

#### 7. Iniciar el Servidor de Desarrollo

```bash
php artisan serve
```

El servidor estará disponible en: `http://127.0.0.1:8000`

#### 8. Acceder a la Aplicación

Abre tu navegador y visita: `http://127.0.0.1:8000`

---

## 👤 Usuarios por Defecto

Después de ejecutar las migraciones, deberás crear un usuario administrador manualmente. Para hacerlo:

1. Ve a la página de registro: `http://127.0.0.1:8000/register`
2. Completa el formulario de registro
3. Los nuevos usuarios se crean con el rol **"Deportista"** por defecto
4. Para cambiar el rol a Administrador, ejecuta en MySQL:

```sql
UPDATE users SET rol = 'Administrador' WHERE email = 'tu-email@ejemplo.com';
```

---

## 🔐 Sistema de Roles

El sistema cuenta con tres roles principales:

- **Administrador**: Acceso completo al sistema, puede gestionar usuarios, planes, ejercicios y rutinas
- **Coach**: Acceso limitado para ver y gestionar entrenamientos
- **Deportista**: Acceso básico para ver ejercicios y rutinas asignadas a su plan

---

## 📝 Funcionalidades Principales

### Para Administradores

- **Gestión de Clientes**: CRUD completo de usuarios
- **Gestión de Planes**: Crear, editar y asignar planes de suscripción
- **Gestión de Ejercicios**: CRUD completo con categorías y asignación a planes
- **Gestión de Rutinas**: Crear rutinas con ejercicios, calcular tiempo y calorías automáticamente
- **Gestión de Tipos de Rutinas**: Crear nuevos tipos de rutinas (Fuerza, Cardio, Body, etc.)

### Para Coaches y Deportistas

- **Visualización de Ejercicios**: Ver ejercicios asignados a su plan
- **Visualización de Rutinas**: Ver rutinas disponibles según su plan
- **Seguimiento de Progreso**: Ver porcentaje de completado en rutinas
- **Perfil Personal**: Ver y editar información personal

---

## 🗄️ Sistema de Migraciones SQL

Este proyecto utiliza un **sistema de migraciones SQL personalizado** en lugar de las migraciones estándar de Laravel. Las migraciones se encuentran en `database/sql_migrations/` y se ejecutan con:

```bash
php artisan migrate:sql
```

### Características del Sistema de Migraciones SQL

- ✅ **Ejecución ordenada**: Las migraciones se ejecutan en orden numérico (001, 002, 003, etc.)
- ✅ **Seguimiento automático**: Rastrea las migraciones ejecutadas en la tabla `sql_migrations`
- ✅ **Prevención de duplicados**: Omite automáticamente las migraciones ya ejecutadas
- ✅ **Reinicio completo**: Soporta el flag `--fresh` para eliminar todas las tablas y reiniciar desde cero

### Ubicación de las Migraciones

Todas las migraciones SQL están en: `database/sql_migrations/`

### Comandos Disponibles

```bash
# Ejecutar todas las migraciones SQL pendientes
php artisan migrate:sql

# Ejecutar todas las migraciones desde cero (elimina todas las tablas)
php artisan migrate:sql --fresh
```

⚠️ **Nota importante**: No uses `php artisan migrate` estándar de Laravel, ya que este proyecto no utiliza las migraciones de Laravel.

---

## 🔧 Comandos Útiles

### Migraciones SQL

```bash
# Ejecutar migraciones SQL (comando principal para este proyecto)
php artisan migrate:sql

# Ejecutar migraciones desde cero (elimina todas las tablas)
php artisan migrate:sql --fresh
```

### Limpieza de Caché

```bash
# Limpiar caché de configuración
php artisan config:clear

# Limpiar caché de rutas
php artisan route:clear

# Limpiar caché de vistas
php artisan view:clear

# Limpiar todo el caché
php artisan cache:clear
```

### Otros Comandos

```bash
# Ver todas las rutas
php artisan route:list

# Generar clave de aplicación
php artisan key:generate

# Iniciar servidor de desarrollo
php artisan serve
```

---

## 📚 Notas Técnicas

- El proyecto utiliza **SQL directo** en lugar de Eloquent ORM para la mayoría de las operaciones
- Las consultas se realizan mediante `DB::table()` para mayor control sobre las queries
- Se implementó un sistema de paginación manual para mantener consistencia con SQL directo
- El cálculo de tiempo y calorías en rutinas se realiza automáticamente basado en los ejercicios asignados

---

## 📄 Licencia

Este proyecto es parte de un proyecto académico para la asignatura Bases de Datos 1.

---

## 👨‍💻 Soporte

Para cualquier consulta o problema relacionado con el proyecto, contactar a los desarrolladores.

---

**Desarrollado con ❤️ por Luis Osorio, Luis Torres y Brian Zuleta**
