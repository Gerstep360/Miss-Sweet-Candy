<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
public function run(): void
{
    $this->call([
        RolePermissionSeeder::class,
        UserSeeder::class,
        ClientePerfilSeeder::class, // CU22 - Perfiles de clientes
        HorarioSeeder::class,
        CategoriaSeeder::class,
        ProductoSeeder::class,
        AlergenoSeeder::class, // CU22 - Alérgenos comunes
        ProductoAlergenoSeeder::class, // CU22 - Asignar alérgenos a productos
        MesaSeeder::class,
        PedidoSeeder::class,
        CobroCajaSeeder::class,
        TurnoCajaSeeder::class,
        CierreCajaSeeder::class,
        InventarioProductoSeeder::class,
        EspecialDelDiaSeeder::class,
        NotificacionSeeder::class,
    ]);
}
}
