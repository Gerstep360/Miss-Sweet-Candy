<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AsignarPermisoBarista extends Command
{
    protected $signature = 'barista:asignar-permiso';
    protected $description = 'Asigna el permiso gestionar-pedidos-barista al rol barista';

    public function handle()
    {
        $this->info('Verificando permiso gestionar-pedidos-barista...');
        
        // Crear o verificar el permiso
        $permission = Permission::firstOrCreate(['name' => 'gestionar-pedidos-barista']);
        $this->info('✓ Permiso existe: ' . $permission->name);
        
        // Buscar el rol barista
        $barista = Role::where('name', 'barista')->first();
        
        if (!$barista) {
            $this->error('✗ Rol barista no encontrado. Primero ejecuta el seeder de roles.');
            return 1;
        }
        
        $this->info('✓ Rol barista encontrado');
        
        // Asignar el permiso
        if (!$barista->hasPermissionTo('gestionar-pedidos-barista')) {
            $barista->givePermissionTo('gestionar-pedidos-barista');
            $this->info('✓ Permiso asignado al rol barista');
        } else {
            $this->info('✓ El rol barista ya tiene el permiso');
        }
        
        // Verificar usuarios con rol barista
        $usuarios = \App\Models\User::role('barista')->get();
        $this->info('');
        $this->info('Usuarios con rol barista: ' . $usuarios->count());
        
        foreach ($usuarios as $usuario) {
            $this->line('  - ' . $usuario->name . ' (' . $usuario->email . ')');
        }
        
        if ($usuarios->isEmpty()) {
            $this->warn('');
            $this->warn('⚠ No hay usuarios con rol barista. Asigna el rol desde la interfaz de usuarios.');
        }
        
        $this->info('');
        $this->info('✓ Proceso completado');
        
        return 0;
    }
}
