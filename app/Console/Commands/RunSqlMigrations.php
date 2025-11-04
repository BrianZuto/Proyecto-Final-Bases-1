<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunSqlMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:sql {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta las migraciones SQL desde la carpeta database/sql_migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Verificar que esté usando MySQL
        $driver = DB::connection()->getDriverName();

        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            $this->error("Este comando solo funciona con MySQL/MariaDB. Driver actual: {$driver}");
            $this->info("Por favor, configura DB_CONNECTION=mysql en tu archivo .env");
            return 1;
        }

        $migrationsPath = database_path('sql_migrations');

        if (!File::exists($migrationsPath)) {
            $this->error("La carpeta {$migrationsPath} no existe.");
            return 1;
        }

        // Crear tabla de seguimiento de migraciones SQL si no existe
        $this->createMigrationsTable();

        // Obtener todas las migraciones SQL
        $migrationFiles = File::glob($migrationsPath . '/*.sql');
        sort($migrationFiles);

        if (empty($migrationFiles)) {
            $this->info('No se encontraron migraciones SQL.');
            return 0;
        }

        if ($this->option('fresh')) {
            if ($this->confirm('¿Estás seguro de que quieres eliminar todas las tablas y ejecutar todas las migraciones?', false)) {
                $this->call('migrate:fresh');
                DB::table('sql_migrations')->truncate();
                $this->info('Base de datos limpiada. Ejecutando todas las migraciones SQL...');
            } else {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $executed = 0;
        $skipped = 0;

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = basename($migrationFile);

            // Verificar si ya se ejecutó
            $alreadyExecuted = DB::table('sql_migrations')
                ->where('migration', $migrationName)
                ->exists();

            if ($alreadyExecuted && !$this->option('fresh')) {
                $this->line("⏭  Saltando: {$migrationName} (ya ejecutada)");
                $skipped++;
                continue;
            }

            try {
                $this->info("▶  Ejecutando: {$migrationName}");

                // Leer el contenido del archivo SQL
                $sql = File::get($migrationFile);

                // Ejecutar las consultas SQL
                DB::unprepared($sql);

                // Registrar la migración como ejecutada
                if (!$alreadyExecuted || $this->option('fresh')) {
                    DB::table('sql_migrations')->insert([
                        'migration' => $migrationName,
                        'batch' => DB::table('sql_migrations')->max('batch') + 1,
                        'executed_at' => now(),
                    ]);
                }

                $this->info("✓  Completada: {$migrationName}");
                $executed++;

            } catch (\Exception $e) {
                $this->error("✗  Error en {$migrationName}: " . $e->getMessage());
                return 1;
            }
        }

        $this->newLine();
        $this->info("Migraciones ejecutadas: {$executed}");
        if ($skipped > 0) {
            $this->info("Migraciones omitidas: {$skipped}");
        }

        return 0;
    }

    /**
     * Crear la tabla de seguimiento de migraciones SQL
     */
    protected function createMigrationsTable()
    {
        if (!DB::getSchemaBuilder()->hasTable('sql_migrations')) {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql' || $driver === 'mariadb') {
                // Sintaxis MySQL/MariaDB
                DB::statement("
                    CREATE TABLE sql_migrations (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        migration VARCHAR(255) NOT NULL,
                        batch INT NOT NULL,
                        executed_at TIMESTAMP NULL DEFAULT NULL,
                        INDEX idx_migration (migration),
                        INDEX idx_batch (batch)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            } else {
                // Sintaxis genérica SQL (para otros drivers)
                DB::statement("
                    CREATE TABLE sql_migrations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        migration VARCHAR(255) NOT NULL,
                        batch INTEGER NOT NULL,
                        executed_at TIMESTAMP NULL DEFAULT NULL
                    )
                ");

                // Crear índices separados para SQLite
                try {
                    DB::statement("CREATE INDEX idx_migration ON sql_migrations (migration)");
                    DB::statement("CREATE INDEX idx_batch ON sql_migrations (batch)");
                } catch (\Exception $e) {
                    // Los índices pueden ya existir, ignorar
                }
            }
        }
    }
}

