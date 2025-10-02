<?php
// filepath: c:\Users\German\Documents\Proyectos\PHP\Cafeteria\cafeteria\database\seeders\RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /* -----------------------------------------------------------------
         | PERMISOS ALINEADOS A LOS 12 CU ESPECIFICADOS
         | Simplificados y enfocados en los casos de uso actuales
         -----------------------------------------------------------------*/
        $permissions = [
            // CU01 - Iniciar sesión / Registrar (gestionado por Laravel Auth)
            // No requiere permisos específicos
            
            // CU02 - Cerrar sesión (gestionado por Laravel Auth)
            // No requiere permisos específicos
            //Bitacora
            'ver-bitacora',
            'ver-detalle-bitacora',
            //Barista
            'gestionar-pedidos-barista',
            // CU03 - Gestión de Usuarios (ADMIN)
            'crear-usuarios',
            'ver-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',
            'activar-usuarios',

            // CU04 - Gestión de Roles y Permisos (ADMIN)
            'crear-roles',
            'ver-roles',
            'editar-roles',
            'eliminar-roles',
            'asignar-roles',
            'gestionar-permisos',

            // CU05 - Gestión de Categorías (ADMIN)
            'crear-categorias',
            'ver-categorias',
            'editar-categorias',
            'eliminar-categorias',

            // CU06 - Gestión de Productos/Menú (ADMIN, CAJERO)
            'crear-productos',
            'ver-productos',
            'editar-productos',
            'eliminar-productos',

            // CU07 - Gestión de Horarios (ADMIN)
            'configurar-horarios',
            'ver-horarios',

            // CU08 - Gestión de Mesas (CAJERO)
            'crear-mesas',
            'ver-mesas',
            'editar-mesas',
            'eliminar-mesas',
            'fusionar-mesas',
            'cambiar-estado-mesas',

            // CU09 - Tomar Pedido en Mesa (CAJERO)
            'crear-pedidos-mesa',
            'editar-pedidos-mesa',
            'ver-pedidos-mesa',
            'anular-pedidos-mesa',

            // CU10 - Pedido Para Llevar/Mostrador (CAJERO)
            'crear-pedidos-mostrador',
            'editar-pedidos-mostrador',
            'ver-pedidos-mostrador',
            'anular-pedidos-mostrador',

            // CU11 - Cobro en Caja (CAJERO)
            'crear-cobros',
            'ver-cobros',
            'cancelar-cobros',
            'confirmar-pago-qr',
            'ver-reporte-caja',

            // CU12 - Consulta de Menú Público (CLIENTE, TODOS)
            'ver-menu-publico',

            // Dashboards
            'ver-dashboard-admin',
            'ver-dashboard-cajero',
            'ver-dashboard-cliente',

            // Permisos generales
            'ver-pedidos',
            'cambiar-estado-pedidos',
        ];

        /* -----------------------------------------------------------------
         | Registrar permisos únicos
         -----------------------------------------------------------------*/
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        /* -----------------------------------------------------------------
         | ROLES
         -----------------------------------------------------------------*/
        $admin   = Role::firstOrCreate(['name' => 'administrador']);
        $cajero  = Role::firstOrCreate(['name' => 'cajero']);
        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        $barista = Role::firstOrCreate(['name' => 'barista']);
        /* -----------------------------------------------------------------
         | Asignar permisos por rol
         -----------------------------------------------------------------*/
        
        // 1) ADMINISTRADOR - Todos los permisos
        $admin->syncPermissions(Permission::all());

        // 2) CAJERO
        $cajero->syncPermissions([
            // CU06 - Productos (solo ver y editar, no eliminar)
            'ver-productos',
            'editar-productos',
            
            // CU07 - Horarios (solo ver)
            'ver-horarios',
            
            // CU08 - Mesas
            'crear-mesas',
            'ver-mesas',
            'editar-mesas',
            'fusionar-mesas',
            'cambiar-estado-mesas',
            
            // CU09 - Pedidos Mesa
            'crear-pedidos-mesa',
            'editar-pedidos-mesa',
            'ver-pedidos-mesa',
            'anular-pedidos-mesa',
            
            // CU10 - Pedidos Mostrador
            'crear-pedidos-mostrador',
            'editar-pedidos-mostrador',
            'ver-pedidos-mostrador',
            'anular-pedidos-mostrador',
            
            // CU11 - Cobros
            'crear-cobros',
            'ver-cobros',
            'cancelar-cobros',
            'confirmar-pago-qr',
            'ver-reporte-caja',
            
            // CU12 - Menú Público
            'ver-menu-publico',
            
            // Generales
            'ver-pedidos',
            'cambiar-estado-pedidos',
            'ver-dashboard-cajero',
            'ver-categorias',
        ]);

        // 3) CLIENTE
        $cliente->syncPermissions([
            // CU12 - Menú Público
            'ver-menu-publico',
            
            // Dashboard
            'ver-dashboard-cliente',
            
            // Ver sus propios pedidos
            'ver-pedidos',
        ]);

        // 4) BARISTA (por el momento no tiene muchos permisos, pero se deja para los proximos casos de uso)
        $barista->syncPermissions([
            'ver-productos',
            'gestionar-pedidos-barista',
        ]);
    }
}