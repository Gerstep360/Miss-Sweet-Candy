<?php

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
         | PERMISOS (alineados a los 30 CU de Miss Sweet Candy Bar)
         |  Agrupados por módulo para facilitar mantenimiento               |
         -----------------------------------------------------------------*/
        $permissions = [
            // Autenticación / Dashboards
            'ver-dashboard-admin', 'ver-dashboard-cajero', 'ver-dashboard-barista', 'ver-dashboard-cliente',

            // Usuarios y Roles (CU03‑CU04)
            'crear-usuarios', 'ver-usuarios', 'editar-usuarios', 'eliminar-usuarios', 'asignar-roles',
            'crear-roles', 'ver-roles', 'editar-roles', 'eliminar-roles', 'gestionar-permisos',

            // Categorías (CU05)
            'crear-categorias', 'ver-categorias', 'editar-categorias', 'eliminar-categorias',

            // Productos / Menú (CU06, CU30‑stock)
            'crear-productos', 'ver-productos', 'editar-productos', 'eliminar-productos',
            'marcar-producto-agotado', 'actualizar-menu',

            // Horarios (CU07)
            'configurar-horarios', 'ver-horarios',

            // Mesas (CU08)
            'crear-mesas', 'ver-mesas', 'editar-mesas', 'fusionar-mesas', 'cambiar-estado-mesas',

            // Órdenes / Pedidos (CU09‑CU10‑CU13)
            'crear-ordenes', 'editar-ordenes', 'ver-ordenes', 'procesar-ordenes', 'anular-ordenes', 'completar-ordenes',
            'crear-pedidos-online', 'gestionar-pedidos-online',

            // Ventas / Caja (CU11‑CU12)
            'crear-ventas', 'ver-ventas', 'cerrar-caja',

            // Promociones / Descuentos (CU14)
            'configurar-promociones', 'ver-promociones',

            // Inventario (CU15‑CU17‑CU30)
            'ver-inventario', 'editar-inventario', 'registrar-merma', 'ver-alertas-inventario',

            // Recetas (CU16)
            'ver-recetas', 'editar-recetas',

            // Reservas (CU18)
            'crear-reservas', 'ver-reservas', 'editar-reservas', 'cancelar-reservas',

            // Fidelidad (CU19)
            'ver-fidelidad', 'gestionar-fidelidad',

            // Notificaciones internas (CU20)
            'enviar-notificaciones', 'ver-notificaciones',

            // Reportes (CU21)
            'ver-reportes-ventas', 'ver-reportes-inventario', 'ver-reportes-operaciones', 'exportar-reportes',

            // Cumplimiento sanitario (CU22)
            'registrar-sanitario', 'ver-registros-sanitarios',

            // Bitácora / Auditoría (CU23)
            'ver-bitacora',

            // Configuración sistema + Back‑Office (CU24)
            'configurar-sistema', 'gestionar-backoffice',

            // Feedback (CU25)
            'crear-feedback', 'ver-feedback',

            // Pagos QR (CU26)
            'generar-qr', 'procesar-pago-qr',

            // Menú público y perfil cliente (CU27‑CU28‑CU29)
            'ver-menu-publico', 'editar-perfil-cliente', 'ver-historial-pedidos',

            // Especial del Dia
            'crear-especiales', 'ver-especiales', 'editar-especiales', 'eliminar-especiales'
        ];

        /* -----------------------------------------------------------------
         | Registrar permisos únicos                                        |
         -----------------------------------------------------------------*/
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        /* -----------------------------------------------------------------
         | ROLES                                                            |
         -----------------------------------------------------------------*/
        $admin     = Role::firstOrCreate(['name' => 'administrador']);
        $cajero    = Role::firstOrCreate(['name' => 'cajero']);
        $barista   = Role::firstOrCreate(['name' => 'barista']);   // Incluye personal de cocina / barra
        $cliente   = Role::firstOrCreate(['name' => 'cliente']);

        /* -----------------------------------------------------------------
         | Asignar permisos por rol (principio de menor privilegio)         |
         -----------------------------------------------------------------*/
        // 1) Administrador: todos
        $admin->syncPermissions(Permission::all());

        // 2) Cajero
        $cajero->syncPermissions([
            // Ventas / Caja
            'crear-ventas','ver-ventas','cerrar-caja',
            // Órdenes
            'crear-ordenes','editar-ordenes','ver-ordenes','procesar-ordenes','anular-ordenes','completar-ordenes',
            // Mesas
            'ver-mesas','cambiar-estado-mesas','fusionar-mesas',
            // Productos y Categorías (solo vista)
            'ver-productos','ver-categorias','ver-horarios',
            // Inventario (consulta)
            'ver-inventario','ver-alertas-inventario',
            // Promociones (consulta)
            'ver-promociones',
            // Pedidos online
            'gestionar-pedidos-online',
            // Reportes básicos
            'ver-reportes-ventas',
            // Dashboard
            'ver-dashboard-cajero',
            // Pagos QR
            'generar-qr','procesar-pago-qr',
            // Notificaciones
            'ver-notificaciones'
        ]);

        // 3) Barista (Cocina / Barra)
        $barista->syncPermissions([
            'ver-ordenes','procesar-ordenes','completar-ordenes',
            'ver-mesas','cambiar-estado-mesas',
            'ver-productos',
            'ver-inventario','registrar-merma',
            'ver-horarios',
            'ver-notificaciones',
            'ver-dashboard-barista'
        ]);

        // 4) Cliente
        $cliente->syncPermissions([
            'crear-pedidos-online','ver-ordenes','ver-menu-publico',
            'editar-perfil-cliente','ver-historial-pedidos',
            'crear-feedback','ver-fidelidad','ver-dashboard-cliente'
        ]);
    }
}