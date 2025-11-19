<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:user-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra los datos de usuarios existentes a las nuevas tablas de herencia (deportistas, entrenadores, administradores)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de datos de usuarios...');
        
        try {
            DB::beginTransaction();

            // 1. Verificar que existan los roles
            $roles = DB::table('roles')->pluck('nombre', 'id')->toArray();
            if (empty($roles)) {
                $this->error('No se encontraron roles en la tabla roles. Ejecuta primero las migraciones SQL.');
                return 1;
            }

            // 2. Mapear usuarios a roles si no tienen rol_id
            $usersSinRolId = DB::table('users')
                ->whereNull('rol_id')
                ->get();

            if ($usersSinRolId->count() > 0) {
                $this->info("Se encontraron {$usersSinRolId->count()} usuarios sin rol_id. Intentando mapear...");
                
                foreach ($usersSinRolId as $user) {
                    // Si el campo 'rol' todavía existe, usarlo
                    if (isset($user->rol)) {
                        $rolNombre = $user->rol === 'Coach' ? 'Entrenador' : $user->rol;
                        $rolId = array_search($rolNombre, $roles);
                        
                        if ($rolId) {
                            DB::table('users')
                                ->where('id', $user->id)
                                ->update(['rol_id' => $rolId]);
                            $this->line("  ✓ Usuario {$user->id} mapeado a rol: {$rolNombre}");
                        }
                    }
                }
            }

            // 3. Migrar usuarios a tabla deportistas
            $deportistasCount = DB::table('users')
                ->join('roles', 'users.rol_id', '=', 'roles.id')
                ->where('roles.nombre', 'Deportista')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('deportistas')
                          ->whereColumn('deportistas.user_id', 'users.id');
                })
                ->count();

            if ($deportistasCount > 0) {
                DB::table('deportistas')
                    ->insertUsing(
                        ['user_id', 'created_at', 'updated_at'],
                        DB::table('users')
                            ->join('roles', 'users.rol_id', '=', 'roles.id')
                            ->where('roles.nombre', 'Deportista')
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('deportistas')
                                      ->whereColumn('deportistas.user_id', 'users.id');
                            })
                            ->select('users.id as user_id', 'users.created_at', 'users.updated_at')
                    );
                $this->info("  ✓ {$deportistasCount} usuarios migrados a tabla deportistas");
            }

            // 4. Migrar usuarios a tabla entrenadores
            $entrenadoresCount = DB::table('users')
                ->join('roles', 'users.rol_id', '=', 'roles.id')
                ->whereIn('roles.nombre', ['Entrenador', 'Coach'])
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('entrenadores')
                          ->whereColumn('entrenadores.user_id', 'users.id');
                })
                ->count();

            if ($entrenadoresCount > 0) {
                DB::table('entrenadores')
                    ->insertUsing(
                        ['user_id', 'created_at', 'updated_at'],
                        DB::table('users')
                            ->join('roles', 'users.rol_id', '=', 'roles.id')
                            ->whereIn('roles.nombre', ['Entrenador', 'Coach'])
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('entrenadores')
                                      ->whereColumn('entrenadores.user_id', 'users.id');
                            })
                            ->select('users.id as user_id', 'users.created_at', 'users.updated_at')
                    );
                $this->info("  ✓ {$entrenadoresCount} usuarios migrados a tabla entrenadores");
            }

            // 5. Migrar usuarios a tabla administradores
            $administradoresCount = DB::table('users')
                ->join('roles', 'users.rol_id', '=', 'roles.id')
                ->where('roles.nombre', 'Administrador')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                          ->from('administradores')
                          ->whereColumn('administradores.user_id', 'users.id');
                })
                ->count();

            if ($administradoresCount > 0) {
                DB::table('administradores')
                    ->insertUsing(
                        ['user_id', 'created_at', 'updated_at'],
                        DB::table('users')
                            ->join('roles', 'users.rol_id', '=', 'roles.id')
                            ->where('roles.nombre', 'Administrador')
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('administradores')
                                      ->whereColumn('administradores.user_id', 'users.id');
                            })
                            ->select('users.id as user_id', 'users.created_at', 'users.updated_at')
                    );
                $this->info("  ✓ {$administradoresCount} usuarios migrados a tabla administradores");
            }

            DB::commit();
            $this->info('✓ Migración de datos de usuarios completada exitosamente.');
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('✗ Error durante la migración: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

