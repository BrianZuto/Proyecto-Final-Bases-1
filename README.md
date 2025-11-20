# FitTracker - Sistema de Gestión de Entrenamientos

## 📋 Descripción del Proyecto

**FitTracker** es una aplicación web desarrollada como proyecto final de la asignatura **Bases de Datos 1**. Es un sistema integral de gestión de entrenamientos que permite a administradores, entrenadores y deportistas gestionar ejercicios, rutinas, planes de suscripción, sesiones de entrenamiento, seguimiento de progreso, gamificación y contenido educativo.

### Características Principales

- **Gestión de Usuarios**: Sistema de roles (Administrador, Entrenador, Deportista) con control de acceso basado en roles y herencia de tablas
- **Gestión de Ejercicios**: Catálogo completo de ejercicios con categorías, dificultad, imágenes y asignación a planes
- **Gestión de Rutinas**: Creación y edición de rutinas de entrenamiento con ejercicios organizados, cálculo automático de tiempo y calorías
- **Gestión de Planes**: Sistema de suscripciones con precios en pesos colombianos (COP)
- **Sesiones de Entrenamiento**: Sistema completo para crear, gestionar y completar sesiones de entrenamiento
- **Seguimiento de Progreso**: Sistema detallado de seguimiento del progreso de los deportistas en rutinas y ejercicios
- **Gamificación**: Sistema de logros y puntos para motivar a los deportistas
- **Contenido Educativo**: Gestión de artículos, videos, infografías y consejos
- **Dashboard Interactivo**: Panel de control con estadísticas en tiempo real, métricas de entrenamiento y próximos entrenamientos
- **Perfil de Usuario**: Gestión completa del perfil con estadísticas personales, objetivos y plan activo

---

## 👥 Autores

Este proyecto fue desarrollado por:

- **Luis Osorio**
- **Luis Torres**
- **Brian Zuleta**

**Asignatura**: Bases de Datos 1  
**Proyecto**: Final  
**Año**: 2024

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.4.0**
- **Laravel 12.37.0**
- **MySQL/MariaDB** (Base de datos)

### Frontend
- **Tailwind CSS** (via CDN)
- **JavaScript (Vanilla)** para interacciones dinámicas
- **Blade Templates** (Motor de plantillas de Laravel)
- **AJAX** para operaciones asíncronas

### Base de Datos
- **MySQL/MariaDB** como motor de base de datos
- **SQL directo** para todas las consultas (DB::select, DB::insert, DB::update, DB::delete)
- **Sistema de migraciones SQL personalizado** (45 migraciones)
- **Herencia de tablas** para roles (administradores, entrenadores, deportistas)

---

## 📁 Estructura del Proyecto

```
bases1/
├── app/
│   ├── Console/Commands/
│   │   └── RunSqlMigrations.php      # Comando personalizado para migraciones SQL
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   ├── CategoriaController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── ContenidoController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── EjercicioController.php
│   │   │   ├── PlanController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ProgresoController.php
│   │   │   ├── RutinaController.php
│   │   │   ├── SesionController.php
│   │   │   └── TipoRutinaController.php
│   │   └── Middleware/
│   │       └── CheckRole.php         # Middleware para control de roles
│   └── Models/                        # Modelos Eloquent (simplificados)
│
├── database/
│   └── sql_migrations/               # 45 migraciones SQL personalizadas
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
│       ├── 020_insert_tipo_rutinas.sql
│       ├── 021_create_roles_table.sql
│       ├── 022_add_rol_id_to_users.sql
│       ├── 023_create_deportistas_table.sql
│       ├── 024_create_entrenadores_table.sql
│       ├── 025_create_administradores_table.sql
│       ├── 026_add_fk_entrenador_to_deportistas.sql
│       ├── 027_update_rutinas_structure.sql
│       ├── 028_update_detalle_rutinas_structure.sql
│       ├── 029_update_ejercicios_structure.sql
│       ├── 030_update_planes_structure.sql
│       ├── 031_create_progresos_table.sql
│       ├── 032_create_ejercicio_favoritos_table.sql
│       ├── 033_create_recordatorios_table.sql
│       ├── 034_create_gamificaciones_table.sql
│       ├── 035_create_consejos_nutricion_table.sql
│       ├── 036_create_transacciones_table.sql
│       ├── 037_create_deportista_ejercicio_table.sql
│       ├── 038_migrate_users_to_heritage_tables.sql
│       ├── 039_fix_rol_mapping.sql
│       ├── 040_add_estado_to_rutina_usuario_progreso.sql
│       ├── 041_create_ejercicio_usuario_progreso_table.sql
│       ├── 042_create_sesiones_table.sql
│       ├── 043_create_sesion_ejercicios_table.sql
│       ├── 044_create_contenido_table.sql
│       └── 045_insert_datos_prueba.sql
│
├── resources/
│   └── views/
│       ├── auth/                      # Vistas de autenticación
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       ├── clientes/                   # CRUD de clientes
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── ejercicios/                 # CRUD de ejercicios
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── planes/                     # CRUD de planes
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── rutinas/                    # CRUD de rutinas
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   ├── show.blade.php
│       │   └── execute.blade.php
│       ├── sesiones/                   # CRUD de sesiones
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── progreso/                   # Progreso y logros
│       │   ├── index.blade.php
│       │   └── logros.blade.php
│       ├── contenido/                  # Gestión de contenido
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── profile/                    # Perfil de usuario
│       │   └── edit.blade.php
│       ├── layouts/
│       │   └── app.blade.php          # Layout principal con sidebar
│       ├── dashboard.blade.php
│       └── profile.blade.php
│
├── routes/
│   └── web.php                        # Rutas de la aplicación
│
├── config/
│   └── database.php                   # Configuración de base de datos
│
├── README.md                           # Este archivo
├── README_SQL.txt                     # Documentación de consultas SQL por dificultad
└── .env                               # Variables de entorno
```

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales

**Usuarios y Roles:**
- `users` - Usuarios del sistema con información de perfil
- `roles` - Roles del sistema (Administrador, Entrenador, Deportista)
- `administradores` - Tabla de herencia para administradores
- `entrenadores` - Tabla de herencia para entrenadores
- `deportistas` - Tabla de herencia para deportistas

**Planes y Suscripciones:**
- `planes` - Planes de suscripción disponibles
- `plan_usuario` - Relación muchos-a-muchos entre usuarios y planes
- `plan_ejercicio` - Relación entre planes y ejercicios

**Ejercicios y Categorías:**
- `categorias` - Categorías de ejercicios (grupos musculares)
- `ejercicios` - Catálogo completo de ejercicios
- `ejercicio_usuario_progreso` - Progreso individual de ejercicios

**Rutinas:**
- `tipo_rutinas` - Tipos de rutinas (Fuerza, Cardio, Body, etc.)
- `rutinas` - Rutinas de entrenamiento
- `detalle_rutinas` - Relación entre rutinas y ejercicios con detalles (series, repeticiones, peso, etc.)
- `rutina_plan` - Relación entre rutinas y planes
- `rutina_usuario_progreso` - Seguimiento del progreso de usuarios en rutinas

**Sesiones:**
- `sesiones` - Sesiones de entrenamiento completadas
- `sesion_ejercicios` - Ejercicios completados en cada sesión

**Progreso y Gamificación:**
- `progresos` - Registro detallado de progreso en ejercicios de rutinas
- `gamificaciones` - Logros y puntos asignados a deportistas

**Contenido:**
- `contenido` - Artículos, videos, infografías y consejos educativos

**Otras:**
- `ejercicio_favoritos` - Ejercicios favoritos de usuarios
- `recordatorios` - Recordatorios de entrenamiento
- `consejos_nutricion` - Consejos nutricionales
- `transacciones` - Transacciones de planes

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- **PHP 8.4.0** o superior
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

Crea un archivo `.env` con la siguiente configuración:

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

Crea una base de datos MySQL llamada `bases1`:

```sql
CREATE DATABASE bases1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 6. Ejecutar las Migraciones SQL

⚠️ **IMPORTANTE**: Este proyecto utiliza un sistema de migraciones SQL personalizado ubicado en `database/sql_migrations/`. **NO uses** `php artisan migrate` estándar de Laravel.

Para ejecutar las migraciones SQL, usa el siguiente comando:

```bash
php artisan migrate:sql
```

Este comando:
- ✅ Ejecuta todas las migraciones SQL en orden numérico (001, 002, 003, etc.)
- ✅ Crea todas las tablas necesarias
- ✅ Inserta datos iniciales (categorías, tipos de rutinas y datos de prueba)
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

## 👤 Usuarios de Prueba

Después de ejecutar las migraciones SQL (incluyendo `045_insert_datos_prueba.sql`), tendrás usuarios de prueba disponibles:

- **Administradores**: 2 usuarios
- **Entrenadores**: 3 usuarios
- **Deportistas**: 7 usuarios

Los datos de prueba incluyen:
- 5 planes de suscripción
- 18 ejercicios
- 5 rutinas completas
- 5 sesiones de entrenamiento
- 5 artículos de contenido
- 4 logros asignados

---

## 🔐 Sistema de Roles

El sistema cuenta con tres roles principales implementados mediante herencia de tablas:

- **Administrador**: Acceso completo al sistema
  - Gestión de usuarios (CRUD completo)
  - Gestión de planes, ejercicios, rutinas, categorías y tipos de rutinas
  - Gestión de contenido educativo
  - Acceso a todas las funcionalidades del sistema

- **Entrenador**: Acceso para gestionar entrenamientos
  - Ver y gestionar sesiones de entrenamiento
  - Ver progreso de deportistas asignados
  - Acceso a rutinas y ejercicios

- **Deportista**: Acceso básico para entrenar
  - Ver ejercicios y rutinas asignadas a su plan
  - Iniciar y completar rutinas
  - Ver su propio progreso y logros
  - Gestionar su perfil personal
  - Ver contenido educativo

---

## 📝 Funcionalidades Principales

### Dashboard

- **Estadísticas en Tiempo Real**:
  - Rutinas completadas esta semana vs total
  - Calorías quemadas esta semana con comparación a la semana pasada
  - Tiempo total de entrenamiento
  - Peso levantado estimado (en toneladas)
  
- **Progreso Reciente**: Últimos 5 ejercicios completados
  
- **Próximos Entrenamientos**: Próximas 5 rutinas en progreso o pendientes

- **Plan Activo**: Información del plan de suscripción activo

### Gestión de Rutinas

- **CRUD Completo**: Crear, editar, ver y eliminar rutinas
- **Asignación de Ejercicios**: Agregar ejercicios a rutinas con detalles (series, repeticiones, peso, tiempo, descanso)
- **Cálculo Automático**: Tiempo estimado y calorías calculadas automáticamente
- **Estados de Rutina**: Pendiente, En Progreso, Completada
- **Ejecución de Rutinas**: 
  - Iniciar rutina (cambia estado a "En Progreso")
  - Completar ejercicios uno por uno mediante modal
  - Finalizar rutina (cambia estado a "Completada" y crea sesión automáticamente)
- **Progreso Visual**: Porcentaje de completado y estado de cada ejercicio

### Gestión de Ejercicios

- **CRUD Completo**: Crear, editar, ver y eliminar ejercicios
- **Categorización**: Asignación a categorías (grupos musculares)
- **Asignación a Planes**: Ejercicios disponibles según el plan del usuario
- **Progreso Individual**: Seguimiento de veces completado por ejercicio
- **Información Detallada**: Descripción, imagen, dificultad, calorías estimadas

### Sesiones de Entrenamiento

- **CRUD Completo**: Crear, editar, ver y eliminar sesiones
- **Filtros Avanzados**: Por estado, rutina, fecha
- **Estadísticas**: Total de sesiones, sesiones del mes, calorías totales, tiempo total
- **Completar Sesión**: Botón para marcar sesión como completada con cálculo automático de duración
- **Creación Automática**: Las sesiones se crean automáticamente al finalizar una rutina

### Progreso y Logros

- **Estadísticas de Progreso**:
  - Progreso de rutinas (porcentaje completado, estado, sesiones completadas)
  - Progreso de ejercicios (últimos 30 días)
  - Gráfico de sesiones por mes
  - Evolución del peso corporal
  
- **Sistema de Logros**:
  - Logros automáticos basados en logros alcanzados
  - Sistema de puntos
  - Categorización de logros
  - Percentil de logros comparado con otros deportistas
  - Racha de entrenamiento (días consecutivos)

### Contenido Educativo

- **CRUD Completo**: Crear, editar, ver y eliminar contenido
- **Tipos de Contenido**: Artículos, Videos, Infografías, Recursos, Consejos
- **Categorización**: Por categorías temáticas
- **Sistema de Likes**: Los usuarios pueden dar like al contenido
- **Contador de Vistas**: Seguimiento automático de visualizaciones
- **Contenido Relacionado**: Sugerencias de contenido similar
- **Filtros**: Por tipo, categoría, autor y estado de publicación

### Perfil de Usuario

- **Información Personal**: Datos completos del usuario
- **Estadísticas Personales**:
  - Total de sesiones completadas
  - Sesiones del mes actual
  - Total de logros obtenidos
  - Racha de entrenamiento
  - Percentil de logros
- **Objetivos**: Peso objetivo, altura, objetivos de entrenamiento
- **Plan Activo**: Información del plan de suscripción actual
- **Edición de Perfil**: Actualización completa de datos personales

### Gestión de Clientes (Administradores)

- **CRUD Completo**: Crear, editar, ver y eliminar usuarios
- **Gestión de Roles**: Cambiar roles de usuarios
- **Asignación de Planes**: Asignar y gestionar planes de suscripción
- **Filtros**: Por rol, nombre, email
- **Herencia de Tablas**: Gestión automática de tablas de herencia según el rol

### Gestión de Planes

- **CRUD Completo**: Crear, editar, ver y eliminar planes
- **Información Detallada**: Nombre, descripción, precio, duración, características
- **Validación**: No se pueden eliminar planes con usuarios activos

### Gestión de Categorías

- **CRUD Completo**: Crear, editar, ver y eliminar categorías
- **Colores**: Asignación de colores para identificación visual
- **Validación**: No se pueden eliminar categorías con ejercicios asignados

### Gestión de Tipos de Rutinas

- **CRUD Completo**: Crear, editar, ver y eliminar tipos de rutinas
- **Colores**: Asignación de colores para identificación visual
- **Validación**: No se pueden eliminar tipos con rutinas asignadas

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
- ✅ **45 Migraciones**: Sistema completo con todas las tablas y relaciones

### Ubicación de las Migraciones

Todas las migraciones SQL están en: `database/sql_migrations/`

### Comandos Disponibles

```bash
# Ejecutar todas las migraciones SQL pendientes
php artisan migrate:sql

# Ejecutar todas las migraciones desde cero (elimina todas las tablas)
php artisan migrate:sql --fresh

# Ver qué migraciones se ejecutarían sin ejecutarlas
php artisan migrate:sql --pretend
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

# Ver migraciones pendientes sin ejecutarlas
php artisan migrate:sql --pretend
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

### Consultas SQL

- El proyecto utiliza **SQL directo** en lugar de Eloquent ORM para todas las operaciones
- Todas las consultas se realizan mediante `DB::select()`, `DB::insert()`, `DB::update()`, `DB::delete()`
- Se implementó un sistema de paginación manual para mantener consistencia con SQL directo
- **Total de consultas**: ~163 consultas SQL distribuidas en 12 controladores
  - **Fáciles**: ~123 consultas
  - **Medias**: ~39 consultas
  - **Difíciles**: ~1 sistema complejo + 2 Media-Difícil

Para más detalles sobre las consultas SQL, consulta el archivo `README_SQL.txt`.

### Arquitectura

- **Patrón MVC**: Modelo-Vista-Controlador
- **Middleware de Roles**: Control de acceso basado en roles
- **Herencia de Tablas**: Implementación de herencia para roles mediante tablas separadas
- **Sistema de Estados**: Estados para rutinas (pendiente, en_progreso, completada) y sesiones
- **Gamificación Automática**: Sistema que asigna logros automáticamente según logros alcanzados

### Frontend

- **Diseño Responsive**: Interfaz adaptativa usando Tailwind CSS
- **Interacciones Dinámicas**: Modales, formularios AJAX, actualizaciones en tiempo real
- **UX Optimizada**: Indicadores de carga, mensajes de éxito/error, validaciones en tiempo real

---

## 📊 Estadísticas del Proyecto

- **Controladores**: 12 controladores principales
- **Vistas**: 30+ vistas Blade
- **Rutas**: 50+ rutas definidas
- **Tablas de Base de Datos**: 25+ tablas
- **Migraciones SQL**: 45 migraciones
- **Consultas SQL**: ~163 consultas
- **Funcionalidades Principales**: 10+ módulos completos

---

## 📄 Documentación Adicional

- **README_SQL.txt**: Documentación completa de todas las consultas SQL categorizadas por dificultad (Fácil, Media, Difícil)
- **DOCUMENTACION_BD_REPORTES.txt**: Documentación técnica de la base de datos

---

## 📄 Licencia

Este proyecto es parte de un proyecto académico para la asignatura Bases de Datos 1.

---

## 👨‍💻 Soporte

Para cualquier consulta o problema relacionado con el proyecto, contactar a los desarrolladores:
- Luis Osorio
- Luis Torres
- Brian Zuleta

---

**Desarrollado con ❤️ por Luis Osorio, Luis Torres y Brian Zuleta**

**Versión**: 1.0.0  
**Última actualización**: Diciembre 2024
